import { PLATFORMS } from '../constants';
import { platformSettings } from '../utils';
import { getPlatformLayer } from './platformLayerSingleton';
import { getRequestLayer } from './requestLayerSingleton';

const createFormData = data => {
  const formData = new FormData();
  Object.entries(data).forEach(([key, value]) => {
    formData.append(key, value);
  });
  return formData;
};

const addWpNonce = formData => {
  const { PLATFORM_NONCE, PLATFORM_REFERER } = platformSettings;
  const shouldAddNonce = PLATFORM_NONCE && PLATFORM_REFERER;
  if (!shouldAddNonce) return formData;
  formData.append('_nonce', PLATFORM_NONCE);
  formData.append('_wp_http_referer ', PLATFORM_REFERER);
  return formData;
};

const getBaseURL = () => {
  const { PLATFORM, PROVIDER_API_URL } = platformSettings;
  return (Object.values(PLATFORMS).includes(PLATFORM) ? PROVIDER_API_URL : '/API');
};

export const fetcUser = apiKey => getRequestLayer().get(`user?getUserFromWordpressChatbotPlugin=true&apikey=${apiKey}`);

export const addApiKeyToUrl = (url, apiKey = '') => {
  if (!apiKey) return url;

  const [base, query = ''] = url.split('?');
  const params = new URLSearchParams(query);
  params.set('apikey', apiKey);

  return `${base}?${params.toString()}`;
};

export const apiUsePlatformAgent = (params, apiKey = '') => {
  const url = addApiKeyToUrl('ai-chatbot/use-platform-agent', apiKey);
  return getRequestLayer().post(url, params);
};

export const getPlatformAgent = (params, apiKey = '') => {
  const url = addApiKeyToUrl('ai-chatbot/get-platform-agent', apiKey);
  return getRequestLayer().post(url, params);
};

export const interactWithPlatform = params => {
  let formData = createFormData(params);
  formData = addWpNonce(formData);
  const platformLayer = getPlatformLayer();
  return platformLayer.post('', formData);
};

export const saveAgentId = agentId => interactWithPlatform({ key: 'agentId', value: agentId, action: 'update' });

export const updateAgent = (agentId, params, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}`, apiKey);
  return getRequestLayer().put(url, params);
};

export const updateAgentProperty = (agentId, params, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/properties`, apiKey);
  return getRequestLayer().post(url, params);
};

export const getMaterialById = (agentId, materialId, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/materials/${materialId}`, apiKey);
  return getRequestLayer().get(url);
};

export const fetchMaterials = (agentId, apiKey) => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/materials`, apiKey);
  return getRequestLayer().get(url);
};

export const addMaterial = (agentId, material, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/materials`, apiKey);
  const config = material.type === 'DOCUMENT' ? { headers: { 'Content-Type': 'multipart/form-data' } } : {};
  return getRequestLayer().post(url, material, config);
};

export const updateMaterial = (agentId, materialId, updatedMaterial, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/materials/${materialId}`, apiKey);
  const config = updatedMaterial.type === 'DOCUMENT' ? { headers: { 'Content-Type': 'multipart/form-data' }, baseURL: getBaseURL() } : { baseURL: getBaseURL() };
  return getRequestLayer().post(url, updatedMaterial, config);
};

export const deleteMaterial = (agentId, materialId, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/materials/${materialId}`, apiKey);
  return getRequestLayer().delete(url);
};

export const bulkDeleteMaterial = (agentId, materialIds, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/materials/bulk_delete`, apiKey);
  return getRequestLayer().put(url, { id_list: [...materialIds] }, { isFormData: false });
};

export const getAvatars = (agentId, params, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/agents/${agentId}/avatars/gallery`, apiKey);
  return getRequestLayer().post(url, params);
};

export function getAIAgentsLimitExceeded(apiKey) {
  const url = addApiKeyToUrl('user-limit/ai-agents-limit-exceeded', apiKey);
  return getRequestLayer().get(url);
}

export const getSentenceRecommendations = string => getRequestLayer().post('chat/complete-sentence', new URLSearchParams({ prompt: string }));

export const getAllAgents = apiKey => getRequestLayer().get(`mixed-listing/assets?offset=0&limit=1000&orderby=created_at&status=active&assetTypes[0]=ai-agent&addAIAgents=1&apiKey=${apiKey}`);

export const setInstallment = (params, apiKey = '') => {
  const url = addApiKeyToUrl('ai-chatbot/installment', apiKey);
  return getRequestLayer()?.post(url, params);
};

export const fetchConversations = (agentId, activeViewId, params, apiKey = '') => {
  const queryParams = new URLSearchParams({
    ...params,
    orderby: 'created_at,desc',
    conversationsV2: '1',
    isWPAIChatbotPlugin: 'true',
    pagination: 'true'
  });
  const url = addApiKeyToUrl(`sheets/${activeViewId}/ai-agent/agent/${agentId}/conversations?${queryParams.toString()}`, apiKey);
  return getRequestLayer().get(url);
};

export const fetchChats = (agentId, activeViewId, conversations = [], apiKey = '') => {
  const params = new URLSearchParams();

  const filter = {
    'chat_id:in': conversations
  };

  params.append('filter', JSON.stringify(filter));
  params.append('allowMultipleActions', '1');
  params.append('no-leaddata', '1');

  const url = addApiKeyToUrl(`sheets/${activeViewId}/ai-agent/${agentId}/chats?${params.toString()}`, apiKey);

  return getRequestLayer().get(url);
};

export const setWoocommerceSettings = (params, apiKey = '') => {
  const url = addApiKeyToUrl('ai-chatbot/woocommerce/integration', apiKey);
  return getRequestLayer().post(url, params);
};

export const updateWoocommerceSettings = (agentId, params, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-chatbot/woocommerce/integration/${agentId}`, apiKey);
  return getRequestLayer().put(url, params);
};

export const getWoocommerceSettings = (agentId, storeUrl, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-chatbot/woocommerce/integration/${agentId}/${storeUrl}`, apiKey);
  return getRequestLayer().get(url);
};

export const disconnectWoocommerceStore = (params, apiKey = '') => {
  const url = addApiKeyToUrl('ai-chatbot/woocommerce/integration/disconnect', apiKey);
  return getRequestLayer().post(url, params);
};

export const fetchUserLimitWarnings = (apiKey = '') => {
  const url = addApiKeyToUrl('user-limit/limit-warnings', apiKey);
  return getRequestLayer().get(url);
};

export const uploadImageFile = (username, file, type, saveImg = true, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/${username}/uploads`, apiKey);
  return getRequestLayer().post(url, { file, type, ...(saveImg && { save: '1' }) }, { headers: { 'Content-Type': 'multipart/form-data' } });
};

export const removeImageBackground = (username, payload, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-agent-builder/user/${username}/avatars/remove-image-bg`, apiKey);
  return getRequestLayer().post(url, payload, { headers: { 'Content-Type': 'multipart/form-data' } });
};

export const getAgentAvatarAsBase64 = (agentId, apiKey = '') => {
  const url = addApiKeyToUrl(`ai-chatbot/agent-avatar/${agentId}`, apiKey);
  return getRequestLayer().get(url);
};
