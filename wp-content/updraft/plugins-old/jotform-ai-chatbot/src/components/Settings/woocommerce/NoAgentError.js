import React from 'react';

import { ALL_TEXTS } from '../../../constants';
import { useWizard } from '../../../hooks';
import { translationRenderer } from '../../../utils';

const NoAgentError = () => {
  const { state } = useWizard();
  const { platformSettings: { PLATFORM_URL } } = state;

  const handleClick = () => {
    window.location.href = `${PLATFORM_URL}/wp-admin/admin.php?page=jotform_ai_chatbot`;
  };

  return (
    <p className='jfpContent-wrapper--settings-options-input-error'>
      {translationRenderer(ALL_TEXTS.NO_AGENT_ERROR)({
        renderer1: str => (
          <button type='button' className='jfpContent-wrapper--settings-options-link-button' onClick={handleClick}>{str}</button>)
      })}
    </p>
  );
};

export default NoAgentError;
