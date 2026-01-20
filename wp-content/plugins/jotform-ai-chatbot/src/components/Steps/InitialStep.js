import React, { useEffect } from 'react';
import { object } from 'prop-types';

import { saveInstallment } from '../../api';
import IconArrowRight from '../../assets/svg/IconArrowRight.svg';
import LogoJotformColor from '../../assets/svg/LogoJotformColor.svg';
import { ALL_TEXTS } from '../../constants';
import { useWizard } from '../../hooks';
import { t, toCamelCase } from '../../utils';
import NetworkError from '../NetworkError';
import Button from '../UI/Button';
import UnauthorizedApiKeyError from '../UnauthorizedApiKeyError';

const InitialStep = ({
  customTexts = {}
}) => {
  const { state } = useWizard();
  const {
    step, showNetworkError, isUnauthorizedApiKey
  } = state;

  const PROJECT_NAME = 'Jotform Wordpress AI Chatbot';
  const PROJECT_URL = window.location.href;
  const ENTERPRISE_LOGIN_ENDPOINT = '/api/legacy-oauth/enterprise-domain';
  // eslint-disable-next-line max-len
  const loginUrl = `https://www.jotform.com/api/oauth.php?registrationType=oauth&client_id=${encodeURIComponent(PROJECT_NAME)}&access_type=full&auth_type=login&ref=${encodeURIComponent(PROJECT_URL)}&integration_auth=1&isNewLoginFlow=1&enterpriseLoginEndpoint=${encodeURIComponent(ENTERPRISE_LOGIN_ENDPOINT)}&rk=1&wpPlugin=1`;

  useEffect(() => {
    if (showNetworkError) return;
    saveInstallment(`${toCamelCase(step)}Step`);
  }, []);

  const handleStartClick = () => {
    saveInstallment('letsStartButton');
  };

  return (
    <>
      <div className='first-step'>
        <div className='first-step--logo'>
          <LogoJotformColor width='148' height='28' />
        </div>
        <h2>{t(customTexts.title || ALL_TEXTS.READ_TO_BUILD_YOUR_AI)}</h2>
        <p>{t(customTexts.subtitle || ALL_TEXTS.CREATE_AND_CUSTOMIZE_YOUR_AI)}</p>
        <Button
          endIcon={<IconArrowRight />}
          onClick={handleStartClick}
          className='lets-start buttonRTL'
          target='_self'
          href={loginUrl}
        >
          {t(ALL_TEXTS.LETS_START)}
        </Button>
      </div>
      {showNetworkError && <NetworkError />}
      {isUnauthorizedApiKey && <UnauthorizedApiKeyError />}
    </>
  );
};

export default InitialStep;

InitialStep.propTypes = {
  customTexts: object
};
