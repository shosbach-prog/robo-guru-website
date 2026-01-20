import React from 'react';

import { ALL_TEXTS } from '../constants';
import GenericError from './GenericError';

const NetworkError = () => (
  <div style={{ marginTop: '100px' }}>
    <GenericError message={ALL_TEXTS.NETWORK_ERROR_DESC_ENTERPRISE} />
  </div>
);

export default NetworkError;
