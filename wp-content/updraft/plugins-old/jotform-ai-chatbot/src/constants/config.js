/**
 * Configuration and platform-related constants
 */

export const PLATFORMS = {
  WORDPRESS: 'wordpress'
};

// API and provider URLs
export const EU_PROVIDER_URL = 'https://eu.jotform.com';
export const EU_PROVIDER_API_URL = 'https://eu-api.jotform.com';

// Installment and deletion
export const DELETE_INST_NAME = 'deleteWpChatbotButton';

// Timeout and debounce settings
export const WRITING_DEBOUNCE_TIMEOUT = 1750;
export const DELETE_INSTRUCTION_DEBOUNCE_TIMEOUT = 500;
export const GREETING_TEXT_REQ_DEBOUNCE_TIMEOUT = 500;

// Modal and local storage flags
export const WHATS_NEW_MODAL_LCST_FLAG = 'jaic_wnm_v3_6_0';

// Validation patterns
export const URL_REGEX = /^(https?:\/\/)(localhost|([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,})(:\d{1,5})?(\/[^\s]*)?$/i;
