import React from 'react';
import { func, node, string } from 'prop-types';

import '../../styles/dropdown.scss';

const Dropdown = ({ children, value = '', onChange = f => f }) => (
  <select
    value={value}
    onChange={(e) => onChange(e.target.value)}
    className='jfDropdown'
  >
    {children}
  </select>
);

Dropdown.propTypes = {
  children: node.isRequired,
  value: string,
  onChange: func
};

export default Dropdown;
