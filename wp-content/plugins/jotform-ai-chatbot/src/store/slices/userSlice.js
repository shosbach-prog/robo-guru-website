import { generatePromiseActionType } from '../actionTypes';
import { ADD_MATERIAL, FETCH_USER, LOGOUT_FROM_JOTFORM } from './commonActions';

// Internal action types (only used within this slice)
const SET_USER = 'SET_USER';
const FETCH_LIMIT_WARNINGS = generatePromiseActionType('FETCH_LIMIT_WARNINGS');

// Initial state for user domain
export const userInitialState = {
  user: null,
  refetchUser: false,
  showNetworkError: false,
  limitWarnings: {},
  refetchLimitWarnings: false
};

// User slice reducer
export const userReducer = (state, action) => {
  switch (action.type) {
    case SET_USER:
      return { ...state, user: action.payload.user };

    case FETCH_USER.REQUEST:
      return state;

    case FETCH_USER.SUCCESS:
      const user = action.payload.result;
      return { ...state, user };

    case FETCH_USER.ERROR:
      let errorState = {};
      const errorResult = action.payload.result;
      const errorData = errorResult?.data;
      if (errorResult.message === 'Network Error' && errorResult.code === 'ERR_NETWORK') {
        errorState = { showNetworkError: true };
      }
      if (errorData?.responseCode === 301 && errorData.location?.includes('eu-api')) {
        errorState = { refetchUser: true };
      }
      return { ...state, ...errorState };

    case LOGOUT_FROM_JOTFORM.SUCCESS:
      return {
        ...state,
        ...userInitialState
      };

    case FETCH_LIMIT_WARNINGS.SUCCESS: {
      return {
        ...state,
        limitWarnings: action.payload.result
      };
    }

    case ADD_MATERIAL.REQUEST: {
      return {
        ...state,
        refetchLimitWarnings: !state.refetchLimitWarnings
      };
    }

    case FETCH_LIMIT_WARNINGS.REQUEST:
    case FETCH_LIMIT_WARNINGS.ERROR:
      return state;

    default:
      return state;
  }
};

// User action creators
export const userActionCreators = {
  setUser: user => ({
    type: SET_USER,
    payload: { user }
  }),

  fetchUserRequest: () => ({
    type: FETCH_USER.REQUEST
  }),

  fetchUserSuccess: result => ({
    type: FETCH_USER.SUCCESS,
    payload: { result }
  }),

  fetchUserError: result => ({
    type: FETCH_USER.ERROR,
    payload: { result }
  }),
  fetchLimitWarningsRequest: () => ({
    type: FETCH_LIMIT_WARNINGS.REQUEST
  }),
  fetchLimitWarningsSuccess: result => ({
    type: FETCH_LIMIT_WARNINGS.SUCCESS,
    payload: { result }
  }),
  fetchLimitWarningsError: () => ({
    type: FETCH_LIMIT_WARNINGS.ERROR
  })
};
