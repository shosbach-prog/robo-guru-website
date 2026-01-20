/* eslint-disable jsx-a11y/click-events-have-key-events */
/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
import React from 'react';
import { func, string } from 'prop-types';

import { TRAIN_TYPES } from '../../constants';
import { IconAngleRightCircleFilled } from '../UI/Icon';

const MaterialSelection = ({
  materialType,
  setMaterialType = f => f,
  setStep = f => f
}) => {
  const handleClick = key => {
    setMaterialType(key);
    setStep('editor');
  };

  return (
    <ul className='knowledge-container'>
      {/* knowledge */}
      {Object.entries(TRAIN_TYPES).map(([key, item]) => (
        <li
          key={key}
          data-is-selected={materialType === key}
          data-item={`${item.iconClassName}`}
          onClick={() => handleClick(key)}
          className='knowledge-item'
        >
          {/* for icon */}
          <span className='knowledge-before' />
          <span className='knowledge-icon'>{item.icon}</span>
          {/* text */}
          <div className='knowledge-content'>
            <h3 className='knowledge-title'>{item.name.toUpperCase()}</h3>
            <p className='knowledge-desc'>{item.desc}</p>
          </div>
          {/* icon right */}
          <span className='knowledge-right'>
            <IconAngleRightCircleFilled />
          </span>
        </li>
      ))}
    </ul>
  );
};

export default MaterialSelection;

MaterialSelection.propTypes = {
  materialType: string,
  setMaterialType: func,
  setStep: func
};
