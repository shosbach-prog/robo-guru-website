import React from 'react';
import { bool, func, string } from 'prop-types';

import '../../styles/toggle.scss';

const Toggle = ({ checked, onChange, ariaLabel }) => (
  <label className='chatbot-toggle' aria-label={ariaLabel}>
    <input
      className='chatbot-toggle--input'
      type='checkbox'
      checked={checked}
      onChange={onChange}
    />
    <span className='chatbot-toggle--slider' />
  </label>
);

Toggle.propTypes = {
  checked: bool.isRequired,
  onChange: func.isRequired,
  ariaLabel: string
};

export default Toggle;
