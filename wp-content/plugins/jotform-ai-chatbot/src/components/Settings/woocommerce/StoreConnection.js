import React, { useRef, useState } from 'react';
import { bool, func, string } from 'prop-types';

import { ALL_TEXTS } from '../../../constants';
import { t } from '../../../utils';
import Button from '../../UI/Button';
import { IconPlus } from '../../UI/Icon';
import Input from '../../UI/Input';
import GuidelineInfoBox from './GuidelineInfoBox';
import InvalidCredentialsError from './InvalidCredentialsError';
import NoAgentError from './NoAgentError';
import PermalinkError from './PermalinkError';

const StoreConnection = ({
  platformUrl,
  isConnectLoading,
  permalinkStructure,
  invalidCredentialsError,
  setWoocommerceSettings,
  previewAgentId
}) => {
  const consumerKeyRef = useRef('');
  const consumerSecrefRef = useRef('');
  const [isDisabled, setIsDisabled] = useState(true);
  const showPermalinkError = permalinkStructure === 'Plain';

  const handleChange = () => {
    const consumerKey = consumerKeyRef.current?.value;
    const consumerSecret = consumerSecrefRef.current?.value;
    if (!consumerKey || !consumerSecret) {
      setIsDisabled(true);
    } else {
      setIsDisabled(false);
    }
  };

  const handleConnectClick = () => {
    const consumerKey = consumerKeyRef.current?.value;
    const consumerSecret = consumerSecrefRef.current?.value;
    if (!consumerKey || !consumerSecret) return;
    setWoocommerceSettings({
      key: consumerKey,
      secret: consumerSecret
    });
  };

  return (
    <>
      {/* consumer key and secret */}
      <div className='jfpContent-wrapper--settings-options-wrapper-input-wrapper'>
        <div className='jfpContent-wrapper--settings-options-wrapper-input'>
          <div className='jfpContent-wrapper--settings-options-wrapper-input-title'>
            <h3 id='consumerKeyTitle'>{t(ALL_TEXTS.CONSUMER_KEY)}</h3>
            <p>{t(ALL_TEXTS.YOUR_WOO_COMMERCE_API_KEY)}</p>
          </div>
          {/* todo: add show/hide key */}
          <Input
            type='input'
            ref={consumerKeyRef}
            placeholder={t(ALL_TEXTS.KEY_PLACEHOLDER)}
            onChange={handleChange}
            aria-labelledby='consumerKeyTitle'
          />
        </div>
        <div className='jfpContent-wrapper--settings-options-wrapper-input'>
          <div className='jfpContent-wrapper--settings-options-wrapper-input-title'>
            <h3 id='consumerSecretTitle'>{t(ALL_TEXTS.CONSUMER_SECRET)}</h3>
            <p>{t(ALL_TEXTS.YOUR_WOO_COMMERCE_API_SECRET)}</p>
          </div>
          {/* todo: add show/hide key */}
          <Input
            type='password'
            ref={consumerSecrefRef}
            placeholder={t(ALL_TEXTS.SECRET_PLACEHOLDER)}
            onChange={handleChange}
            aria-labelledby='consumerSecretTitle'
          />
        </div>
      </div>
      {/* info boxes */}
      <GuidelineInfoBox />
      {showPermalinkError && <PermalinkError platformUrl={platformUrl} />}
      {/* connect btn */}
      <div className='jfpContent-wrapper--settings-options-connect-btn-wrapper'>
        {invalidCredentialsError && <InvalidCredentialsError />}
        {!previewAgentId && <NoAgentError />}
        <Button
          loader={isConnectLoading}
          className='jfpContent-wrapper--settings-options-connect-btn'
          startIcon={<IconPlus />}
          colorStyle='primary'
          onClick={handleConnectClick}
          disabled={!previewAgentId || isDisabled}
        >
          {t(ALL_TEXTS.CONNECT)}
        </Button>
      </div>
    </>
  );
};

StoreConnection.propTypes = {
  setWoocommerceSettings: func.isRequired,
  platformUrl: string.isRequired,
  permalinkStructure: string.isRequired,
  invalidCredentialsError: bool.isRequired,
  isConnectLoading: bool.isRequired,
  previewAgentId: string.isRequired
};

export default StoreConnection;
