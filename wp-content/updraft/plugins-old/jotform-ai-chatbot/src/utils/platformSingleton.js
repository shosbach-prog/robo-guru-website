class PlatformSingleton {
  constructor() {
    this.PROVIDER_API_KEY = '';
    this.PROVIDER_API_URL = '';
    this.PROVIDER_URL = '';
    this.PROVIDER_CHATBOT_EMBED_SRC = '';
    this.PLATFORM = '';
    this.PLATFORM_URL = '';
    this.PLATFORM_API_URL = '';
    this.PLATFORM_DOMAIN = '';
    this.PLATFORM_PAGES = [];
    this.PLATFORM_CHATBOT_PAGES = [];
    this.PLATFORM_PAGE_CONTENTS = [];
    this.PLATFORM_PREVIEW_URL = '';
    this.PLATFORM_NONCE = '';
    this.PLATFORM_REFERER = '';
    this.PLATFORM_KNOWLEDGE_BASE = { urls: [] };
    this.PLATFORM_DEVICE = '';
    this.PLATFORM_CHATBOT_PUBLISHED = false;
    this.PLATFORM_PLUGIN_VERSION = '';
    this.PLATFORM_WOOCOMMERCE_AVAILABLE = false;
    this.PLATFORM_PERMALINK_STRUCTURE = '';
  }
}

export const platformSettings = new PlatformSingleton();
