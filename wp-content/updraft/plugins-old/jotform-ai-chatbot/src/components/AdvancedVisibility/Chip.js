import React from 'react';
import { node } from 'prop-types';

const Chip = ({ children }) => (
  <span className='condition-chip'>{children}</span>
);

Chip.propTypes = {
  children: node.isRequired
};

export default Chip;
