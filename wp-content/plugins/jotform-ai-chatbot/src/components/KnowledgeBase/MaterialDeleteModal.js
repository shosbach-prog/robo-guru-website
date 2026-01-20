import React from 'react';
import { bool, func } from 'prop-types';

import IconTrashExclamationFilled from '../../assets/svg/IconTrashExclamationFilled.svg';
import { t } from '../../utils';
import Button from '../UI/Button';
import Modal from '../UI/Modal';

const MaterialDeleteModal = ({ isOpen, onDeleteClick, onCloseClick }) => (
  <Modal
    open={isOpen}
    onClose={onCloseClick}
    ariaLabel={t('Material Confirmation Modal')}
    size='small'
  >
    <div className='jfModal--title'>
      <div className='jfModal--title-icon jfModal--title-icon-error' aria-hidden='true'>
        <IconTrashExclamationFilled />
      </div>
      <h3>
        {t('Do you want to delete knowledge?')}
      </h3>
      <p>
        {t('Once removed, the AI Agent will no longer have access to it.')}
      </p>
    </div>
    <div className='jfModal--actions'>
      <Button
        colorStyle='secondary'
        variant='outline'
        onClick={onCloseClick}
      >
        {t('No, Keep')}
      </Button>
      <Button
        colorStyle='error'
        onClick={onDeleteClick}
      >
        {t('Yes, Delete')}
      </Button>
    </div>
  </Modal>
);

MaterialDeleteModal.propTypes = {
  isOpen: bool.isRequired,
  onDeleteClick: func.isRequired,
  onCloseClick: func.isRequired
};

export default MaterialDeleteModal;
