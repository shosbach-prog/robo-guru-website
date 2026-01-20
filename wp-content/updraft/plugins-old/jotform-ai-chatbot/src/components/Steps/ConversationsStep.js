import React, {
  useCallback, useEffect, useMemo, useRef, useState
} from 'react';

import { fetchChats, fetchConversations, saveInstallment } from '../../api';
import { ALL_TEXTS } from '../../constants';
import { useInfiniteScroll, useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { awaitFor, t, toCamelCase } from '../../utils';
import { ChatHistory, Conversations } from '../Conversations';
import { IconExclamationCircleFilled } from '../UI/Icon';

const ConversationsStep = () => {
  const {
    dispatch, asyncDispatch, state
  } = useWizard();

  const {
    step,
    previewAgentId,
    activeViewId,
    platformSettings: { PROVIDER_URL, PROVIDER_API_KEY },
    conversations,
    chats,
    allAgents: { items: existingAgents }
  } = state;

  const CONVERSATIONS_LIMIT = 10;

  const { items: chatItems } = chats;
  const {
    items: conversationItems, archivedItems: archivedConversationItems, loading, lastUUID, allConversationsFetched
  } = conversations;

  const containerRef = useRef(null);

  const [currentConversationId, setCurrentConversationId] = useState('');

  useEffect(() => {
    const triggerDelayedConversationsLoading = async () => {
      if (!loading) return;
      await awaitFor(1500);
      dispatch(ACTION_CREATORS.setFetchConversationsLoading(false));
    };
    triggerDelayedConversationsLoading();
  }, [conversationItems]);

  useEffect(() => {
    saveInstallment(`${toCamelCase(step)}Page`);
  }, []);

  useEffect(() => {
    if (!conversationItems.length) return;
    setCurrentConversationId(conversationItems[0].id);
  }, [conversationItems, chatItems]);

  // fetch conversations
  const getConversations = useCallback(async () => {
    const data = { conversationsLimit: CONVERSATIONS_LIMIT, lastUUID };
    await asyncDispatch(
      () => fetchConversations(previewAgentId, activeViewId, data, PROVIDER_API_KEY),
      ACTION_CREATORS.fetchConversationsRequest,
      ACTION_CREATORS.fetchConversationsSuccess,
      ACTION_CREATORS.fetchConversationsError
    );
  }, [previewAgentId, lastUUID]);

  // initial fetch
  useEffect(() => {
    if (allConversationsFetched) return;
    getConversations();
  }, []);

  // infinite scroll
  useInfiniteScroll(containerRef, {
    loading,
    hasMore: !allConversationsFetched,
    onLoadMore: getConversations
  });

  useEffect(() => {
    const getChats = async () => {
      if (!conversationItems.length || allConversationsFetched) return;
      const lastGroup = conversationItems.slice(conversationItems.length > CONVERSATIONS_LIMIT ? conversationItems.length - CONVERSATIONS_LIMIT : 0);
      const conversationIds = lastGroup.map(conv => conv.id);
      await asyncDispatch(
        () => fetchChats(previewAgentId, activeViewId, conversationIds, PROVIDER_API_KEY),
        ACTION_CREATORS.fetchChatsRequest,
        ACTION_CREATORS.fetchChatsSuccess,
        ACTION_CREATORS.fetchChatsError
      );
    };
    getChats();
  }, [conversationItems]);

  const handleConversationClick = id => {
    setCurrentConversationId(id);
  };

  function getConversationFullName(conItems, currentConId) {
    const item = conItems.find(i => i.id === currentConId);
    return (
      item?.answers?.chat_filler?.answer?.fullName
      || item?.answers?.chat_filler?.answer?.name
      || ALL_TEXTS.ANONYMOUS
    );
  }

  const currentChatName = getConversationFullName(conversationItems, currentConversationId);

  const handleSeeConversationClick = async e => {
    e.preventDefault();
    saveInstallment('seeConversationOnJotformButton');
    await awaitFor(1000);
    window.open(e.target?.href, '_blank');
  };

  const conversationCount = useMemo(() => {
    const agent = existingAgents.find(item => item.uuid === previewAgentId);
    const total = agent?.totalConversationCount ?? 0;
    const archivedCount = Array.isArray(archivedConversationItems) ? archivedConversationItems.length : 0;
    const count = total - archivedCount;
    return count > 0 ? count : '0';
  }, [existingAgents, previewAgentId, archivedConversationItems]);

  return (
    <div className='jfpContent-wrapper--conversations'>
      <div className='jfpContent-wrapper--conversations-users'>
        <div className='jfpContent-wrapper--conversations-users-title'>
          <h2 className='jfpContent-wrapper--conversations-users-title-content' aria-hidden='true'>{t(ALL_TEXTS.CONVERSATIONS)}</h2>
          <span className='jfpContent-wrapper--conversations-users-title-counter'>Showing <span>{conversationItems.length}</span> of <span>{conversationCount}</span> conversation</span>
        </div>
        <Conversations
          ref={containerRef}
          chats={chatItems}
          conversations={conversationItems}
          currentConversationId={currentConversationId}
          onConversationClick={handleConversationClick}
          loading={loading}
        />
        {(!conversationItems.length && !loading)
          && (
            <div className='jfpContent-wrapper--conversations-users-empty'>
              <IconExclamationCircleFilled />
              <span>{t(ALL_TEXTS.CONVERSATIONS_WILL_BE_LISTED_HERE)}</span>
            </div>
          )}
      </div>
      <div className='jfpContent-wrapper--conversations-chats'>
        <div className='jfpContent-wrapper--conversations-chats-title'>
          {!!conversationItems.length
            && (
              <>
                <h2 className='jfpContent-wrapper--conversations-chats-title-username'>{currentChatName}</h2>
                <div className='jfpContent-wrapper--conversations-chats-title-right'>
                  <a
                    target='_blank'
                    rel='noreferrer'
                    href={`${PROVIDER_URL}/conversations/${activeViewId}`}
                    className='jfpContent-wrapper--conversations-chats-title-right-see-link'
                    onClick={handleSeeConversationClick}
                  >
                    {t(ALL_TEXTS.SEE_CONVERSATIONS_ON_JOTFORM)}
                  </a>
                </div>
              </>
            )}
        </div>
        <ChatHistory chat={chatItems[currentConversationId]} />
      </div>
    </div>

  );
};

export default ConversationsStep;
