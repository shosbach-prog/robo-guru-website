import React from 'react';
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import { object } from 'prop-types';

import ConversationEmpty from '../../assets/svg/conversation-empty.svg';
import AvatarPlaceholder from '../../assets/svg/user-no-avatar.svg';
import { ALL_TEXTS, THEME_CUSTOMIZATION_KEYS } from '../../constants';
import { useWizard } from '../../hooks';
import { formatDate, t } from '../../utils';

const ChatHistory = ({ chat }) => {
  const { state } = useWizard();

  const {
    themeCustomizations
  } = state;

  const getChatHTML = (content) => DOMPurify.sanitize(marked(content || ''));

  return (
    <div className='jfpContent-wrapper--conversations-chats-wrapper'>
      {chat?.chatHistory.map((msg) => {
        const isUser = msg.type === 'USER';
        const time = new Date(msg.created_at).toLocaleString();

        return (
          <div
            key={msg.uuid}
            className={`jfpContent-wrapper--conversations-chats-box ${isUser ? 'user' : 'agent'}`}
          >
            <div className='jfpContent-wrapper--conversations-chats-message-container'>
              <div className='jfpContent-wrapper--conversations-chats-avatar'>
                {msg.avatar_url && !['podo', 'gravatar'].some(sub => msg.avatar_url?.toLowerCase().includes(sub)) ? (
                  <img
                    src={msg.avatar_url}
                    alt='avatar'
                    style={!isUser
                      // eslint-disable-next-line max-len
                      ? { background: `linear-gradient(180deg, ${themeCustomizations[THEME_CUSTOMIZATION_KEYS.AGENT_BG_START_COLOR]} 0%, ${themeCustomizations[THEME_CUSTOMIZATION_KEYS.AGENT_BG_END_COLOR]} 100%)` }
                      : {}}
                  />
                ) : <AvatarPlaceholder />}
              </div>
              <div className='jfpContent-wrapper--conversations-chats-message-wrapper'>
                <span className='jfpContent-wrapper--conversations-chats-time'>{formatDate(time)}</span>
                <p className='jfpContent-wrapper--conversations-chats-message' dangerouslySetInnerHTML={{ __html: getChatHTML(msg.content) }} />
              </div>
            </div>
          </div>
        );
      })}
      {!chat
        && (
          <div className='jfpContent-wrapper--conversations-chats-noresult'>
            <ConversationEmpty className='jfpContent-wrapper--conversations-chats-noresult-icon' />
            <h3 className='jfpContent-wrapper--conversations-chats-noresult-title'>{t(ALL_TEXTS.YOU_DONT_HAVE_ANY_CONVERSATIONS)}</h3>
            <p className='jfpContent-wrapper--conversations-chats-noresult-desc'>{t(ALL_TEXTS.CONVERSATIONS_WILL_BE_LISTED_HERE)}</p>
          </div>
        )}
    </div>
  );
};

ChatHistory.propTypes = {
  chat: object
};

export default ChatHistory;
