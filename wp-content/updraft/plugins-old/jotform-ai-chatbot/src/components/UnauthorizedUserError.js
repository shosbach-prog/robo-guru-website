import React from 'react';

import { ALL_TEXTS } from '../constants';
import GenericError from './GenericError';

const UnauthorizedUserError = () => (
  <div style={{ marginTop: '30px' }}>
    <GenericError message={ALL_TEXTS.UNAUTHORIZED_API_KEY_DESC} />
  </div>
);

export default UnauthorizedUserError;
