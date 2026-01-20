import React, { useEffect } from 'react';
import { bool, func } from 'prop-types';

import { saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { t, translationRenderer } from '../utils';
import Button from './UI/Button';
import { IconExclamationCircleFilled, IconHeadset } from './UI/Icon';
import Modal from './UI/Modal';

// TODO: add UI & content for support modal
const SupportModal = ({ isModalVisible, onGotoSupportPageClick, onCloseClick }) => {
  useEffect(() => {
    if (isModalVisible) {
      saveInstallment('supportDialog');
    }
  }, [isModalVisible]);

  return (
    <Modal
      open={isModalVisible}
      onClose={onCloseClick}
      ariaLabel={t(ALL_TEXTS.WHATS_NEW)}
      size='small'
    >
      <div className='jfModal--title'>
        <div className='jfModal--title-icon jfModal--title-icon-informative' aria-hidden='true'>
          <IconHeadset />
        </div>
        <h2 id='removeDialogTitle'>
          {t(ALL_TEXTS.OPEN_A_SUPPORT_TICKET)}
        </h2>
        <p style={{ marginBottom: 0 }}>
          {t(ALL_TEXTS.YOU_WILL_BE_REDIRECTED_TO_SUPPORT)}
        </p>
        <div className='jfModal--title-info'>
          <IconExclamationCircleFilled className='jfModal--title-info-icon' aria-hidden='true' />
          <p>
            {translationRenderer(ALL_TEXTS.TO_SUBMIT_A_TICKET_PLEASE)({
              renderer1: str => (<strong>{str}</strong>),
              renderer2: str => (<strong>{str}</strong>)
            })}
          </p>
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
          colorStyle='primary'
          // variant='outline'
          onClick={onGotoSupportPageClick}
          aria-label='Go to support page (opens in new tab)'
        >
          {t(ALL_TEXTS.GO_TO_SUPPORT_PAGE)}
        </Button>
      </div>
    </Modal>
  );
};

export default SupportModal;

SupportModal.propTypes = {
  isModalVisible: bool.isRequired,
  onCloseClick: func.isRequired,
  onGotoSupportPageClick: func.isRequired
};
