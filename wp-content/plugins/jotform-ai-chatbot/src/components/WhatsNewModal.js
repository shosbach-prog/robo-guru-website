import React, { useEffect } from 'react';

import { saveInstallment } from '../api';
import WhatsNewImg from '../assets/images/whats-new-img-ca.png';
import { ALL_TEXTS, WHATS_NEW_MODAL_LCST_FLAG } from '../constants';
import { useLocalStorageModal } from '../hooks';
import { t } from '../utils';
import Button from './UI/Button';
import Modal from './UI/Modal';

const WhatsNewModal = () => {
  const [isModalVisible, closeModal] = useLocalStorageModal(WHATS_NEW_MODAL_LCST_FLAG);

  useEffect(() => {
    if (isModalVisible) {
      saveInstallment('whatsNewDialog_v3_6_0');
    }
  }, [isModalVisible]);

  return (
    <Modal
      open={isModalVisible}
      onClose={closeModal}
      ariaLabel={t(ALL_TEXTS.WHATS_NEW)}
      size='medium'
      className='jfModal--whats-new'
      aria-labelledby='whatsNewTitle'
      aria-describedby='whatsNewTescription'
      role='dialog'
      aria-modal='true'
    >
      <div className='jfModal--header'>
        <div className='jfModal--header-title'>
          <h3 id='whatsNewTitle'>{t(ALL_TEXTS.WHATS_NEW)}</h3>
          <p id='whatsNewTescription'>{t(ALL_TEXTS.GET_THE_LATEST_CHANGES_AND_UPDATES)}</p>
        </div>
      </div>
      <div className='jfModal--body'>
        <img src={WhatsNewImg} className='jfModal--body-img' alt='Chatbot Whats New' />
        <div className='jfModal--body-content' role='region' aria-labelledby='avatar-upload-title'>
          <p id='avatar-upload-title'>You can now <strong>upload your own image and use it as your chatbot’s avatar.</strong></p>
          <p>Make your chatbot truly yours by matching your brand, persona, or organization. <span>No more default avatars that don’t fit.</span></p>
          <p><strong>Upload your image and customize your avatar in seconds.</strong></p>
        </div>
      </div>
      <div className='jfModal--footer'>
        <Button
          colorStyle='primary'
          // variant='outline'
          onClick={closeModal}
        >
          {t(ALL_TEXTS.TRY_IT_NOW)}
        </Button>
      </div>
    </Modal>
  );
};

export default WhatsNewModal;
