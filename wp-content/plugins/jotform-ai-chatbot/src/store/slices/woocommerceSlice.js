import { WOO_COMMERCE_PROPERTIES } from '../../constants';
import { generatePromiseActionType } from '../actionTypes';

// Internal action types (only used within this slice)
const SET_WOOCOMMERCE_ABILITY = 'SET_WOOCOMMERCE_ABILITY';
const SET_WOOCOMMERCE_CONSUMER_KEY = 'SET_WOOCOMMERCE_CONSUMER_KEY';
const RESET_INVALID_CREDENTIALS_ERROR = 'RESET_INVALID_CREDENTIALS_ERROR';
const GET_WOOCOMMERCE_SETTINGS = generatePromiseActionType('GET_WOOCOMMERCE_SETTINGS');
const SET_WOOCOMMERCE_SETTINGS = generatePromiseActionType('SET_WOOCOMMERCE_SETTINGS');
const UPDATE_WOOCOMMERCE_SETTINGS = generatePromiseActionType('UPDATE_WOOCOMMERCE_SETTINGS');
const DISCONNECT_WOOCOMMERCE_STORE = generatePromiseActionType('DISCONNECT_WOOCOMMERCE_STORE');

// Initial state for woocommerce domain
export const woocommerceInitialState = {
  woocommerce: {
    consumerKey: '',
    isConnected: false,
    isConnectLoading: false,
    isSettingsLoading: false,
    isDisconnectLoading: false,
    invalidCredentialsError: false,
    abilities: {
      [WOO_COMMERCE_PROPERTIES.PRODUCT_FILTER]: true,
      [WOO_COMMERCE_PROPERTIES.PRODUCT_RECOMMENDATION]: true,
      [WOO_COMMERCE_PROPERTIES.ADD_TO_CART]: true,
      [WOO_COMMERCE_PROPERTIES.ORDER_TRACKING]: true
      // [WOO_COMMERCE_PROPERTIES.REFUND_REQUEST]: true
    }
  }
};

// Woocommerce slice reducer
export const woocommerceReducer = (state, action) => {
  switch (action.type) {
    case SET_WOOCOMMERCE_CONSUMER_KEY:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          consumerKey: action.payload.consumerKey
        }
      };

    case SET_WOOCOMMERCE_ABILITY:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          abilities: {
            ...state.woocommerce.abilities,
            [action.payload.key]: action.payload.value
          }
        }
      };

    case GET_WOOCOMMERCE_SETTINGS.REQUEST:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isSettingsLoading: true
        }
      };

    case GET_WOOCOMMERCE_SETTINGS.SUCCESS:
      const { integrationOptions, consumerKeyMasked } = action.payload?.result || {};
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isConnected: !!consumerKeyMasked,
          consumerKey: consumerKeyMasked,
          abilities: {
            [WOO_COMMERCE_PROPERTIES.PRODUCT_FILTER]: integrationOptions.includes(WOO_COMMERCE_PROPERTIES.PRODUCT_FILTER),
            [WOO_COMMERCE_PROPERTIES.PRODUCT_RECOMMENDATION]: integrationOptions.includes(WOO_COMMERCE_PROPERTIES.PRODUCT_RECOMMENDATION),
            [WOO_COMMERCE_PROPERTIES.ADD_TO_CART]: integrationOptions.includes(WOO_COMMERCE_PROPERTIES.ADD_TO_CART),
            [WOO_COMMERCE_PROPERTIES.ORDER_TRACKING]: integrationOptions.includes(WOO_COMMERCE_PROPERTIES.ORDER_TRACKING)
          },
          isSettingsLoading: false
        }
      };

    case GET_WOOCOMMERCE_SETTINGS.ERROR:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isSettingsLoading: false
        }
      };

    case SET_WOOCOMMERCE_SETTINGS.REQUEST:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isConnectLoading: true
        }
      };

    case SET_WOOCOMMERCE_SETTINGS.SUCCESS:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isConnected: !!action.payload.result?.consumerKeyMasked,
          consumerKey: action.payload.result?.consumerKeyMasked,
          isConnectLoading: false
        }
      };

    case SET_WOOCOMMERCE_SETTINGS.ERROR:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isConnectLoading: false,
          invalidCredentialsError: action.payload.result?.data?.responseCode === 401
        }
      };

    case DISCONNECT_WOOCOMMERCE_STORE.REQUEST:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isDisconnectLoading: true
        }
      };

    case DISCONNECT_WOOCOMMERCE_STORE.SUCCESS:
      return {
        ...state,
        woocommerce: {
          ...woocommerceInitialState.woocommerce,
          isDisconnectLoading: false
        }
      };

    case DISCONNECT_WOOCOMMERCE_STORE.ERROR:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          isDisconnectLoading: false
        }
      };

    case RESET_INVALID_CREDENTIALS_ERROR:
      return {
        ...state,
        woocommerce: {
          ...state.woocommerce,
          invalidCredentialsError: false
        }
      };

    default:
      return state;
  }
};

// Woocommerce action creators
export const woocommerceActionCreators = {
  setWoocommerceAbility: (key, value) => ({
    type: SET_WOOCOMMERCE_ABILITY,
    payload: { key, value }
  }),
  // get settings
  getWoocommerceSettingsRequest: result => ({
    type: GET_WOOCOMMERCE_SETTINGS.REQUEST,
    payload: { result }
  }),
  getWoocommerceSettingsSuccess: result => ({
    type: GET_WOOCOMMERCE_SETTINGS.SUCCESS,
    payload: { result }
  }),
  getWoocommerceSettingsError: result => ({
    type: GET_WOOCOMMERCE_SETTINGS.ERROR,
    payload: { result }
  }),
  // set settings
  setWoocommerceSettingsRequest: result => ({
    type: SET_WOOCOMMERCE_SETTINGS.REQUEST,
    payload: { result }
  }),
  setWoocommerceSettingsSuccess: result => ({
    type: SET_WOOCOMMERCE_SETTINGS.SUCCESS,
    payload: { result }
  }),
  setWoocommerceSettingsError: result => ({
    type: SET_WOOCOMMERCE_SETTINGS.ERROR,
    payload: { result }
  }),
  // update settings
  updateWoocommerceSettingsRequest: result => ({
    type: UPDATE_WOOCOMMERCE_SETTINGS.REQUEST,
    payload: { result }
  }),
  updateWoocommerceSettingsSuccess: result => ({
    type: UPDATE_WOOCOMMERCE_SETTINGS.SUCCESS,
    payload: { result }
  }),
  updateWoocommerceSettingsError: result => ({
    type: UPDATE_WOOCOMMERCE_SETTINGS.ERROR,
    payload: { result }
  }),
  // disconnect store
  disconnectWoocommerceStoreRequest: result => ({
    type: DISCONNECT_WOOCOMMERCE_STORE.REQUEST,
    payload: { result }
  }),
  disconnectWoocommerceStoreSuccess: result => ({
    type: DISCONNECT_WOOCOMMERCE_STORE.SUCCESS,
    payload: { result }
  }),
  disconnectWoocommerceStoreError: result => ({
    type: DISCONNECT_WOOCOMMERCE_STORE.ERROR,
    payload: { result }
  }),
  resetInvalidCredentialsError: () => ({
    type: RESET_INVALID_CREDENTIALS_ERROR
  })
};
