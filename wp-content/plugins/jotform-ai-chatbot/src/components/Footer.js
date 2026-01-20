import React, { useState } from 'react';
import { string } from 'prop-types';

import { saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { t } from '../utils';
import SupportModal from './SupportModal';
import Button from './UI/Button';
import { IconHeadset } from './UI/Icon';

const Footer = ({ platformDomain, platformPluginVersion }) => {
  const domainQuery = `?domain=${platformDomain}&plugin_version=${platformPluginVersion}`;
  const [isSupportModalOpen, setIsSupportModalOpen] = useState(false);

  const handleHowToUseClick = e => {
    e.preventDefault();
    saveInstallment('howToUseJotformAiChatbotButton');
    window.open(e.target.href, '_blank');
  };

  const handleGetSupportClick = () => {
    setIsSupportModalOpen(true);
    saveInstallment('getSupportButton');
  };

  const handleGoToSupportPageClick = e => {
    e.preventDefault();
    const feedbackUrl = `https://link.jotform.com/YmN9tHjSBA${domainQuery}`;
    window.open(feedbackUrl, '_blank');
    setIsSupportModalOpen(false);
    saveInstallment('goToSupportPageButton');
  };

  return (
    <footer className='chatbot-footer'>
      <a
        className='how-to-use-link'
        href='https://link.jotform.com/NTVCqmVoHv'
        target='_blank'
        rel='noreferrer'
        onClick={handleHowToUseClick}
        aria-label='How to use Jotform AI Chatbot? (opens in new tab)'
      >
        {(t(ALL_TEXTS.HOW_TO_USE_JOTFORM_AI_CHATBOT))}
      </a>
      <Button
        startIcon={<IconHeadset />}
        variant='outline'
        colorStyle='secondary'
        onClick={handleGetSupportClick}
      >
        {(t(ALL_TEXTS.GET_SUPPORT))}
      </Button>
      <SupportModal
        isModalVisible={isSupportModalOpen}
        onCloseClick={() => setIsSupportModalOpen(false)}
        onGotoSupportPageClick={handleGoToSupportPageClick}
      />
    </footer>
  );
};

Footer.propTypes = {
  platformDomain: string.isRequired,
  platformPluginVersion: string.isRequired
};

export default Footer;
