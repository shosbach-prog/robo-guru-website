import React from 'react';
import { string } from 'prop-types';

import { ALL_TEXTS } from '../../../constants';
import { translationRenderer } from '../../../utils';
import { IconExclamationTriangle } from '../../UI/Icon';

const PermalinkError = ({ platformUrl }) => (
  <div className='jfpContent-wrapper--settings-options-wrapper-info-box jfpError'>
    <div className='jfpContent-wrapper--settings-options-wrapper-info-box-icon'>
      <IconExclamationTriangle />
    </div>
    <div className='jfpContent-wrapper--settings-options-wrapper-info-box-message'>
      {translationRenderer(ALL_TEXTS.WOOCOMMERCE_PERMALINK_ERROR)({
        renderer1: str => (
          <strong>{str}</strong>),
        renderer2: str => (
          <a className='jfpContent-wrapper--settings-options-wrapper-info-box-link' href={`${platformUrl}/wp-admin/options-permalink.php`} target='_blank' rel='noreferrer'>{str}</a>)
      })}
    </div>
  </div>
);

PermalinkError.propTypes = {
  platformUrl: string.isRequired
};

export default PermalinkError;
