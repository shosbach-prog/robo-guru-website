import { reinitializeRequestLayer } from '../../api';
import { EU_PROVIDER_API_URL, EU_PROVIDER_URL, STEPS } from '../../constants';
import { SETTINGS_TABS } from '../../constants/wizard';
import { isSettingsPage, platformSettings as platformSettingsSingleton, removeStepFromQueryParams } from '../../utils';
import { generatePromiseActionType } from '../actionTypes';
import {
  DELETE_PLATFORM_AGENT, FETCH_USER, GET_PLATFORM_AGENT, LOGOUT_FROM_JOTFORM, USE_PLATFORM_AGENT
} from './commonActions';

// Internal action types (only used within this slice)
const SET_STEP = 'SET_STEP';
const SET_PROMPT = 'SET_PROMPT';
const SET_LIMIT_DIALOG_VISIBLE = 'SET_LIMIT_DIALOG_VISIBLE';
const SET_INITAL_LOADING = 'SET_INITAL_LOADING';
const SET_GET_PLATFORM_AGENT_ONCE = 'SET_GET_PLATFORM_AGENT_ONCE';
const SET_ACTIVE_SETTINGS_TAB = 'SET_ACTIVE_SETTINGS_TAB';

const CHECK_AI_CHATBOT_LIMITS = generatePromiseActionType('CHECK_AI_CHATBOT_LIMITS');

// Initial state for UI-related state
export const uiInitialState = {
  step: STEPS.INITIAL,
  prompt: '',
  isUseAgentLoading: false,
  isInitialLoading: true,
  isLimitDialogVisible: false,
  tryGetPlatformAgentOnce: false,
  errorMessage: '',
  activeSettingsTab: SETTINGS_TABS.GENERAL,
  showUnauthorizedUserError: false
};

// UI slice reducer
export const uiReducer = (state, action) => {
  switch (action.type) {
    case SET_STEP:
      return { ...state, step: action.payload.step };

    case SET_PROMPT:
      return { ...state, prompt: action.payload.prompt };

    case SET_LIMIT_DIALOG_VISIBLE:
      return { ...state, isLimitDialogVisible: action.payload.isLimitDialogVisible };

    case SET_INITAL_LOADING:
      return { ...state, isInitialLoading: action.payload.isLoading };

    case SET_GET_PLATFORM_AGENT_ONCE:
      return { ...state, tryGetPlatformAgentOnce: action.payload.tryOnce };

    case USE_PLATFORM_AGENT.REQUEST:
      return { ...state, isUseAgentLoading: true };

    case USE_PLATFORM_AGENT.SUCCESS:
    case GET_PLATFORM_AGENT.SUCCESS:
      const {
        result: { content = undefined },
        step
      } = action.payload;

      if (content === false) {
        return {
          ...state,
          step: isSettingsPage() ? STEPS.SETTINGS : STEPS.USECASE_SELECTION,
          isUseAgentLoading: false,
          isInitialLoading: false
        };
      }

      return {
        ...state,
        step: step || STEPS.AI_PERSONA,
        isLimitDialogVisible: false,
        isUseAgentLoading: false,
        isInitialLoading: false
      };

    case USE_PLATFORM_AGENT.ERROR:
    case GET_PLATFORM_AGENT.ERROR:
      const { tryOnce = false, result } = action.payload;

      // for enterprise teams data user
      // todo: ideally responseCode should be 401
      const showUnauthorizedUserError = result?.data?.responseCode === 500 && result?.data?.content === 'Unauthorized Access';

      return {
        ...state,
        errorMessage: action.payload.result?.message,
        isUseAgentLoading: false,
        tryGetPlatformAgentOnce: tryOnce,
        showUnauthorizedUserError
      };

    case FETCH_USER.ERROR:
      let errorState = {};
      const errorData = action.payload.result?.data;
      if (errorData?.responseCode === 301 && errorData.location?.includes('eu-api')) {
        platformSettingsSingleton.PROVIDER_URL = EU_PROVIDER_URL;
        platformSettingsSingleton.PROVIDER_API_URL = EU_PROVIDER_API_URL;
        reinitializeRequestLayer();
        errorState = {};
      }
      return {
        ...state,
        step: STEPS.INITIAL,
        isInitialLoading: false,
        ...errorState
      };

    case CHECK_AI_CHATBOT_LIMITS.REQUEST:
      return state;

    case CHECK_AI_CHATBOT_LIMITS.SUCCESS:
      if (typeof action.payload.result === 'boolean' && action.payload.result === true) {
        return { ...state, isLimitDialogVisible: true };
      }
      return state;

    case CHECK_AI_CHATBOT_LIMITS.ERROR:
      console.error('chatbot limits fetch error', action.payload);
      return state;

    case DELETE_PLATFORM_AGENT.SUCCESS:
      removeStepFromQueryParams();
      return {
        ...state,
        step: STEPS.USECASE_SELECTION,
        prompt: ''
      };

    case SET_ACTIVE_SETTINGS_TAB:
      return {
        ...state,
        activeSettingsTab: action.payload.tab
      };

    case LOGOUT_FROM_JOTFORM.SUCCESS:
      return {
        ...state,
        step: STEPS.INITIAL
      };

    default:
      return state;
  }
};

// UI action creators
export const uiActionCreators = {
  setStep: (step, initialScreen) => ({
    type: SET_STEP,
    payload: { step, initialScreen }
  }),

  setPrompt: prompt => ({
    type: SET_PROMPT,
    payload: { prompt }
  }),

  setIsLimitDialogVisible: isLimitDialogVisible => ({
    type: SET_LIMIT_DIALOG_VISIBLE,
    payload: { isLimitDialogVisible }
  }),

  setInitialLoading: isLoading => ({
    type: SET_INITAL_LOADING,
    payload: { isLoading }
  }),

  setTryGetPlatformAgentOnce: tryOnce => ({
    type: SET_GET_PLATFORM_AGENT_ONCE,
    payload: { tryOnce }
  }),

  checkAIChatbotLimitsRequest: () => ({
    type: CHECK_AI_CHATBOT_LIMITS.REQUEST
  }),

  checkAIChatbotLimitsSuccess: result => ({
    type: CHECK_AI_CHATBOT_LIMITS.SUCCESS,
    payload: { result }
  }),

  checkAIChatbotLimitsError: error => ({
    type: CHECK_AI_CHATBOT_LIMITS.ERROR,
    payload: error
  }),

  // Platform agent actions that affect UI
  usePlatformAgentRequest: () => ({
    type: USE_PLATFORM_AGENT.REQUEST
  }),

  usePlatformAgentSuccess: result => ({
    type: USE_PLATFORM_AGENT.SUCCESS,
    payload: { result }
  }),

  usePlatformAgentError: (result, { tryOnce }) => ({
    type: USE_PLATFORM_AGENT.ERROR,
    payload: { result, tryOnce }
  }),

  getPlatformAgentRequest: () => ({
    type: GET_PLATFORM_AGENT.REQUEST
  }),

  getPlatformAgentSuccess: (result, step) => ({
    type: GET_PLATFORM_AGENT.SUCCESS,
    payload: { result, step }
  }),

  getPlatformAgentError: (result) => ({
    type: GET_PLATFORM_AGENT.ERROR,
    payload: { result }
  }),

  setActiveSettingsTab: (tab) => ({
    type: SET_ACTIVE_SETTINGS_TAB,
    payload: { tab }
  })
};
