import { getBaseURL } from '../utils';
import { createAxiosInstance } from './base';

const createLayer = () => createAxiosInstance(getBaseURL(), false);

let requestLayer = createLayer();

export const reinitializeRequestLayer = () => {
  requestLayer = createLayer();
};

export const getRequestLayer = () => {
  if (!requestLayer) {
    throw new Error('RequestLayer is not initialized. Call initializeRequestLayer first.');
  }
  return requestLayer;
};
