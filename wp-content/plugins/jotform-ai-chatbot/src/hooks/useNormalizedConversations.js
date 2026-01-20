import { useMemo } from 'react';

export const useNormalizedConversations = (conversations, chats) => useMemo(() => {
  if (!Array.isArray(conversations) || !chats || typeof chats !== 'object') {
    return [];
  }

  return conversations.map(convo => {
    const convoId = convo.id;
    const chatData = chats[convoId];

    if (!chatData || !Array.isArray(chatData.chatHistory)) {
      return { ...convo, lastMessage: null };
    }

    const lastUserMessage = [...chatData.chatHistory]
      .reverse()
      .find(message => message.type === 'USER');

    return {
      ...convo,
      lastMessage: lastUserMessage?.content || null
    };
  });
}, [conversations, chats]);
