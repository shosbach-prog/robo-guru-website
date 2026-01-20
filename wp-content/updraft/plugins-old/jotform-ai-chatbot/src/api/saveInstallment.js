import { DELETE_INST_NAME } from '../constants';
import { platformSettings } from '../utils';
import { setInstallment } from './api';

const installmentQueue = [];
let installmentCache = {};
window.__jaic_queue = installmentQueue;
window.__jaic_installment_cache = installmentCache;

const enableCache = false;

export const saveInstallment = (action) => {
  if (!action) return;

  const actionV2 = `${action}_V2`;
  const { PROVIDER_API_KEY, PLATFORM_DOMAIN, PLATFORM } = platformSettings;
  const isReady = PROVIDER_API_KEY && PLATFORM_DOMAIN;

  const hasSent = installmentCache[actionV2];

  const recordInCache = (key) => {
    if (enableCache) {
      installmentCache[key] = 1; // mark as sent
    } else {
      installmentCache[key] = (installmentCache[key] || 0) + 1; // count all sends
    }
  };

  const flushQueue = () => {
    while (installmentQueue.length > 0) {
      const queuedAction = installmentQueue.shift();

      // Only send if caching is disabled OR not already sent
      const shouldSend = !enableCache || !installmentCache[queuedAction];
      if (shouldSend) {
        setInstallment({
          action: queuedAction,
          platform: PLATFORM,
          domain: PLATFORM_DOMAIN
        }, PROVIDER_API_KEY);

        recordInCache(queuedAction);
      }
    }
  };

  if (!isReady) {
    // Queue action only if:
    // - cache disabled → always
    // - cache enabled → only if not already sent
    if (!enableCache || !installmentCache[actionV2]) {
      installmentQueue.push(actionV2);
    }
    return;
  }

  flushQueue();

  // 🔁 Reset cache if it's a DELETE action
  if (actionV2.includes(DELETE_INST_NAME)) {
    installmentCache = {};
    window.__jaic_installment_cache = installmentCache;
  }

  // Handle the current action
  if (!enableCache || !hasSent) {
    setInstallment({
      action: actionV2,
      platform: PLATFORM,
      domain: PLATFORM_DOMAIN
    }, PROVIDER_API_KEY);

    recordInCache(actionV2);
  }
};
