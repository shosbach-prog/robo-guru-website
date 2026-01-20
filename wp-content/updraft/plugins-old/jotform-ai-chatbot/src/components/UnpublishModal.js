import React, { useEffect } from 'react';
import { bool, func } from 'prop-types';

import { saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { t } from '../utils';
import Button from './UI/Button';
import { IconExclamationCircleFilled, IconXmarkCircle } from './UI/Icon';
import Modal from './UI/Modal';

const UnpublishModal = ({
  isOpen,
  isPublishLoading,
  onUnpublishClick,
  onCloseClick
}) => {
  useEffect(() => {
    if (isOpen) {
      saveInstallment('unpublishDialog');
    }
  }, [isOpen]);

  return (
    <Modal
      open={isOpen}
      onClose={onCloseClick}
      aria-labelledby='unpublishDialogTitle'
      size='small'
    >
      <div className='jfModal--title'>
        <div className='jfModal--title-icon jfModal--title-icon-error' aria-hidden='true'>
          <IconXmarkCircle />
        </div>
        <h2 id='unpublishDialogTitle'>
          {t(ALL_TEXTS.UNPUBLISH_CHATBOT_FROM_WEBSITE)}
        </h2>
        <p style={{ marginBottom: 0 }}>
          {t(ALL_TEXTS.CLICK_UNPUBLISH_TO_REMOVE_THE_CHATBOT)}
        </p>
        <div className='jfModal--title-info'>
          <IconExclamationCircleFilled className='jfModal--title-info-icon' aria-hidden='true' />
          <p>{t(ALL_TEXTS.YOUR_CHATBOT_AND_ITS_TRANING_DATA_WILL_REMAIN_SAVED)}</p>
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
          // loader={isPublishLoading}
          disabled={isPublishLoading}
          onClick={onUnpublishClick}
          aria-live='polite'
        >
          {isPublishLoading
            ? t(ALL_TEXTS.UNPUBLISHING)
            : t(ALL_TEXTS.UNPUBLISH)}
        </Button>
      </div>
    </Modal>
  );
};

UnpublishModal.propTypes = {
  isOpen: bool.isRequired,
  onUnpublishClick: func.isRequired,
  onCloseClick: func.isRequired,
  isPublishLoading: bool
};

export default UnpublishModal;
