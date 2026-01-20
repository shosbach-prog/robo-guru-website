import React, { useEffect } from 'react';
import { bool, func } from 'prop-types';

import { saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { t } from '../utils';
import Button from './UI/Button';
import { IconExclamationCircleFilled, IconXmarkCircle } from './UI/Icon';
import Modal from './UI/Modal';

const LogoutModal = ({
  isOpen,
  isLogoutLoading,
  onLogoutClick,
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
      aria-labelledby='logoutDialogTitle'
      // ariaLabel={t(ALL_TEXTS.LOGOUT_DIALOG_TITLE)}
      size='small'
    >
      <div className='jfModal--title'>
        <div className='jfModal--title-icon jfModal--title-icon-error' aria-hidden='true'>
          <IconXmarkCircle />
        </div>
        <h2 id='logoutDialogTitle'>{t(ALL_TEXTS.LOGOUT_DIALOG_TITLE)}</h2>
        <p style={{ marginBottom: 0 }}>
          {t(ALL_TEXTS.LOGOUT_DIALOG_DESC)}
        </p>
        <div className='jfModal--title-info'>
          <IconExclamationCircleFilled className='jfModal--title-info-icon' aria-hidden='true' />
          <p>{t(ALL_TEXTS.LOGOUT_DIALOG_INFO)}</p>
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
          // loader={isLogoutLoading}
          disabled={isLogoutLoading}
          onClick={onLogoutClick}
          aria-live='polite'
        >
          {isLogoutLoading
            ? t(ALL_TEXTS.LOGGINGOUT)
            : t(ALL_TEXTS.LOGOUT)}
        </Button>
      </div>
    </Modal>
  );
};

LogoutModal.propTypes = {
  isOpen: bool.isRequired,
  onLogoutClick: func.isRequired,
  onCloseClick: func.isRequired,
  isLogoutLoading: bool.isRequired
};

export default LogoutModal;
