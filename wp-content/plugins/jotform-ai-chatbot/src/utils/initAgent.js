import { PLATFORMS } from '../constants';
import { platformSettings } from './platformSingleton';
import { getEmbedSource } from './utils';

let scriptLoaded = false;

const loadAgentEmbedScript = async (onLoad = f => f) => {
  if (scriptLoaded) {
    onLoad();
    return;
  }

  const script = document.createElement('script');
  script.src = getEmbedSource();
  script.async = true;
  script.onload = () => {
    scriptLoaded = true;
    onLoad();
  };
  document.body.appendChild(script);
};

export const initAgent = async ({
  agentId,
  customizations = {},
  customAvatarUrl: customAvatarURL = '',
  agentBackgroundStart,
  agentBackgroundEnd,
  sendButtonBackground,
  sendButtonIconColor
}) => {
  const { PLATFORM, PROVIDER_URL } = platformSettings;

  loadAgentEmbedScript(() => {
    if (global.AgentInitializer && agentId) {
      global.AgentInitializer.init({
        rootId: 'agent-preview-root',
        formID: agentId,
        queryParams: ['skipWelcome=1', 'isAIAgentAllowed=1'],
        domain: Object.values(PLATFORMS).includes(PLATFORM) ? PROVIDER_URL : global.location.origin,
        isInitialOpen: global.innerWidth > 1024,
        isDraggable: false,
        background: `linear-gradient(180deg, ${agentBackgroundStart} 0%, ${agentBackgroundEnd} 100%)`,
        buttonBackgroundColor: sendButtonBackground,
        buttonIconColor: sendButtonIconColor,
        variant: false,
        ...({ customAvatarURL } && { customAvatarURL }),
        customizations: {
          ...customizations
          // greeting: 'Yes',
          // greetingMessage: 'Hi! How can I assist you?',
          // pulse: 'Yes',
          // position: 'right',
          // layout: 'extended'
        }
      });
    }
  });
};
