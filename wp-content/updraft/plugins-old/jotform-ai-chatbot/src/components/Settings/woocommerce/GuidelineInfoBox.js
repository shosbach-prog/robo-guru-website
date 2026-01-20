import React from 'react';

import { ALL_TEXTS } from '../../../constants';
import { translationRenderer } from '../../../utils';
import { IconExclamationCircle } from '../../UI/Icon';

const GuidelineInfoBox = () => (
  <div className='jfpContent-wrapper--settings-options-wrapper-info-box'>
    <div className='jfpContent-wrapper--settings-options-wrapper-info-box-icon'>
      <IconExclamationCircle />
    </div>
    <div className='jfpContent-wrapper--settings-options-wrapper-info-box-message'>
      {translationRenderer(ALL_TEXTS.WOOCOMMERCE_CONSUMER_SECRET_AND_KEY)({
        renderer1: str => (
          <strong>{str}</strong>)
      })}
    </div>
  </div>
);

export default GuidelineInfoBox;
