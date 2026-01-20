import React from 'react';
import classnames from 'classnames';
import {
  bool, func, node, string
} from 'prop-types';

import '../../styles/infobox.scss';

import Button from '../UI/Button';
import { IconChevronLeft } from '../UI/Icon';

const InfoBox = ({
  icon,
  name,
  desc,
  isBackVisible = false,
  iconClassName,
  isNameVisible = false,
  isDescVisible = false,
  isIconVisible = false,
  handleBack = f => f
}) => (
  <div className='jfInfobox'>
    {isBackVisible && (
    <Button
      colorStyle='secondary'
      variant='ghost'
      startIcon={<IconChevronLeft />}
      onClick={handleBack}
      className='jfInfobox--back-btn'
    />
    )}
    {isIconVisible && icon && (
    <div className={classnames('jfInfobox--icon', iconClassName)}>
      {icon}
    </div>
    )}
    <div className='jfInfobox--content'>
      {isNameVisible && <span className='jfInfobox--name'>{name}</span>}
      {isDescVisible && <span className='jfInfobox--desc'>{desc}</span>}
    </div>
  </div>
);

export default InfoBox;

InfoBox.propTypes = {
  icon: node,
  name: string,
  desc: string,
  handleBack: func,
  isBackVisible: bool,
  iconClassName: string,
  isNameVisible: bool,
  isDescVisible: bool,
  isIconVisible: bool
};
