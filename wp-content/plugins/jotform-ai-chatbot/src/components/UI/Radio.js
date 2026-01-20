import React from 'react';
import { bool, func, string } from 'prop-types';

import '../../styles/radio.scss';

const Radio = ({
  checked, label, onChange, ...props
}) => (
  <label className='jfRadio'>
    <span className='jfRadio--checkmark'>
      <input
        type='radio'
        checked={checked}
        onChange={onChange}
        {...props}
      />
      <span className='jfRadio--checkmark-inner' />
    </span>
    <span className='jfRadio--label'>{label}</span>
  </label>
);

Radio.propTypes = {
  checked: bool.isRequired,
  label: string.isRequired,
  onChange: func.isRequired
};

export default Radio;
