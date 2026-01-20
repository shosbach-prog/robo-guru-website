import React from 'react';
import {
  bool, func, object, string
} from 'prop-types';

import '../../styles/agent-radio.scss';

const AgentRadio = ({
  checked, label, description, onChange, avatarImage, ...props
}) => (
  <label className='jfRadio'>
    <span className='jfRadio--checkmark'>
      <input
        type='radio'
        aria-label={label}
        checked={checked}
        onChange={onChange}
        {...props}
      />
      <span className='jfRadio--checkmark-inner' />
    </span>
    <img className='jfRadio--avatar-image' src={avatarImage} alt='Avatar' width='38px' aria-hidden='true' />
    <div className='jfRadio--text-wrapper' aria-hidden='true'>
      <p className='jfRadio--label'>{label}</p>
      <p className='jfRadio--description'>{description}</p>
    </div>
  </label>
);

AgentRadio.propTypes = {
  checked: bool.isRequired,
  label: string.isRequired,
  description: string.isRequired,
  avatar: object.isRequired,
  onChange: func.isRequired,
  avatarImage: string.isRequired
};

export default AgentRadio;
