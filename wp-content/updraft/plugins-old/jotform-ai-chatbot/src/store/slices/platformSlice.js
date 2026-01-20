import { reinitializeRequestLayer } from '../../api';
import { DEVICES, VISIBILITY_TOGGLE } from '../../constants';
import { platformSettings as platformSettingsSingleton, removeStepFromQueryParams } from '../../utils';
import { generatePromiseActionType } from '../actionTypes';
import {
  DELETE_PLATFORM_AGENT, GET_PLATFORM_AGENT, LOGOUT_FROM_JOTFORM, USE_PLATFORM_AGENT
} from './commonActions';

// Internal action types (only used within this slice)
const GET_PLATFORM_SETTINGS = generatePromiseActionType('GET_PLATFORM_SETTINGS');
const PUBLISH_AGENT = generatePromiseActionType('PUBLISH_AGENT');
const SAVE_PROVIDER_API_KEY = generatePromiseActionType('SAVE_PROVIDER_API_KEY');
const SAVE_PLATFORM_AGENT_PAGES = generatePromiseActionType('SAVE_PLATFORM_AGENT_PAGES');

const SET_IS_PUBLISHED = 'SET_IS_PUBLISHED';
const SET_PLATFORM_SETTINGS = 'SET_PLATFORM_SETTINGS';
const SET_SELECTED_PAGES = 'SET_SELECTED_PAGES';
const SET_VISIBLE_DEVICE = 'SET_VISIBLE_DEVICE';
const UNAUTHORIZED_API_KEY = 'UNAUTHORIZED_API_KEY';

// Default selected pages
const defaultSelectedPages = {
  showOn: [],
  hideOn: [],
  active: VISIBILITY_TOGGLE.SHOW_ON.value
};

// Initial state for platform domain
export const platformInitialState = {
  isPlatformSettingsLoading: true,
  isInitialPlatformSettingsReady: false,
  isPublishLoading: false,
  isDeletePlatformAgentLoading: false,
  isSavePlatformAgentPagesLoading: false,
  selectedPages: defaultSelectedPages,
  platformSettings: { ...platformSettingsSingleton },
  visibleDevice: DEVICES[0].value,
  isPublished: false,
  errorMessage: '',
  isLogoutLoading: false,
  isUnauthorizedApiKey: false // form users
};

