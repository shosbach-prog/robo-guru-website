import React, { useCallback, useEffect } from 'react';

import {
  disconnectWoocommerceStore as disconnectWoocommerceStoreReq,
  getWoocommerceSettings as getWoocommerceSettingsReq,
  setWoocommerceSettings as setWoocommerceSettingsReq,
  updateWoocommerceSettings as updateWoocommerceSettingsReq
} from '../../api';
import { ALL_TEXTS } from '../../constants';
import { useEffectIgnoreFirst, useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { t } from '../../utils';
import Loading from './Loading';
import Abilities from './woocommerce/Abilities';
import ActivationInfoBox from './woocommerce/ActivationInfoBox';
import ConnectedStore from './woocommerce/ConnectedStore';
import LocalhostError from './woocommerce/LocalhostError';
import StoreConnection from './woocommerce/StoreConnection';

const Woocommerce = () => {
  const {
    dispatch, asyncDispatch, state
  } = useWizard();

  const {
    previewAgentId,
    platformSettings: {
      PROVIDER_API_KEY, PLATFORM_URL,
      PLATFORM_WOOCOMMERCE_AVAILABLE,
      PLATFORM_PERMALINK_STRUCTURE,
      PLATFORM_DOMAIN
    },
    woocommerce: {
      consumerKey, abilities, isConnected, isSettingsLoading, isConnectLoading, invalidCredentialsError
    }
  } = state;

  const isLocalhost = PLATFORM_DOMAIN === 'localhost';
  const integrationOptions = Object.keys(abilities).filter(abilityKey => abilities[abilityKey] === true);

  useEffect(() => {
    const timer = setTimeout(() => {
      dispatch(ACTION_CREATORS.resetInvalidCredentialsError());
    }, 3000);
    return () => clearTimeout(timer);
  }, [invalidCredentialsError]);

  const setWoocommerceSettings = useCallback(
    async ({ key = '', secret = '' } = {}) => {
      const data = {
        ...(key && { consumerKey: key }),
        ...(secret && { consumerSecret: secret }),
        storeUrl: window.location.hostname,
        agentId: previewAgentId,
        integrationOptions
      };
      await asyncDispatch(
        () => setWoocommerceSettingsReq(data, PROVIDER_API_KEY),
        ACTION_CREATORS.setWoocommerceSettingsRequest,
        ACTION_CREATORS.setWoocommerceSettingsSuccess,
        ACTION_CREATORS.setWoocommerceSettingsError
      );
    },
    [consumerKey, previewAgentId, integrationOptions]
  );

  const updateWoocommerceSettings = useCallback(
    async () => {
      const data = {
        storeUrl: window.location.hostname,
        integrationOptions
      };
      await asyncDispatch(
        () => updateWoocommerceSettingsReq(previewAgentId, data, PROVIDER_API_KEY),
        ACTION_CREATORS.setWoocommerceSettingsRequest,
        ACTION_CREATORS.setWoocommerceSettingsSuccess,
        ACTION_CREATORS.setWoocommerceSettingsError
      );
    },
    [previewAgentId, integrationOptions]
  );

  const getWoocommerceSettings = useCallback(async () => {
    if (!PLATFORM_WOOCOMMERCE_AVAILABLE) return;
    await asyncDispatch(
      () => getWoocommerceSettingsReq(previewAgentId, window.location.hostname, PROVIDER_API_KEY),
      ACTION_CREATORS.getWoocommerceSettingsRequest,
      ACTION_CREATORS.getWoocommerceSettingsSuccess,
      ACTION_CREATORS.getWoocommerceSettingsError
    );
  }, [previewAgentId, PLATFORM_WOOCOMMERCE_AVAILABLE]);

  const disconnectWoocommerceStore = useCallback(async () => {
    const data = {
      storeUrl: window.location.hostname,
      agentId: previewAgentId
    };
    await asyncDispatch(
      () => disconnectWoocommerceStoreReq(data, PROVIDER_API_KEY),
      ACTION_CREATORS.disconnectWoocommerceStoreRequest,
      ACTION_CREATORS.disconnectWoocommerceStoreSuccess,
      ACTION_CREATORS.disconnectWoocommerceStoreError
    );
  }, [previewAgentId]);

  useEffect(() => {
    getWoocommerceSettings();
  }, []);

  useEffectIgnoreFirst(() => {
    updateWoocommerceSettings();
  }, [abilities]);

  return (
    <div className='jfpContent-wrapper--settings-options-wrapper'>
      <h2 className='jfpContent-wrapper--settings-options-wrapper-title'>
        {t(ALL_TEXTS.WOOCOMMERCE_STORE_SETTINGS)}
      </h2>
      {isLocalhost && <LocalhostError />}
      {!isLocalhost && !PLATFORM_WOOCOMMERCE_AVAILABLE && <ActivationInfoBox />}
      {PLATFORM_WOOCOMMERCE_AVAILABLE && isSettingsLoading && <Loading />}
      {PLATFORM_WOOCOMMERCE_AVAILABLE && !isSettingsLoading && (
        <>
          {!isConnected && (
            <StoreConnection
              previewAgentId={previewAgentId}
              platformUrl={PLATFORM_URL}
              isConnectLoading={isConnectLoading}
              invalidCredentialsError={invalidCredentialsError}
              permalinkStructure={PLATFORM_PERMALINK_STRUCTURE}
              setWoocommerceSettings={setWoocommerceSettings}
            />
          )}
          {isConnected && <ConnectedStore disconnectStore={disconnectWoocommerceStore} />}
        </>
      )}
      <Abilities
        isConnected={isConnected}
      />
    </div>
  );
};

export default Woocommerce;
