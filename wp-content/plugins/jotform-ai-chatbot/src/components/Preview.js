import React, { useEffect } from 'react';

import introVideo from '../assets/videos/intro.mp4';
import { ALL_TEXTS, STEPS } from '../constants';
import { useWizard } from '../hooks';
import { initAgent, t } from '../utils';

const Preview = () => {
  const { state } = useWizard();

  const {
    step,
    previewAgentId,
    customizations,
    selectedAvatar,
    themeCustomizations,
    agentName,
    agentRole,
    agentChattiness,
    agentLanguage,
    agentToneOfVoice,
    persona,
    materials,
    refreshPreviewForAvatar
  } = state;

  useEffect(() => {
    const refreshAgent = async () => {
      if (!document.querySelector('#agent-preview-root')) return;

      initAgent({
        agentId: previewAgentId,
        customizations,
        customAvatarUrl: selectedAvatar.avatarIconLink,
        ...themeCustomizations
      });
    };

    refreshAgent();
  }, [
    refreshPreviewForAvatar,
    agentName,
    agentRole,
    agentLanguage,
    agentToneOfVoice,
    customizations,
    agentChattiness,
    persona,
    themeCustomizations,
    materials
  ]);

  return (
    <>
      {[STEPS.INITIAL, STEPS.USECASE_SELECTION].includes(step) ? (
        <div className='introduction'>
          <video
            autoPlay
            loop
            muted
            playsInline
            src={introVideo}
            title={t(ALL_TEXTS.ANIMATION_TITLE)}
          />
        </div>
      ) : (
        <div className='agent-preview'>
          <div className='agent-preview-bg' />
          <div id='agent-preview-root' />
        </div>
      )}
    </>
  );
};

export default Preview;
