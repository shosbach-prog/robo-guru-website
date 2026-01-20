import React from 'react';
import { bool } from 'prop-types';

import { ALL_TEXTS } from '../../constants';
import { t } from '../../utils';
import { IconInfoCircle } from '../UI/Icon';

const InfoBox = ({ isPublished }) => (
  <div className='condition-empty-info'>
    <IconInfoCircle className='condition-empty-info-icon' />
    <p className='condition-empty-info-text'>{t(isPublished ? ALL_TEXTS.YOUR_AI_CHATBOT_IS_CURRENTLY_LIVE_ON_ALL_PAGES : ALL_TEXTS.YOUR_AI_CHATBOT_WILL_BE_LIVE_ON_ALL_PAGES)}</p>
  </div>
);

InfoBox.propTypes = {
  isPublished: bool
};

export default InfoBox;
