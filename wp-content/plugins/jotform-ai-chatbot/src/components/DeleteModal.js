import React, { useEffect } from 'react';
import { bool, func } from 'prop-types';

import { saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { t } from '../utils';
import Button from './UI/Button';
import { IconExclamationCircleFilled, IconXmarkCircle } from './UI/Icon';
import Modal from './UI/Modal';

const DeleteModal = ({
  isOpen,
  isDeleteLoading,
  onDeleteClick,
  onCloseClick
}) => {
  useEffect(() => {
    if (isOpen) {
      saveInstallment('deleteDialog');
    }
  }, [isOpen]);

  return (
    <Modal
      open={isOpen}
      onClose={onCloseClick}
      aria-labelledby='removeDialogTitle'
      // ariaLabel={t(ALL_TEXTS.REMOVE_CHATBOT_FROM_WEBSITE)}
      size='small'
    >
      <div className='jfModal--title'>
        <div className='jfModal--title-icon jfModal--title-icon-error' aria-hidden='true'>
          <IconXmarkCircle />
        </div>
        <h2 id='removeDialogTitle'>
          {t(ALL_TEXTS.REMOVE_CHATBOT_FROM_WEBSITE)}
        </h2>
        <p style={{ marginBottom: 0 }}>
          {t(ALL_TEXTS.CLICK_REMOVE_FROM_WEBSITE_TO_PERMANENTLY_DELETE)}
        </p>
        <div className='jfModal--title-info'>
          <IconExclamationCircleFilled className='jfModal--title-info-icon' aria-hidden='true' />
          <p>{t(ALL_TEXTS.YOUR_CHATBOT_AND_ITS_TRANING_DATA_WILL_STILL_BE_SAVED)}</p>
        </div>
      </div>
      <div className='jfModal--actions'>
        <Button
          colorStyle='secondary'
          variant='outline'
          onClick={onCloseClick}
        >
          {t(ALL_TEXTS.CANCEL)}
        </Button>
        <Button
          colorStyle='error'
          // loader={isDeleteLoading}
          onClick={onDeleteClick}
          disabled={isDeleteLoading}
          aria-live='polite'
        >
          {isDeleteLoading
            ? t(ALL_TEXTS.REMOVING)
            : t(ALL_TEXTS.REMOVE_FROM_WEBSITE)}
        </Button>
      </div>
    </Modal>
  );
};

DeleteModal.propTypes = {
  isOpen: bool.isRequired,
  onDeleteClick: func.isRequired,
  onCloseClick: func.isRequired,
  isDeleteLoading: bool
};

export default DeleteModal;
