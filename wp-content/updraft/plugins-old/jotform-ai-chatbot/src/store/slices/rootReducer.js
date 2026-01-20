import { agentInitialState, agentReducer } from './agentSlice';
import { avatarInitialState, avatarReducer } from './avatarSlice';
import { conversationInitialState, conversationReducer } from './conversationSlice';
import { materialInitialState, materialReducer } from './materialSlice';
import { platformInitialState, platformReducer } from './platformSlice';
import { themeInitialState, themeReducer } from './themeSlice';
import { uiInitialState, uiReducer } from './uiSlice';
import { userInitialState, userReducer } from './userSlice';
import { woocommerceInitialState, woocommerceReducer } from './woocommerceSlice';

// Combined initial state from all slices
export const initialState = {
  ...userInitialState,
  ...agentInitialState,
  ...avatarInitialState,
  ...materialInitialState,
  ...themeInitialState,
  ...platformInitialState,
  ...conversationInitialState,
  ...uiInitialState,
  ...woocommerceInitialState
};

// Reducer map for clean organization and performance
const reducers = {
  user: {
    reducer: userReducer,
    select: state => ({
      user: state.user,
      termsChecked: state.termsChecked,
      refetchUser: state.refetchUser,
      showNetworkError: state.showNetworkError,
      limitWarnings: state.limitWarnings,
      refetchLimitWarnings: state.refetchLimitWarnings
    })
  },
  agent: {
    reducer: agentReducer,
    select: state => ({
      agentName: state.agentName,
      agentRole: state.agentRole,
      agentChattiness: state.agentChattiness,
      agentLanguage: state.agentLanguage,
      agentToneOfVoice: state.agentToneOfVoice,
      persona: state.persona,
      previewAgentId: state.previewAgentId,
      activeViewId: state.activeViewId,
      refreshPreviewForAvatar: state.refreshPreviewForAvatar
    })
  },
  avatar: {
    reducer: avatarReducer,
    select: state => ({
      selectedAvatar: state.selectedAvatar,
      avatars: state.avatars,
      avatarsOffset: state.avatarsOffset,
      areAvatarsLoading: state.areAvatarsLoading,
      allAvatarsFetched: state.allAvatarsFetched,
      allAgents: state.allAgents
    })
  },
  material: {
    reducer: materialReducer,
    select: state => ({
      materials: state.materials,
      materialsLoading: state.materialsLoading
    })
  },
  theme: {
    reducer: themeReducer,
    select: state => ({
      themeName: state.themeName,
      customizations: state.customizations,
      themeCustomizations: state.themeCustomizations
    })
  },
  platform: {
    reducer: platformReducer,
    select: state => ({
      isPlatformSettingsLoading: state.isPlatformSettingsLoading,
      isInitialPlatformSettingsReady: state.isInitialPlatformSettingsReady,
      isPublishLoading: state.isPublishLoading,
      isDeletePlatformAgentLoading: state.isDeletePlatformAgentLoading,
      isSavePlatformAgentPagesLoading: state.isSavePlatformAgentPagesLoading,
      selectedPages: state.selectedPages,
      platformSettings: state.platformSettings,
      visibleDevice: state.visibleDevice,
      isPublished: state.isPublished,
      errorMessage: state.errorMessage,
      isLogoutLoading: state.isLogoutLoading,
      isUnauthorizedApiKey: state.isUnauthorizedApiKey
    })
  },
  conversation: {
    reducer: conversationReducer,
    select: state => ({
      conversations: state.conversations,
      chats: state.chats
    })
  },
  ui: {
    reducer: uiReducer,
    select: state => ({
      step: state.step,
      prompt: state.prompt,
      isUseAgentLoading: state.isUseAgentLoading,
      isInitialLoading: state.isInitialLoading,
      isLimitDialogVisible: state.isLimitDialogVisible,
      tryGetPlatformAgentOnce: state.tryGetPlatformAgentOnce,
      errorMessage: state.errorMessage,
      activeSettingsTab: state.activeSettingsTab,
      showUnauthorizedUserError: state.showUnauthorizedUserError
    })
  },
  woocommerce: {
    reducer: woocommerceReducer,
    select: state => ({
      woocommerce: state.woocommerce
    })
  }
};

// Simple and performant root reducer
export const rootReducer = (state, action) => {
  if (state === undefined) {
    return initialState;
  }

  let hasChanged = false;
  const nextState = {};

  // Run all reducers and collect changes
  Object.values(reducers).forEach(({ reducer, select }) => {
    const sliceState = select(state);
    const newSliceState = reducer(sliceState, action);

    if (newSliceState !== sliceState) {
      hasChanged = true;
    }

    Object.assign(nextState, newSliceState);
  });

  return hasChanged ? nextState : state;
};
