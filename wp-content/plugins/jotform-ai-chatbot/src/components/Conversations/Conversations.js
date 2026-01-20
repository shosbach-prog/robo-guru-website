import React, { forwardRef } from 'react';
import cx from 'classnames';
import {
  arrayOf, bool, func, object
} from 'prop-types';

import AvatarPlaceholder from '../../assets/svg/user-no-avatar.svg';
import { ALL_TEXTS } from '../../constants';
import { useNormalizedConversations } from '../../hooks';
import { getElapsedTime } from '../../utils';

const Conversations = forwardRef(({
  conversations, chats, currentConversationId, loading, onConversationClick
}, ref) => {
  const normalizedConversations = useNormalizedConversations(conversations, chats);

  const onClick = id => {
    onConversationClick(id);
  };

  return (
    <div ref={ref} className='jfpContent-wrapper--conversations-users-wrapper'>
      {normalizedConversations.map(({
        id,
        lastMessage,
        updated_at: updatedAt,
        answers: { chat_filler: { answer: { fullName = ALL_TEXTS.ANONYMOUS, avatarURL, name } = {} } = {} }
      }) => (
        <button
          key={id}
          type='button'
          onClick={() => onClick(id)}
          className={cx('jfpContent-wrapper--conversations-users-btn', {
            isActive: currentConversationId === id
          })}
        >
          <div className='jfpContent-wrapper--conversations-users-btn-img'>
            {avatarURL ? <img src={avatarURL} alt='user avatar' /> : <AvatarPlaceholder />}
          </div>
          <div className='jfpContent-wrapper--conversations-users-btn-name-cont'>
            <h4 className='jfpContent-wrapper--conversations-users-btn-name'>{fullName || name}</h4>
            <p className='jfpContent-wrapper--conversations-users-btn-message'>
              {lastMessage || '—'}
            </p>
          </div>
          <div className='jfpContent-wrapper--conversations-users-btn-date'>{getElapsedTime(updatedAt)}</div>
        </button>
      ))}
      {loading
        && (
          <div className='jfpContent-wrapper--conversations-users-loader'>
            <div className='create-page-loading--spinner xsmall' />
          </div>
        )}
    </div>
  );
});

Conversations.displayName = 'Conversations';

Conversations.propTypes = {
  conversations: arrayOf(object),
  chats: object,
  currentConversationId: bool,
  loading: bool,
  onConversationClick: func
};

export default Conversations;
