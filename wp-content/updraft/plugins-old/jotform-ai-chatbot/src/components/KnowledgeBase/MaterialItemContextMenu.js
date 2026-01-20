import React from 'react';
import {
  Popover, PopoverButton, PopoverPanel, useClose
} from '@headlessui/react';
import { func } from 'prop-types';

import { useElementScrollListener } from '../../hooks';
import {
  IconEllipsisVertical, IconNotificationTextFilled, IconPencilLineFilled, IconTrashExclamationFilled
} from '../UI/Icon';

const CloserComponentOnScroll = () => {
  const close = useClose();
  useElementScrollListener({
    targetElement: document.querySelector("[data-js='knowledge-scroll-container']"),
    callback: () => close()
  });
  return null;
};

const MaterialItemContextMenu = ({
  onEditClick,
  onSummaryClick,
  onDeleteClick
}) => (
  <Popover className='jfMaterialItemContextMenu'>
    <PopoverButton type='button' className='jfMaterialItemContextMenu--button'>
      <IconEllipsisVertical />
    </PopoverButton>
    <PopoverPanel anchor='bottom end' className='jfMaterialItemContextMenu--panel'>
      <button type='button' onClick={onEditClick} className='jfMaterialItemContextMenu--panel-button'>
        <IconPencilLineFilled />
        <span>Edit</span>
      </button>
      <button type='button' onClick={onSummaryClick} className='jfMaterialItemContextMenu--panel-button'>
        <IconNotificationTextFilled />
        <span>Summary</span>
      </button>
      <button type='button' onClick={onDeleteClick} className='jfMaterialItemContextMenu--panel-button jfMaterialItemContextMenu--panel-button-delete'>
        <IconTrashExclamationFilled />
        <span>Delete</span>
      </button>
      <CloserComponentOnScroll />
    </PopoverPanel>
  </Popover>
);

export default MaterialItemContextMenu;

MaterialItemContextMenu.propTypes = {
  onEditClick: func,
  onSummaryClick: func,
  onDeleteClick: func
};
