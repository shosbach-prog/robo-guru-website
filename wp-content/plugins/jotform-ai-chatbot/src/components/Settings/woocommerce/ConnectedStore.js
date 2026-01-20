import React from 'react';
import { func } from 'prop-types';

import WooLogo from '../../../assets/svg/woo-logo.svg';
import { ALL_TEXTS } from '../../../constants';
import { useWizard } from '../../../hooks';
import { t } from '../../../utils';
import Button from '../../UI/Button';

const ConnectedStore = ({ disconnectStore }) => {
  const { state } = useWizard();

  const {
    woocommerce: { consumerKey, isDisconnectLoading }
  } = state;

  return (
    <div className='jfpContent-wrapper--settings-options-wrapper-connected-wrapper'>
      <div className='jfpContent-wrapper--settings-options-wrapper-connected-content-wrapper'>
        <WooLogo className='jfpContent-wrapper--settings-options-wrapper-connected-icon' />
        <div className='jfpContent-wrapper--settings-options-wrapper-connected-content'>
          <strong>{t(ALL_TEXTS.CONNECTED_STORE)}</strong>
          <p style={{ fontFamily: 'monospace' }}>{consumerKey}</p>
        </div>
      </div>
      <Button
        colorStyle='error'
        variant='outline'
        size='small'
        loader={isDisconnectLoading}
        className='jfpContent-wrapper--settings-options-wrapper-connected-btn'
        onClick={disconnectStore}
      >
        {t(ALL_TEXTS.DISCONNECT_STORE)}
      </Button>
    </div>
  );
};

ConnectedStore.propTypes = {
  disconnectStore: func.isRequired
};

export default ConnectedStore;
