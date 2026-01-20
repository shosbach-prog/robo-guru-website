import React, {
  useEffect, useMemo, useReducer, useState
} from 'react';
import { node, shape } from 'prop-types';

import {
  fetchUserLimitWarnings, fetcUser, interactWithPlatform
} from '../api';
import { initializePlatformLayer } from '../api/platformLayerSingleton';
import {
  ACTION_CREATORS, initialState, rootReducer
} from '../store';
import {
  awaitFor, cleanParams, createAsyncDispatch, platformSettings as platformSettingsSingleton,
  toggleSettingsItems
} from '../utils';
import { WizardContext } from './WizardContext';

export const WizardProvider = ({
  children,
  ...props
}) => {
  const [state, dispatch] = useReducer(rootReducer, { ...initialState, ...props });

  const {
    isInitialPlatformSettingsReady,
    platformSettings: { PROVIDER_API_KEY },
    refetchUser,
    refetchLimitWarnings
  } = state;

  // async dispatch
  const asyncDispatch = createAsyncDispatch(dispatch);

  const contextValue = useMemo(() => ({
    state,
    dispatch,
    asyncDispatch
  }), [
    state,
    dispatch,
    asyncDispatch
  ]);

  // platform settings & init platform layer
  useEffect(() => {
    const PLATFORM_API_URL = document.getElementById('platform_api_url').value;
    const PLATFORM_NONCE = document.getElementById('_nonce').value;
    const PLATFORM_REFERER = document.getElementsByName('_wp_http_referer')[0].value;
    platformSettingsSingleton.PLATFORM_API_URL = PLATFORM_API_URL;
    platformSettingsSingleton.PLATFORM_NONCE = PLATFORM_NONCE;
    platformSettingsSingleton.PLATFORM_REFERER = PLATFORM_REFERER;
    initializePlatformLayer(PLATFORM_API_URL);
    dispatch(ACTION_CREATORS.setPlatformSettings({ ...platformSettingsSingleton }));
  }, [platformSettingsSingleton]);

  const [credentialsSaved, setCredentialsSaved] = useState(false);

  // save enterprise api key to platform
  useEffect(() => {
    const saveCredentials = async () => {
      if (!isInitialPlatformSettingsReady) return;
      const queryString = window.location.search;
      const params = new URLSearchParams(queryString);
      const apiKeyParam = params.get('code');
      if (apiKeyParam === '0') {
        dispatch(ACTION_CREATORS.setUnauthorizedApiKey(true)); // some users are not able obtain api key on enterprise server
        setCredentialsSaved(true);
        return;
      }
      if (!apiKeyParam) {
        setCredentialsSaved(true);
        return;
      }
      let payload = { key: 'apiKey', value: apiKeyParam };
      if (params.get('domain')) {
        const domain = new URL(params.get('domain')).hostname;
        payload = { key: 'apiKey|enterpriseDomain', value: `${apiKeyParam}|${domain}` };
      }
      const dataApiKey = { action: 'update', ...payload };
      await asyncDispatch(
        () => interactWithPlatform(dataApiKey),
        ACTION_CREATORS.saveProviderApiKeyRequest,
        ACTION_CREATORS.saveProviderApiKeySuccess,
        ACTION_CREATORS.saveProviderApiKeyError
      );
      await awaitFor(1000);
      toggleSettingsItems({ action: 'show' });
      setCredentialsSaved(true);
      cleanParams(['code', 'domain']);
    };
    saveCredentials();
  }, [isInitialPlatformSettingsReady]);

  // get platform settings
  useEffect(() => {
    if (!credentialsSaved) return;
    const fetchPlatformSettings = async () => {
      const data = { action: 'createSettings' };
      await asyncDispatch(
        () => interactWithPlatform(data),
        ACTION_CREATORS.getPlatformSettingsRequest,
        ACTION_CREATORS.getPlatformSettingsSuccess,
        ACTION_CREATORS.getPlatformSettingsError
      );
    };
    fetchPlatformSettings();
  }, [credentialsSaved]);

  // fetch user
  useEffect(() => {
    if (!PROVIDER_API_KEY) return;
    const fetchUserAsync = async () => {
      await asyncDispatch(
        () => fetcUser(PROVIDER_API_KEY),
        ACTION_CREATORS.fetchUserRequest,
        ACTION_CREATORS.fetchUserSuccess,
        ACTION_CREATORS.fetchUserError
      );
    };
    fetchUserAsync();
  }, [PROVIDER_API_KEY, refetchUser]);

  // fetch limit warnings on material add
  useEffect(() => {
    if (!PROVIDER_API_KEY) return;
    const fetchLimitWarningsAsync = async () => {
      await asyncDispatch(
        () => fetchUserLimitWarnings(PROVIDER_API_KEY),
        ACTION_CREATORS.fetchLimitWarningsRequest,
        ACTION_CREATORS.fetchLimitWarningsSuccess,
        ACTION_CREATORS.fetchLimitWarningsError
      );
    };
    fetchLimitWarningsAsync();
  }, [PROVIDER_API_KEY, refetchLimitWarnings]);

  return (
    <WizardContext.Provider
      value={contextValue}
    >
      {children}
    </WizardContext.Provider>
  );
};

WizardProvider.propTypes = {
  user: shape({}),
  children: node.isRequired
};
