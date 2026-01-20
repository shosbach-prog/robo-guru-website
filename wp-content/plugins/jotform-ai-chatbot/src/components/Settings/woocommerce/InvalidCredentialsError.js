import React from 'react';

import { ALL_TEXTS } from '../../../constants';
import { t } from '../../../utils';

const InvalidCredentialsError = () => (
  <p className='jfpContent-wrapper--settings-options-input-error'>{t(ALL_TEXTS.INVALID_CREDENTIALS)}</p>
);

export default InvalidCredentialsError;
