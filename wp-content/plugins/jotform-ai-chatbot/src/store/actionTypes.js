/**
 * Utility function to generate promise action types for async operations
 * @param {string} baseName - The base name for the action type
 * @returns {Object} Object containing REQUEST, SUCCESS, and ERROR action types
 */
export const generatePromiseActionType = baseName => ({
  REQUEST: `${baseName}/REQUEST`,
  SUCCESS: `${baseName}/SUCCESS`,
  ERROR: `${baseName}/ERROR`
});
