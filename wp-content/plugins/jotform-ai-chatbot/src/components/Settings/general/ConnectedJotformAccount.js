import React, { useState } from 'react';

import { interactWithPlatform, saveInstallment } from '../../../api';
import { ALL_TEXTS } from '../../../constants';
import { useWizard } from '../../../hooks';
import { ACTION_CREATORS } from '../../../store';
import { t, toggleSettingsItems } from '../../../utils';
import LogoutModal from '../../LogoutModal';
import Button from '../../UI/Button';

const ConnectedJotformAccount = () => {
  const { state, asyncDispatch } = useWizard();
  const [isLogoutModalOpen, setIsLogoutModalOpen] = useState(false);

  const { user, isLogoutLoading } = state;

  const logoutFromJotform = async () => {
    saveInstallment('logout');
    const data = { action: 'update', key: 'logout' };
    await asyncDispatch(
      () => interactWithPlatform(data),
      ACTION_CREATORS.logoutFromJotformRequest,
      ACTION_CREATORS.logoutFromJotformSuccess,
      ACTION_CREATORS.logoutFromJotformError
    );
    setIsLogoutModalOpen(false);
    toggleSettingsItems({ action: 'hide' });
  };

  const handleLogoutClick = () => {
    setIsLogoutModalOpen(true);
  };

  return (
    <div className='jfpContent-wrapper--settings-options-wrapper-connected'>
      <h3 className='jfpContent-wrapper--settings-options-wrapper-connected-title'>{t(ALL_TEXTS.CONNECTED_JOTFORM_ACCOUNT.toUpperCase())}</h3>
      <div className='jfpContent-wrapper--settings-options-wrapper-connected-wrapper'>
        <div className='jfpContent-wrapper--settings-options-wrapper-connected-content-wrapper'>
          <img
            src={user?.avatarUrl}
            alt='User Avatar'
            className='jfpContent-wrapper--settings-options-wrapper-connected-icon big full-radius'
          />
          <div className='jfpContent-wrapper--settings-options-wrapper-connected-content'>
            <strong>{user?.name}</strong>
            <p>{user?.email}</p>
          </div>
        </div>
        <Button
          colorStyle='error'
          variant='outline'
          size='small'
          onClick={handleLogoutClick}
          aria-haspopup='dialog'
          aria-expanded={isLogoutModalOpen}
          className='jfpContent-wrapper--settings-options-wrapper-connected-btn'
        >
          {t(ALL_TEXTS.LOGOUT)}
        </Button>
      </div>
      <LogoutModal
        isOpen={isLogoutModalOpen}
        isLogoutLoading={isLogoutLoading}
        onLogoutClick={logoutFromJotform}
        onCloseClick={() => setIsLogoutModalOpen(false)}
      />
    </div>
  );
};

export default ConnectedJotformAccount;