// Platform slice reducer
export const platformReducer = (state, action) => {
  switch (action.type) {
    case GET_PLATFORM_SETTINGS.REQUEST:
      return { ...state, isPlatformSettingsLoading: true };

    case GET_PLATFORM_SETTINGS.SUCCESS:
      const data = action.payload.result?.data;
      // Update singleton settings
      platformSettingsSingleton.PROVIDER_API_KEY = data.PROVIDER_API_KEY;
      platformSettingsSingleton.PROVIDER_API_URL = data.PROVIDER_API_URL;
      platformSettingsSingleton.PROVIDER_URL = data.PROVIDER_URL;
      platformSettingsSingleton.PLATFORM = data.PLATFORM;
      platformSettingsSingleton.PLATFORM_URL = data.PLATFORM_URL;
      platformSettingsSingleton.PLATFORM_DOMAIN = data.PLATFORM_DOMAIN;
      platformSettingsSingleton.PLATFORM_PAGES = data.PLATFORM_PAGES;
      platformSettingsSingleton.PLATFORM_CHATBOT_PAGES = data.PLATFORM_CHATBOT_PAGES;
      platformSettingsSingleton.PLATFORM_PAGE_CONTENTS = data.PLATFORM_PAGE_CONTENTS;
      platformSettingsSingleton.PLATFORM_PREVIEW_URL = data.PLATFORM_PREVIEW_URL;
      platformSettingsSingleton.PLATFORM_KNOWLEDGE_BASE = data.PLATFORM_KNOWLEDGE_BASE;
      platformSettingsSingleton.PLATFORM_DEVICE = data.PLATFORM_DEVICE;
      platformSettingsSingleton.PLATFORM_CHATBOT_PUBLISHED = data.PLATFORM_CHATBOT_PUBLISHED;
      platformSettingsSingleton.PLATFORM_PLUGIN_VERSION = data.PLATFORM_PLUGIN_VERSION;
      platformSettingsSingleton.PLATFORM_WOOCOMMERCE_AVAILABLE = data.PLATFORM_WOOCOMMERCE_AVAILABLE;
      platformSettingsSingleton.PLATFORM_PERMALINK_STRUCTURE = data.PLATFORM_PERMALINK_STRUCTURE;
      reinitializeRequestLayer();

      return {
        ...state,
        platformSettings: { ...state.platformSettings, ...data },
        isPlatformSettingsLoading: false,
        visibleDevice: data.PLATFORM_DEVICE,
        selectedPages: data.PLATFORM_CHATBOT_PAGES,
        isPublished: data.PLATFORM_CHATBOT_PUBLISHED
      };

    case GET_PLATFORM_SETTINGS.ERROR:
      return {
        ...state,
        platformSettings: { ...action.payload.result?.message },
        isPlatformSettingsLoading: false
      };

    case SET_PLATFORM_SETTINGS:
      return {
        ...state,
        platformSettings: action.payload.platformSettings,
        isInitialPlatformSettingsReady: true
      };

    case PUBLISH_AGENT.REQUEST:
      return {
        ...state,
        ...(['embed', 'unpublish'].includes(action.payload.key) && { isPublishLoading: true })
      };

    case PUBLISH_AGENT.SUCCESS:
      return {
        ...state,
        isPublishLoading: false,
        ...(['embed', 'unpublish'].includes(action.payload.key) && {
          isPublished: action.payload.key === 'embed'
        })
      };

    case PUBLISH_AGENT.ERROR:
      return {
        ...state,
        isPublishLoading: false,
        errorMessage: action.payload.result?.message
      };

    case SAVE_PLATFORM_AGENT_PAGES.REQUEST:
      return { ...state, isSavePlatformAgentPagesLoading: true };

    case SAVE_PLATFORM_AGENT_PAGES.SUCCESS:
      return { ...state, isSavePlatformAgentPagesLoading: false, isPageSaveDisabled: true };

    case SAVE_PLATFORM_AGENT_PAGES.ERROR:
      return {
        ...state,
        isSavePlatformAgentPagesLoading: false,
        errorMessage: action.payload.result?.message
      };

    case DELETE_PLATFORM_AGENT.REQUEST:
      return { ...state, isDeletePlatformAgentLoading: true };

    case DELETE_PLATFORM_AGENT.SUCCESS:
      removeStepFromQueryParams();
      platformSettingsSingleton.PLATFORM_CHATBOT_PAGES = defaultSelectedPages;
      platformSettingsSingleton.PLATFORM_CHATBOT_PUBLISHED = false;

      return {
        ...state,
        isDeletePlatformAgentLoading: false,
        platformSettings: {
          ...state.platformSettings,
          PLATFORM_CHATBOT_PAGES: defaultSelectedPages,
          PLATFORM_CHATBOT_PUBLISHED: false
        },
        selectedPages: defaultSelectedPages,
        isPublished: false
      };

    case DELETE_PLATFORM_AGENT.ERROR:
      return { ...state, isDeletePlatformAgentLoading: false };

    case SAVE_PROVIDER_API_KEY.REQUEST:
      return state;

    case SAVE_PROVIDER_API_KEY.SUCCESS:
      return state;

    case SAVE_PROVIDER_API_KEY.ERROR:
      return { ...state, errorMessage: action.payload.result?.message };

    case SET_SELECTED_PAGES:
      return { ...state, selectedPages: action.payload.selectedPages };

    case SET_VISIBLE_DEVICE:
      return { ...state, visibleDevice: action.payload.visibleDevice };

    case SET_IS_PUBLISHED:
      return { ...state, isPublished: action.payload.isPublished };

    case USE_PLATFORM_AGENT.SUCCESS:
    case GET_PLATFORM_AGENT.SUCCESS:
      const {
        result: { chatbotEmbedSrc }
      } = action.payload;

      if (chatbotEmbedSrc) {
        platformSettingsSingleton.PROVIDER_CHATBOT_EMBED_SRC = chatbotEmbedSrc;
      }

      return state;

    case LOGOUT_FROM_JOTFORM.REQUEST:
      return { ...state, isLogoutLoading: true };

    case LOGOUT_FROM_JOTFORM.SUCCESS:
      platformSettingsSingleton.PROVIDER_API_KEY = '';
      return {
        ...state,
        platformSettings: { ...state.platformSettings, PROVIDER_API_KEY: '' },
        isLogoutLoading: false
      };

    case LOGOUT_FROM_JOTFORM.ERROR:
      return { ...state, isLogoutLoading: false };
    case UNAUTHORIZED_API_KEY:
      return { ...state, isUnauthorizedApiKey: action.payload.isUnauthorizedApiKey };

    default:
      return state;
  }
};

