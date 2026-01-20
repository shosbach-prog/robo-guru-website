import { createAxiosInstance } from './base';

let platformLayer = null;

export const initializePlatformLayer = platformApiUrl => {
  if (!platformLayer) {
    platformLayer = createAxiosInstance(platformApiUrl);
  } else {
    console.warn('PlatformLayer is already initialized!');
  }
};

export const getPlatformLayer = () => {
  if (!platformLayer) {
    throw new Error('PlatformLayer is not initialized. Call initializePlatformLayer first.');
  }
  return platformLayer;
};
