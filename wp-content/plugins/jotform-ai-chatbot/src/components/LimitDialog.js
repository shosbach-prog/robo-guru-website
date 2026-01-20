import React, { useEffect } from 'react';
import { func, string } from 'prop-types';

import { saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { awaitFor, t } from '../utils';
import Button from './UI/Button';
import { IconInfoSquareFilled } from './UI/Icon';
import Modal from './UI/Modal';

const LimitDialog = ({
  providerUrl,
  utmContent,
  onCloseClick
}) => {
  useEffect(() => {
    saveInstallment('limitDialog');
  }, []);

  const handleGoToPricing = async () => {
    saveInstallment('goToPricing');
    await awaitFor(1000);
    // eslint-disable-next-line max-len
    window.open(`${providerUrl}/ai/wordpress-agent/pricing/?utm_source=limitDialog&utm_content=${utmContent}&utm_campaign=aiAgents&utm_medium=dialog&utm_term=go-to-pricing`, '_blank');
  };

  return (
    <Modal
      open
      onClose={onCloseClick}
      ariaLabel={t(ALL_TEXTS.YOU_HAVE_REACHED_YOUR_LIMIT)}
      size='small'
    >
      <div className='jfModal--title'>
        <div className='jfModal--title-icon jfModal--title-icon-error' aria-hidden='true'>
          <IconInfoSquareFilled />
        </div>
        <h3>
          {t(ALL_TEXTS.YOU_HAVE_REACHED_YOUR_LIMIT)}
        </h3>
        <p>
          {t(ALL_TEXTS.DELETE_EXISTING_AGENT)}
        </p>
      </div>
      <div className='jfModal--actions'>
        <Button
          colorStyle='error'
          onClick={handleGoToPricing}
        >
          {t(ALL_TEXTS.GO_TO_PRICING)}
        </Button>
      </div>
    </Modal>
  );
};

LimitDialog.propTypes = {
  providerUrl: string,
  utmContent: string,
  onCloseClick: func.isRequired
};

export default LimitDialog;