// Platform action creators
export const platformActionCreators = {
  getPlatformSettingsRequest: () => ({
    type: GET_PLATFORM_SETTINGS.REQUEST
  }),

  getPlatformSettingsSuccess: result => ({
    type: GET_PLATFORM_SETTINGS.SUCCESS,
    payload: { result }
  }),

  getPlatformSettingsError: result => ({
    type: GET_PLATFORM_SETTINGS.ERROR,
    payload: { result }
  }),

  setPlatformSettings: platformSettings => ({
    type: SET_PLATFORM_SETTINGS,
    payload: { platformSettings }
  }),

  publishAgentRequest: (key) => ({
    type: PUBLISH_AGENT.REQUEST,
    payload: { key }
  }),

  publishAgentSuccess: (result, key) => ({
    type: PUBLISH_AGENT.SUCCESS,
    payload: { result, key }
  }),

  publishAgentError: result => ({
    type: PUBLISH_AGENT.ERROR,
    payload: { result }
  }),

  savePlatformAgentPagesRequest: () => ({
    type: SAVE_PLATFORM_AGENT_PAGES.REQUEST
  }),

  savePlatformAgentPagesSuccess: result => ({
    type: SAVE_PLATFORM_AGENT_PAGES.SUCCESS,
    payload: { result }
  }),

  savePlatformAgentPagesError: result => ({
    type: SAVE_PLATFORM_AGENT_PAGES.ERROR,
    payload: { result }
  }),

  deletePlatformAgentRequest: () => ({
    type: DELETE_PLATFORM_AGENT.REQUEST
  }),

  deletePlatformAgentSuccess: result => ({
    type: DELETE_PLATFORM_AGENT.SUCCESS,
    payload: { result }
  }),

  deletePlatformAgentError: result => ({
    type: DELETE_PLATFORM_AGENT.ERROR,
    payload: { result }
  }),

  saveProviderApiKeyRequest: () => ({
    type: SAVE_PROVIDER_API_KEY.REQUEST
  }),

  saveProviderApiKeySuccess: result => ({
    type: SAVE_PROVIDER_API_KEY.SUCCESS,
    payload: { result }
  }),

  saveProviderApiKeyError: result => ({
    type: SAVE_PROVIDER_API_KEY.ERROR,
    payload: { result }
  }),

  setSelectedPages: selectedPages => ({
    type: SET_SELECTED_PAGES,
    payload: { selectedPages }
  }),

  updateVisibleDevice: visibleDevice => ({
    type: SET_VISIBLE_DEVICE,
    payload: { visibleDevice }
  }),

  setIsPublished: (isPublished) => ({
    type: SET_IS_PUBLISHED,
    payload: { isPublished }
  }),

  logoutFromJotformRequest: () => ({
    type: LOGOUT_FROM_JOTFORM.REQUEST
  }),

  logoutFromJotformSuccess: result => ({
    type: LOGOUT_FROM_JOTFORM.SUCCESS,
    payload: { result }
  }),

  logoutFromJotformError: result => ({
    type: LOGOUT_FROM_JOTFORM.ERROR,
    payload: { result }
  }),

  setUnauthorizedApiKey: (isUnauthorizedApiKey) => ({
    type: UNAUTHORIZED_API_KEY,
    payload: { isUnauthorizedApiKey }
  })
};
