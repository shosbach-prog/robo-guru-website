import React from 'react';
import classnames from 'classnames';
import {
  bool, func, oneOf, string
} from 'prop-types';

import '../../styles/checkbox.scss';

import { IconCheck } from './Icon';

const Checkbox = ({
  checked, label, onChange, size = 'medium', ...props
}) => {
  const checkboxClass = classnames(
    'jfCheckbox',
    `jfCheckbox--${size}`
  );

  return (
    <label className={checkboxClass}>
      <span className='jfCheckbox--checkmark'>
        <input
          type='checkbox'
          checked={checked}
          onChange={onChange}
          {...props}
        />
        {checked && <IconCheck />}
      </span>
      <span className='jfCheckbox--label'>{label}</span>
    </label>
  );
};

Checkbox.propTypes = {
  checked: bool.isRequired,
  label: string.isRequired,
  onChange: func.isRequired,
  size: oneOf(['small', 'medium', 'large'])
};

export default Checkbox;
