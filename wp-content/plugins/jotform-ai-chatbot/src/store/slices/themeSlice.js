import {
  AUTO_OPEN_CHAT_VALUES, CUSTOMIZATION_KEYS, GREETING_MESSAGE,
  POSITION, THEME_CUSTOMIZATION_KEYS, VERBAL_TOGGLE, VISIBILITY_LAYOUT
} from '../../constants';
import { getGreetingMessege } from '../../utils';
import { generatePromiseActionType } from '../actionTypes';
import { GET_PLATFORM_AGENT, USE_PLATFORM_AGENT } from './commonActions';

// Internal action types (only used within this slice)
const UPDATE_CUSTOMIZATION = 'UPDATE_CUSTOMIZATION';
const UPDATE_THEME = generatePromiseActionType('UPDATE_THEME');
const UPDATE_THEME_PROPERTY = generatePromiseActionType('UPDATE_THEME_PROPERTY');

// Initial state for theme and customizations domain
export const themeInitialState = {
  themeName: null,
  customizations: {
    [CUSTOMIZATION_KEYS.GREETING]: VERBAL_TOGGLE.YES,
    [CUSTOMIZATION_KEYS.GREETING_MESSAGE]: GREETING_MESSAGE.en,
    [CUSTOMIZATION_KEYS.PULSE]: VERBAL_TOGGLE.YES,
    [CUSTOMIZATION_KEYS.POSITION]: POSITION.RIGHT,
    [CUSTOMIZATION_KEYS.AUTO_OPEN_CHAT]: AUTO_OPEN_CHAT_VALUES.NEVER,
    [CUSTOMIZATION_KEYS.LAYOUT]: VISIBILITY_LAYOUT.MINIMAL
  },
  themeCustomizations: {
    [THEME_CUSTOMIZATION_KEYS.AGENT_BG_START_COLOR]: '',
    [THEME_CUSTOMIZATION_KEYS.AGENT_BG_END_COLOR]: '',
    [THEME_CUSTOMIZATION_KEYS.CHAT_BG_COLOR]: '',
    [THEME_CUSTOMIZATION_KEYS.FONT_FAMILY]: '',
    [THEME_CUSTOMIZATION_KEYS.FONT_COLOR]: '',
    [THEME_CUSTOMIZATION_KEYS.BUTTON_BG_COLOR]: '',
    [THEME_CUSTOMIZATION_KEYS.BUTTON_ICON_BG_COLOR]: ''
  }
};

// Theme slice reducer
export const themeReducer = (state, action) => {
  switch (action.type) {
    case UPDATE_CUSTOMIZATION:
      return {
        ...state,
        customizations: {
          ...state.customizations,
          [action.payload.key]: action.payload.value
        }
      };

    case UPDATE_THEME.REQUEST:
    case UPDATE_THEME.ERROR:
      return state;

    case UPDATE_THEME.SUCCESS:
      const newCustomizations = action.payload.result.reduce((currentCust, { props: { prop, value } = {} }) => {
        if (!prop) return currentCust;
        if (['activeTheme', 'inputBackground', 'pageBackgroundEnd', 'pageBackgroundStart'].includes(prop)) {
          return currentCust;
        }
        return { ...currentCust, [prop]: value };
      }, {});

      return {
        ...state,
        themeName: action.payload.themeName,
        themeCustomizations: { ...state.themeCustomizations, ...newCustomizations }
      };

    case UPDATE_THEME_PROPERTY.REQUEST:
    case UPDATE_THEME_PROPERTY.ERROR:
      return state;

    case UPDATE_THEME_PROPERTY.SUCCESS:
      return {
        ...state,
        themeCustomizations: {
          ...state.themeCustomizations,
          [action.payload.result.props?.prop]: action.payload.result.props?.value
        }
      };

    case USE_PLATFORM_AGENT.SUCCESS:
    case GET_PLATFORM_AGENT.SUCCESS:
      const {
        result: {
          content = undefined,
          agentProperties = {}
        }
      } = action.payload;

      if (content === false) {
        return state;
      }

      return {
        ...state,
        themeName: agentProperties.activeTheme,
        themeCustomizations: {
          [THEME_CUSTOMIZATION_KEYS.AGENT_BG_START_COLOR]: agentProperties[THEME_CUSTOMIZATION_KEYS.AGENT_BG_START_COLOR],
          [THEME_CUSTOMIZATION_KEYS.AGENT_BG_END_COLOR]: agentProperties[THEME_CUSTOMIZATION_KEYS.AGENT_BG_END_COLOR],
          [THEME_CUSTOMIZATION_KEYS.CHAT_BG_COLOR]: agentProperties[THEME_CUSTOMIZATION_KEYS.CHAT_BG_COLOR],
          [THEME_CUSTOMIZATION_KEYS.FONT_FAMILY]: agentProperties[THEME_CUSTOMIZATION_KEYS.FONT_FAMILY],
          [THEME_CUSTOMIZATION_KEYS.FONT_COLOR]: agentProperties[THEME_CUSTOMIZATION_KEYS.FONT_COLOR],
          [THEME_CUSTOMIZATION_KEYS.BUTTON_BG_COLOR]: agentProperties[THEME_CUSTOMIZATION_KEYS.BUTTON_BG_COLOR],
          [THEME_CUSTOMIZATION_KEYS.BUTTON_ICON_BG_COLOR]: agentProperties[THEME_CUSTOMIZATION_KEYS.BUTTON_ICON_BG_COLOR]
        },
        customizations: {
          ...state.customizations,
          ...agentProperties.popover,
          ...getGreetingMessege(agentProperties.language, agentProperties.popover?.greetingMessage)
        }
      };

    default:
      return state;
  }
};

// Theme action creators
export const themeActionCreators = {
  updateCustomization: (key, value) => ({
    type: UPDATE_CUSTOMIZATION,
    payload: { key, value }
  }),

  updateThemeRequest: () => ({
    type: UPDATE_THEME.REQUEST
  }),

  updateThemeSuccess: (result, themeName) => ({
    type: UPDATE_THEME.SUCCESS,
    payload: { result, themeName }
  }),

  updateThemeError: () => ({
    type: UPDATE_THEME.ERROR
  }),

  updateThemePropertyRequest: () => ({
    type: UPDATE_THEME_PROPERTY.REQUEST
  }),

  updateThemePropertySuccess: result => ({
    type: UPDATE_THEME_PROPERTY.SUCCESS,
    payload: { result }
  }),

  updateThemePropertyError: () => ({
    type: UPDATE_THEME_PROPERTY.ERROR
  })
};
