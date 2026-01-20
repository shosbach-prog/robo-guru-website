// selection types
export const SELECTION_TYPE_LABELS = {
  URL: 'URL',
  PAGE: 'Page'
};

export const SELECTION_TYPE_VALUES = {
  URL: 'url',
  PAGE: 'page'
};

export const SELECTION_OPTIONS = [
  { label: SELECTION_TYPE_LABELS.URL, value: SELECTION_TYPE_VALUES.URL },
  { label: SELECTION_TYPE_LABELS.PAGE, value: SELECTION_TYPE_VALUES.PAGE }
];

// URL match types
export const URL_MATCH_TYPE_LABELS = {
  IS: 'Is',
  STARTS_WITH: 'Starts with'
};

export const URL_MATCH_TYPE_VALUES = {
  IS: 'is',
  STARTS_WITH: 'startsWith'
};

export const URL_MATCH_OPTIONS = [
  { label: URL_MATCH_TYPE_LABELS.IS, value: URL_MATCH_TYPE_VALUES.IS },
  { label: URL_MATCH_TYPE_LABELS.STARTS_WITH, value: URL_MATCH_TYPE_VALUES.STARTS_WITH }
];

export const VISIBILITY_TOGGLE = {
  SHOW_ON: { label: 'Show on', value: 'showOn' },
  HIDE_ON: { label: 'Hide on', value: 'hideOn' }
};

// Dummy available pages for selection
export const DUMMY_AVAILABLE_PAGES = [
  { label: 'Case Studies', value: '/case-studies' },
  { label: 'Blog', value: '/blog' },
  { label: 'Contact', value: '/contact' }
];
