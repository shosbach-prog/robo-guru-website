import React, { useEffect, useState } from 'react';
import { func } from 'prop-types';
import { createPortal } from 'react-dom';

import { saveInstallment } from '../api';
import { ALL_TEXTS, STEP_TO_BUILDER_PATH, STEPS } from '../constants';
import { usePublishButton, useWizard } from '../hooks';
import { STAGES } from '../hooks/usePublishButton';
import {
  awaitFor, platformSettings, t
} from '../utils';
import Button from './UI/Button';
import { IconArrowUpRight, IconEyeFilled } from './UI/Icon';
import UnpublishModal from './UnpublishModal';

const Header = ({ publishAgent, unpublishAgent }) => {
  const { state } = useWizard();

  const {
    step,
    isPublished,
    previewAgentId,
    isPublishLoading,
    platformSettings: { PLATFORM_PREVIEW_URL, PROVIDER_URL }
  } = state;

  const [buttonWrappeRoot, setButtonWrapperRoot] = useState(null);
  const [isLivePreviewLoading, setIsLivePreviewLoading] = useState(false);
  const [isUnpublishModalOpen, setIsUnpublishModalOpen] = useState(false);

  const { buttonProps, startPublish, resetToUnpublished } = usePublishButton(isPublished ? STAGES.PUBLISHED : STAGES.UNPUBLISHED);

  const goToJotformUrl = `${PROVIDER_URL}/agent/build/${previewAgentId}${STEP_TO_BUILDER_PATH[step]}`;

  useEffect(() => {
    const removeItems = document.querySelectorAll('.jotform-ai-chatbot-logo > *:not(.jf-logo):not(.jf-title)');
    removeItems.forEach(el => el.remove());
  }, []);

  const handleJotformLink = async () => {
    saveInstallment('goToJotformButton');
  };

  const handleLivePreviewClick = async () => {
    const { PLATFORM_NONCE = '' } = platformSettings;
    saveInstallment('previewButton');
    setIsLivePreviewLoading(true);
    publishAgent({ key: 'preview' });
    await awaitFor(1000); // give some time to wordpress to process the request
    setIsLivePreviewLoading(false);
    window.open(`${PLATFORM_PREVIEW_URL}&_nonce=${PLATFORM_NONCE}`, '_blank');
  };

  const handlePublishClick = async () => {
    saveInstallment('publishButton');
    startPublish();
    await publishAgent({ key: 'embed' });
  };

  const handleUnpublishClick = async () => {
    saveInstallment('unpublishButton');
    await unpublishAgent();
    resetToUnpublished();
    setIsUnpublishModalOpen(false);
  };

  useEffect(() => {
    setButtonWrapperRoot(document.querySelector('#button-wrapper-root'));
  }, []);

  if (!buttonWrappeRoot || !previewAgentId) return null;

  return createPortal(
    <div className='chatbot-header-cta-cont' role='toolbar' aria-label='Chatbot Actions'>
      {/* go to jotform button */}
      {step !== STEPS.CONVERSATIONS && (
        <Button
          variant='ghost'
          colorStyle='secondary'
          className='go-to-jotform-cta'
          endIcon={<IconArrowUpRight />}
          onClick={handleJotformLink}
          href={goToJotformUrl}
          target='_blank'
        >
          {t(ALL_TEXTS.GO_TO_JOTFORM)}
          <span className='sr-only'>(opens in a new tab)</span>
        </Button>
      )}
      {/* preview button */}
      <div className='mobile-cont'>
        <Button
          variant='outline'
          colorStyle='secondary'
          startIcon={<IconEyeFilled />}
          loader={isLivePreviewLoading}
          onClick={handleLivePreviewClick}
        >
          {t(ALL_TEXTS.PREVIEW)}
          <span className='sr-only'>(opens in a new tab)</span>
        </Button>
        {/* publish button */}
        <Button
          colorStyle={buttonProps.colorStyle}
          disabled={buttonProps.disabled}
          variant={buttonProps.variant}
          className={`publish-cta${isPublished ? '' : ' isPulseAnimation'}`}
          onClick={isPublished ? () => setIsUnpublishModalOpen(true) : handlePublishClick}
          {...(isPublished && {
            'aria-haspopup': 'dialog',
            'aria-expanded': isUnpublishModalOpen
          })}
          style={{ opacity: buttonProps.opacity }}
          aria-live='polite'
        >
          {buttonProps.text}
        </Button>
        <UnpublishModal
          isOpen={isUnpublishModalOpen}
          onUnpublishClick={handleUnpublishClick}
          onCloseClick={() => setIsUnpublishModalOpen(false)}
          isPublished={isPublished}
          isPublishLoading={isPublishLoading}
        />
      </div>
    </div>,
    buttonWrappeRoot
  );
};

Header.propTypes = {
  publishAgent: func.isRequired,
  unpublishAgent: func.isRequired
};

export default Header;
