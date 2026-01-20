import { generatePromiseActionType } from '../actionTypes';
import {
  DELETE_PLATFORM_AGENT, GET_PLATFORM_AGENT, LOGOUT_FROM_JOTFORM, USE_PLATFORM_AGENT
} from './commonActions';

// Internal action types (only used within this slice)
const SET_AGENT_NAME = 'SET_AGENT_NAME';
const SET_AGENT_ROLE = 'SET_AGENT_ROLE';
const SET_TONE_OF_VOICE = 'SET_TONE_OF_VOICE';
const SET_AGENT_CHATTINESS = 'SET_AGENT_CHATTINESS';
const SET_LANGUAGE = 'SET_LANGUAGE';
const SET_PERSONA = 'SET_PERSONA';

const UPDATE_AGENT = generatePromiseActionType('UPDATE_AGENT');
const UPDATE_AGENT_PROPERTY = generatePromiseActionType('UPDATE_AGENT_PROPERTY');

// Initial state for agent domain
export const agentInitialState = {
  agentName: '',
  agentRole: '',
  agentChattiness: '1',
  agentLanguage: 'en',
  agentToneOfVoice: '',
  persona: '',
  previewAgentId: null,
  activeViewId: null,
  refreshPreviewForAvatar: false,
  isAgentPropertyLoading: false
};

// Agent slice reducer
export const agentReducer = (state, action) => {
  switch (action.type) {
    case SET_AGENT_NAME:
      return { ...state, agentName: action.payload.agentName };

    case SET_AGENT_ROLE:
      return { ...state, agentRole: action.payload.agentRole };

    case SET_AGENT_CHATTINESS:
      return { ...state, agentChattiness: action.payload.agentChattiness };

    case SET_LANGUAGE:
      return { ...state, agentLanguage: action.payload.language };

    case SET_TONE_OF_VOICE:
      return { ...state, agentToneOfVoice: action.payload.toneOfVoice };

    case SET_PERSONA:
      return { ...state, persona: action.payload.persona };

    case USE_PLATFORM_AGENT.SUCCESS:
    case GET_PLATFORM_AGENT.SUCCESS:
      const {
        result: {
          content = undefined,
          agentID = '',
          agentProperties = {},
          agentName,
          activeViewId = ''
        }
      } = action.payload;

      if (content === false) {
        return state; // No agent data to update
      }

      return {
        ...state,
        previewAgentId: agentID,
        activeViewId,
        agentName,
        agentRole: agentProperties.role,
        agentChattiness: agentProperties.chattiness,
        agentLanguage: agentProperties.language,
        agentToneOfVoice: agentProperties.tone,
        persona: agentProperties.persona
      };

    case UPDATE_AGENT_PROPERTY.REQUEST:
      return { ...state, isAgentPropertyLoading: true };

    case UPDATE_AGENT_PROPERTY.SUCCESS:
      return {
        ...state,
        refreshPreviewForAvatar: action.payload.additionalPayload?.isAvatar ? !state.refreshPreviewForAvatar : state.refreshPreviewForAvatar,
        agentName: action.payload.additionalPayload?.isAvatar && action.payload.additionalPayload?.agentName ? action.payload.additionalPayload.agentName : state.agentName,
        isAgentPropertyLoading: false
      };

    case UPDATE_AGENT_PROPERTY.ERROR:
      return { ...state, isAgentPropertyLoading: false };

    case UPDATE_AGENT.REQUEST:
    case UPDATE_AGENT.SUCCESS:
    case UPDATE_AGENT.ERROR:
      // These don't modify state but are handled for completeness
      return state;

    case DELETE_PLATFORM_AGENT.SUCCESS:
    case LOGOUT_FROM_JOTFORM.SUCCESS:
      return {
        ...state,
        ...agentInitialState
      };

    default:
      return state;
  }
};

// Agent action creators
export const agentActionCreators = {
  setAgentName: agentName => ({
    type: SET_AGENT_NAME,
    payload: { agentName }
  }),

  setAgentRole: agentRole => ({
    type: SET_AGENT_ROLE,
    payload: { agentRole }
  }),

  setAgentChattiness: agentChattiness => ({
    type: SET_AGENT_CHATTINESS,
    payload: { agentChattiness }
  }),

  setAgentLanguage: language => ({
    type: SET_LANGUAGE,
    payload: { language }
  }),

  setAgentToneOfVoice: toneOfVoice => ({
    type: SET_TONE_OF_VOICE,
    payload: { toneOfVoice }
  }),

  setPersona: persona => ({
    type: SET_PERSONA,
    payload: { persona }
  }),

  updateAgentRequest: () => ({
    type: UPDATE_AGENT.REQUEST
  }),

  updateAgentSuccess: result => ({
    type: UPDATE_AGENT.SUCCESS,
    payload: { result }
  }),

  updateAgentError: result => ({
    type: UPDATE_AGENT.ERROR,
    payload: { result }
  }),

  updateAgentPropertyRequest: () => ({
    type: UPDATE_AGENT_PROPERTY.REQUEST
  }),

  updateAgentPropertySuccess: (result, additionalPayload) => ({
    type: UPDATE_AGENT_PROPERTY.SUCCESS,
    payload: { result, additionalPayload }
  }),

  updateAgentPropertyError: result => ({
    type: UPDATE_AGENT_PROPERTY.ERROR,
    payload: { result }
  })
};
