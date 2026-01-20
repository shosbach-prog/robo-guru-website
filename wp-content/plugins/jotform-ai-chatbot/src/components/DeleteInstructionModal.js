import React from 'react';
import { bool, func } from 'prop-types';

import { ALL_TEXTS } from '../constants';
import { t } from '../utils';
import Button from './UI/Button';
import { IconTrashExclamationFilled } from './UI/Icon';
import Modal from './UI/Modal';

const DeleteInstructionModal = ({ isOpen, onDeleteClick, onCloseClick }) => (
  <Modal
    open={isOpen}
    onClose={onCloseClick}
    ariaLabel={t(ALL_TEXTS.DO_YOU_WANT_TO_DELETE_INSTRUCTION)}
    size='small'
  >
    <div className='jfModal--title'>
      <div className='jfModal--title-icon jfModal--title-icon-error' aria-hidden='true'>
        <IconTrashExclamationFilled />
      </div>
      <h3>
        {t(ALL_TEXTS.DO_YOU_WANT_TO_DELETE_INSTRUCTION)}
      </h3>
      <p>
        {t(ALL_TEXTS.THIS_INSTRUCTION_WILL_BE_REMOVED_PERMANENTLY)}
      </p>
    </div>
    <div className='jfModal--actions'>
      <Button
        colorStyle='secondary'
        variant='outline'
        onClick={onCloseClick}
      >
        {t(ALL_TEXTS.NO_KEEP)}
      </Button>
      <Button
        colorStyle='error'
        onClick={onDeleteClick}
      >
        {t(ALL_TEXTS.YES_DELETE)}
      </Button>
    </div>
  </Modal>
);

DeleteInstructionModal.propTypes = {
  isOpen: bool.isRequired,
  onDeleteClick: func.isRequired,
  onCloseClick: func.isRequired
};

export default DeleteInstructionModal;
