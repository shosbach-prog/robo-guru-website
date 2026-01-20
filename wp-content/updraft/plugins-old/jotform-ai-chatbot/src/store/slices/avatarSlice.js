import { getAvatarIdFromUrl, isValidJotformUrl, normalizeAvatarProps } from '../../utils';
import { generatePromiseActionType } from '../actionTypes';
import { GET_PLATFORM_AGENT, USE_PLATFORM_AGENT } from './commonActions';

// Internal action types (only used within this slice)
const GET_AVATARS = generatePromiseActionType('GET_AVATARS');
const GET_ALL_AGENTS = generatePromiseActionType('GET_ALL_AGENTS');
const SET_AVATARS = 'SET_AVATARS';
const RESET_AVATARS = 'RESET_AVATARS';

// Default avatar object
const defaultSelectedAvatar = {
  id: 0, avatarName: '', avatarIconLink: '', avatarLink: '', avatarType: '', propmt: '', customAvatar: false
};

// Initial state for avatar domain
export const avatarInitialState = {
  selectedAvatar: defaultSelectedAvatar,
  avatars: [],
  avatarsOffset: 1,
  areAvatarsLoading: false,
  allAvatarsFetched: false,
  allAgents: {
    loading: false,
    items: []
  }
};

// Avatar slice reducer
export const avatarReducer = (state, action) => {
  switch (action.type) {
    case GET_AVATARS.REQUEST:
      return { ...state, areAvatarsLoading: true };

    case GET_AVATARS.SUCCESS:
      const fetchedAvatars = normalizeAvatarProps(action.payload.result?.avatars);
      const avatarsWithoutSelected = fetchedAvatars.filter(avatar => getAvatarIdFromUrl(avatar.avatarIconLink) !== getAvatarIdFromUrl(state.selectedAvatar.avatarIconLink));
      const validatedAvatars = avatarsWithoutSelected.filter(avtr => isValidJotformUrl(avtr.avatarIconLink) && avtr.avatarName !== 'Avatar test');

      return {
        ...state,
        areAvatarsLoading: false,
        avatars: [...state.avatars, ...validatedAvatars],
        avatarsOffset: action.payload.result?.nextPageOffset,
        allAvatarsFetched: action.payload.result?.avatars?.length === 0
      };

    case GET_AVATARS.ERROR:
      return {
        ...state,
        areAvatarsLoading: false
      };

    case SET_AVATARS:
      return {
        ...state,
        avatars: [...action.payload.avatars],
        selectedAvatar: action.payload.selectedAvatar
      };

    case RESET_AVATARS:
      return {
        ...state,
        avatars: [],
        selectedAvatar: defaultSelectedAvatar,
        avatarsOffset: 1
      };

    case USE_PLATFORM_AGENT.SUCCESS:
    case GET_PLATFORM_AGENT.SUCCESS:
      const {
        result: {
          content = undefined,
          agentProperties = { avatarLink: '' },
          agentName
        }
      } = action.payload;

      if (content === false) {
        return state;
      }

      const currentAvatar = {
        avatarName: agentName,
        avatarType: agentProperties.gender,
        avatarLink: agentProperties.avatarLink,
        prompt: agentProperties.avatarPrompt,
        avatarIconLink: agentProperties.avatarIconLink,
        id: getAvatarIdFromUrl(agentProperties.avatarIconLink),
        customAvatar: agentProperties.avatarLink?.includes('avatar_images') ?? false
      };

      return {
        ...state,
        selectedAvatar: { ...currentAvatar },
        avatars: [
          { ...currentAvatar },
          ...state.avatars.filter(avt => avt.id !== state.selectedAvatar.id)
        ]
      };

    case GET_ALL_AGENTS.REQUEST:
      return {
        ...state,
        allAgents: {
          loading: true,
          items: []
        }
      };

    case GET_ALL_AGENTS.SUCCESS:
    case GET_ALL_AGENTS.ERROR:
      return {
        ...state,
        allAgents: {
          loading: false,
          items: action?.payload?.agents || []
        }
      };

    default:
      return state;
  }
};

// Avatar action creators
export const avatarActionCreators = {
  getAvatarsRequest: () => ({
    type: GET_AVATARS.REQUEST
  }),

  getAvatarsSuccess: result => ({
    type: GET_AVATARS.SUCCESS,
    payload: { result }
  }),

  getAvatarsError: result => ({
    type: GET_AVATARS.ERROR,
    payload: { result }
  }),

  setAvatars: (avatars, selectedAvatar) => ({
    type: SET_AVATARS,
    payload: { avatars, selectedAvatar }
  }),

  resetAvatars: () => ({
    type: RESET_AVATARS
  }),

  getAllAgentsRequest: () => ({
    type: GET_ALL_AGENTS.REQUEST
  }),

  getAllAgentsSuccess: agents => ({
    type: GET_ALL_AGENTS.SUCCESS,
    payload: { agents }
  }),

  getAllAgentsError: () => ({
    type: GET_ALL_AGENTS.ERROR
  })
};
