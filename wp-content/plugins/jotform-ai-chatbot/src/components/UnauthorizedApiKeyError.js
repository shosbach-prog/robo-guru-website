import React from 'react';

import { ALL_TEXTS } from '../constants';
import GenericError from './GenericError';

const UnauthorizedApiKeyError = () => (
  <GenericError message={ALL_TEXTS.UNAUTHORIZED_API_KEY_DESC} />
);

export default UnauthorizedApiKeyError;
