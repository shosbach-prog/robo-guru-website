import React, { useMemo } from 'react';

import { ALL_TEXTS } from '../../../constants';
import { useWizard } from '../../../hooks';
import { t } from '../../../utils';
import Button from '../../UI/Button';

const ConnectedChatbot = () => {
  const { state } = useWizard();

  const { previewAgentId, allAgents: { items = [] } = {} } = state;

  const connectedAgent = useMemo(() => items.find(({ uuid }) => uuid === previewAgentId), [previewAgentId, items]);

  const handleRemoveChatbotClick = () => {
    console.log('Remove from website');
  };

  return (
    <div className='jfpContent-wrapper--settings-options-wrapper-connected'>
      <h3 className='jfpContent-wrapper--settings-options-wrapper-connected-title'>{t(ALL_TEXTS.CONNECTED_CHATBOT.toUpperCase())}</h3>
      <div className='jfpContent-wrapper--settings-options-wrapper-connected-wrapper'>
        <div className='jfpContent-wrapper--settings-options-wrapper-connected-content-wrapper'>
          <img
            src={connectedAgent?.avatarIconLink}
            alt='Agent Avatar'
            className='jfpContent-wrapper--settings-options-wrapper-connected-icon big full-radius'
          />
          <div className='jfpContent-wrapper--settings-options-wrapper-connected-content'>
            <strong>{connectedAgent?.title}</strong>
            <p>
              {connectedAgent?.totalConversationCount}{' '}
              {connectedAgent?.totalConversationCount > 1 ? 'conversations' : 'conversation'}
            </p>
          </div>
        </div>
        <Button
          colorStyle='error'
          variant='outline'
          size='small'
          onClick={handleRemoveChatbotClick}
          className='jfpContent-wrapper--settings-options-wrapper-connected-btn'
        >
          {t(ALL_TEXTS.REMOVE_FROM_WEBSITE_2)}
        </Button>
      </div>
    </div>
  );
};

export default ConnectedChatbot;
