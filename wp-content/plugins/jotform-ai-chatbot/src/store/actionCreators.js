import { agentActionCreators } from './slices/agentSlice';
import { avatarActionCreators } from './slices/avatarSlice';
import { conversationActionCreators } from './slices/conversationSlice';
import { materialActionCreators } from './slices/materialSlice';
import { platformActionCreators } from './slices/platformSlice';
import { themeActionCreators } from './slices/themeSlice';
import { uiActionCreators } from './slices/uiSlice';
import { userActionCreators } from './slices/userSlice';
import { woocommerceActionCreators } from './slices/woocommerceSlice';

// Combined action creators from all slices
export const ACTION_CREATORS = {
  // User actions
  ...userActionCreators,

  // Agent actions
  ...agentActionCreators,

  // Avatar actions
  ...avatarActionCreators,

  // Material actions
  ...materialActionCreators,

  // Theme actions
  ...themeActionCreators,

  // Platform actions
  ...platformActionCreators,

  // Conversation actions
  ...conversationActionCreators,

  // UI actions
  ...uiActionCreators,

  // Woocommerce actions
  ...woocommerceActionCreators
};
