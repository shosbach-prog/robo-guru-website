import React, { useCallback, useEffect, useMemo } from 'react';

import {
  getAllAgents, getPlatformAgent, interactWithPlatform,
  saveAgentId
} from '../api';
import { PLATFORMS, STEPS } from '../constants';
import { useWizard } from '../hooks';
import { ACTION_CREATORS } from '../store';
import {
  awaitFor, createEmbed, getRootElement, isConversationsPage, isGuest, isSettingsPage, resetAgentPreviewRoot,
  toggleConversationItems
} from '../utils';
import Footer from './Footer';
import Header from './Header';
import { LimitWarningBanner } from './LimitWarningBanner';
import { openLimitDialog } from './openLimitDialog';
import Preview from './Preview';
import {
  AiPersonaStep, ConversationsStep, InitialStep,
  KnowledgeStep, LoadingStep, StyleStep, UseCaseStep,
  VisibilityStep
} from './Steps';
import SettingsStep from './Steps/SettingsStep';
import WhatsNewModal from './WhatsNewModal';
import WizardTabs from './WizardTabs';

const Wizard = props => {
  const { state, dispatch, asyncDispatch } = useWizard();

  const {
    user,
    step,
    previewAgentId,
    customizations,
    themeCustomizations,
    isInitialLoading,
    isUseAgentLoading,
    tryGetPlatformAgentOnce,
    isInitialPlatformSettingsReady,
    isPlatformSettingsLoading,
    isLimitDialogVisible,
    allAgents: {
      loading: allAgentsLoading
    },
    platformSettings: {
      PLATFORM_DOMAIN,
      PROVIDER_URL,
      PROVIDER_API_KEY,
      PLATFORM_PLUGIN_VERSION
    }
  } = state;

  // read current step from URL
  useEffect(() => {
    if (!previewAgentId) return;
    const searchParams = new URLSearchParams(window.location.href);
    const urlStep = searchParams.get('current_tab')?.toUpperCase();
    if (Object.values(STEPS).includes(urlStep)) {
      dispatch(ACTION_CREATORS.setStep(urlStep));
    }
  }, [previewAgentId]);

  // reset agent preview root & set step as query param
  useEffect(() => {
    if (step === STEPS.INITIAL) {
      resetAgentPreviewRoot();
    }
  }, [step]);

  // save agent id to platform
  useEffect(() => {
    if (previewAgentId) {
      saveAgentId(previewAgentId);
      toggleConversationItems({ action: 'show' });
    } else {
      toggleConversationItems({ action: 'hide' });
    }
  }, [previewAgentId]);

  useEffect(() => {
    if (!isLimitDialogVisible) return;
    try {
      openLimitDialog({
        utmContent: 'wordpress-plugin',
        container: getRootElement('#modal-root'),
        providerUrl: PROVIDER_URL,
        onClose: () => dispatch(ACTION_CREATORS.setIsLimitDialogVisible(false))
      });
    } catch (e) {
      dispatch(ACTION_CREATORS.setIsLimitDialogVisible(false));
    }
  }, [isLimitDialogVisible]);

  // fetch agent
  const fetchAgent = useCallback(async () => {
    let nextStep = STEPS.AI_PERSONA;
    if (isConversationsPage()) {
      nextStep = STEPS.CONVERSATIONS;
    }
    if (isSettingsPage()) {
      nextStep = STEPS.SETTINGS;
    }
    const data = { domain: PLATFORM_DOMAIN, platform: PLATFORMS.WORDPRESS };
    await asyncDispatch(
      () => getPlatformAgent(data, PROVIDER_API_KEY),
      ACTION_CREATORS.getPlatformAgentRequest,
      ACTION_CREATORS.getPlatformAgentSuccess,
      ACTION_CREATORS.getPlatformAgentError,
      nextStep
    );
  }, [PROVIDER_API_KEY, PLATFORM_DOMAIN]);

  // fetch existing agents
  const fetchExistingAgents = useCallback(async () => {
    if (!PROVIDER_API_KEY) return;

    await asyncDispatch(
      () => getAllAgents(PROVIDER_API_KEY),
      ACTION_CREATORS.getAllAgentsRequest,
      ACTION_CREATORS.getAllAgentsSuccess,
      ACTION_CREATORS.getAllAgentsError
    );
  }, [PROVIDER_API_KEY]);

  // fetch platform agent & existing agents if user is logged in
  useEffect(() => {
    const initFlow = async () => {
      const shouldFetchAgent = user && !isGuest(user) && PROVIDER_API_KEY;
      if (!shouldFetchAgent) return;
      fetchExistingAgents();
      fetchAgent();
    };
    initFlow();
  }, [user, PROVIDER_API_KEY]);

  // try fetch platform agent once to handle 502 use agent error
  useEffect(() => {
    if (!tryGetPlatformAgentOnce) return;
    const awaitAndFetchAgent = async () => {
      await awaitFor(8000);
      await fetchAgent();
      dispatch(ACTION_CREATORS.setTryGetPlatformAgentOnce(false));
    };
    awaitAndFetchAgent();
  }, [tryGetPlatformAgentOnce]);

  // is spinner loader visible
  const isSpinnerLoaderVisible = useMemo(() => (
    (isPlatformSettingsLoading && !PROVIDER_API_KEY) || (PROVIDER_API_KEY
      && (isInitialLoading
        || !isInitialPlatformSettingsReady
        || allAgentsLoading))), [
    PROVIDER_API_KEY,
    isInitialLoading,
    allAgentsLoading,
    isPlatformSettingsLoading,
    isInitialPlatformSettingsReady
  ]);

  // is agent loader visible
  const isAgentLoaderVisible = useMemo(() => (
    isUseAgentLoading || tryGetPlatformAgentOnce), [
    isUseAgentLoading,
    tryGetPlatformAgentOnce
  ]);

  const isLoaderVisible = isSpinnerLoaderVisible || isAgentLoaderVisible;

  // save platform agent embed
  const getPublishAgentRequest = ({ key = 'embed' }) => {
    const embedCode = createEmbed({
      agentId: previewAgentId, chatbotDomain: PROVIDER_URL, ...customizations, ...themeCustomizations
    });
    const dataEmbed = {
      action: 'update',
      key,
      ...(['embed', 'preview'].includes(key) && { value: global.btoa(global.encodeURIComponent(embedCode)) })
    };
    return () => interactWithPlatform(dataEmbed);
  };

  const publishAgent = async ({ key = 'embed' }) => {
    await asyncDispatch(
      () => getPublishAgentRequest({ key })(),
      ACTION_CREATORS.publishAgentRequest,
      ACTION_CREATORS.publishAgentSuccess,
      ACTION_CREATORS.publishAgentError,
      key
    );
  };

  const unpublishAgent = async () => {
    await publishAgent({ key: 'unpublish' });
    dispatch(ACTION_CREATORS.setIsPublished(false));
  };

  // all steps appear in this order
  const stepMap = {
    [STEPS.INITIAL]: InitialStep,
    [STEPS.USECASE_SELECTION]: UseCaseStep,
    [STEPS.VISIBILITY]: VisibilityStep,
    [STEPS.AI_PERSONA]: AiPersonaStep,
    [STEPS.STYLE]: StyleStep,
    [STEPS.KNOWLEDGE]: KnowledgeStep,
    [STEPS.CONVERSATIONS]: ConversationsStep,
    [STEPS.SETTINGS]: SettingsStep
  };

  const CurrentStep = stepMap[step];

  return (
    <>
      <LimitWarningBanner />
      {[STEPS.AI_PERSONA, STEPS.STYLE, STEPS.VISIBILITY, STEPS.KNOWLEDGE, STEPS.CONVERSATIONS, STEPS.SETTINGS].includes(step)
        && <Header publishAgent={publishAgent} unpublishAgent={unpublishAgent} />}
      <div
        data-step={step}
        className={`jfpChatbot-container jfpChatbot-container--platformmode${isLoaderVisible ? ' wizard-loading' : ''}`}
      >
        {isLoaderVisible && <LoadingStep key='loading-step' type={isAgentLoaderVisible ? 'text' : 'default'} />}
        {!isLoaderVisible && (
          <>
            <div className='jfpContent-wrapper' data-step={step}>
              {![STEPS.INITIAL, STEPS.USECASE_SELECTION, STEPS.CONVERSATIONS, STEPS.SETTINGS].includes(step) && <WizardTabs />}
              <CurrentStep {...props} unpublishAgent={unpublishAgent} />
            </div>
            {![STEPS.CONVERSATIONS, STEPS.SETTINGS].includes(step) && <Preview />}
          </>
        )}
      </div>
      <Footer platformDomain={PLATFORM_DOMAIN} platformPluginVersion={PLATFORM_PLUGIN_VERSION} />
      <WhatsNewModal isOpen onCloseClick={f => f} onOkClick={f => f} />
    </>
  );
};

export default Wizard;
