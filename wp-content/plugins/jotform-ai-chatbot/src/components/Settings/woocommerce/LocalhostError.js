import React from 'react';

import { ALL_TEXTS } from '../../../constants';
import { translationRenderer } from '../../../utils';
import { IconExclamationTriangle } from '../../UI/Icon';

const LocalhostError = () => (
  <div className='jfpContent-wrapper--settings-options-wrapper-info-box jfpError'>
    <div className='jfpContent-wrapper--settings-options-wrapper-info-box-icon'>
      <IconExclamationTriangle />
    </div>
    <div className='jfpContent-wrapper--settings-options-wrapper-info-box-message'>
      {translationRenderer(ALL_TEXTS.LOCALHOST_ERROR)({
        renderer1: str => (
          <strong>{str}</strong>)
      })}
    </div>
  </div>
);

export default LocalhostError;
