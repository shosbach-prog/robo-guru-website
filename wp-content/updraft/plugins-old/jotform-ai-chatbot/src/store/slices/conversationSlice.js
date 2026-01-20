import { generatePromiseActionType } from '../actionTypes';

// Internal action types (only used within this slice)
const FETCH_CHATS = generatePromiseActionType('FETCH_CHATS');
const FETCH_CONVERSATIONS = generatePromiseActionType('FETCH_CONVERSATIONS');
const SET_FETCH_CONVERSATIONS_LOADING = 'SET_FETCH_CONVERSATIONS_LOADING';

// Initial state for conversations and chats domain
export const conversationInitialState = {
  conversations: {
    loading: false,
    items: [],
    archivedItems: [],
    lastUUID: '',
    allConversationsFetched: false
  },
  chats: {
    loading: false,
    items: {}
  }
};

// Conversation slice reducer
export const conversationReducer = (state, action) => {
  switch (action.type) {
    case FETCH_CONVERSATIONS.REQUEST:
      return {
        ...state,
        conversations: { ...state.conversations, loading: true }
      };

    case FETCH_CONVERSATIONS.SUCCESS:
      const conv = action.payload.result || [];
      const filteredConv = conv.filter(c => c.status !== 'ARCHIVED');
      const archivedConv = conv.filter(c => c.status === 'ARCHIVED');
      const lastUUID = filteredConv[filteredConv.length - 1]?.id || '';

      return {
        ...state,
        conversations: {
          items: [...state.conversations.items, ...filteredConv],
          archivedItems: [...state.conversations.archivedItems, ...archivedConv],
          lastUUID,
          loading: true,
          allConversationsFetched: filteredConv?.length === 0
        }
      };

    case FETCH_CONVERSATIONS.ERROR:
      return {
        ...state,
        conversations: { ...state.conversations, loading: false }
      };

    case SET_FETCH_CONVERSATIONS_LOADING:
      return {
        ...state,
        conversations: {
          ...state.conversations,
          loading: action.payload.loading
        }
      };

    case FETCH_CHATS.REQUEST:
      return {
        ...state,
        chats: { ...state.chats, loading: true }
      };

    case FETCH_CHATS.SUCCESS:
      return {
        ...state,
        chats: {
          items: { ...state.chats.items, ...action.payload.result },
          loading: false
        }
      };

    case FETCH_CHATS.ERROR:
      return {
        ...state,
        chats: { ...state.chats, loading: false }
      };

    default:
      return state;
  }
};

// Conversation action creators
export const conversationActionCreators = {
  fetchConversationsRequest: () => ({
    type: FETCH_CONVERSATIONS.REQUEST
  }),

  fetchConversationsSuccess: result => ({
    type: FETCH_CONVERSATIONS.SUCCESS,
    payload: { result }
  }),

  fetchConversationsError: result => ({
    type: FETCH_CONVERSATIONS.ERROR,
    payload: { result }
  }),

  setFetchConversationsLoading: loading => ({
    type: SET_FETCH_CONVERSATIONS_LOADING,
    payload: { loading }
  }),

  fetchChatsRequest: () => ({
    type: FETCH_CHATS.REQUEST
  }),

  fetchChatsSuccess: result => ({
    type: FETCH_CHATS.SUCCESS,
    payload: { result }
  }),

  fetchChatsError: result => ({
    type: FETCH_CHATS.ERROR,
    payload: { result }
  })
};
