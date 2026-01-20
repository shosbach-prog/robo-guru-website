import React, { useEffect, useState } from 'react';
import { func } from 'prop-types';

import { interactWithPlatform, saveInstallment } from '../../api';
import {
  ALL_TEXTS, DELETE_INST_NAME, DEVICES
} from '../../constants';
import { useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { t, toCamelCase } from '../../utils';
import { AdvancedVisibility } from '../AdvancedVisibility';
import DeleteModal from '../DeleteModal';
import Button from '../UI/Button';
import Dropdown from '../UI/Dropdown';
import { VisibilityLayout } from '../VisibilityLayout';

const VisibilityStep = ({ unpublishAgent }) => {
  const { state, dispatch, asyncDispatch } = useWizard();

  const {
    step,
    visibleDevice,
    isPublishLoading,
    isDeletePlatformAgentLoading,
    isPublished
  } = state;

  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);

  useEffect(() => {
    saveInstallment(`${toCamelCase(step)}Step`);
  }, []);

  const saveVisibleDevice = async (value) => {
    const dataApiKey = { action: 'update', key: 'device', value };
    await asyncDispatch(
      () => interactWithPlatform(dataApiKey),
      ACTION_CREATORS.saveProviderApiKeyRequest,
      ACTION_CREATORS.saveProviderApiKeySuccess,
      ACTION_CREATORS.saveProviderApiKeyError
    );
  };

  const handleVisibleDeviceChange = value => {
    dispatch(ACTION_CREATORS.updateVisibleDevice(value));
    saveVisibleDevice(value);
    saveInstallment(`visibleOn_${value}`);
  };

  const handleDeleteWpChatbot = async () => {
    saveInstallment(`${DELETE_INST_NAME}`);
    const data = { action: 'delete' };
    await asyncDispatch(
      () => interactWithPlatform(data),
      ACTION_CREATORS.deletePlatformAgentRequest,
      ACTION_CREATORS.deletePlatformAgentSuccess,
      ACTION_CREATORS.deletePlatformAgentError
    );
    setIsDeleteModalOpen(false);
  };

  const handleUnpublishClick = async () => {
    await unpublishAgent();
    setIsDeleteModalOpen(false);
    saveInstallment('unpublishButton');
  };

  return (
    <>
      <div className='jfpContent-wrapper--customization'>
        <h2 className='sr-only'>{t(ALL_TEXTS.VISIBILITY)}</h2>
        {/* layout */}
        <VisibilityLayout />
        {/* visible on */}
        <div className='customize-option visibility'>
          <div className='jfpContent-wrapper--customization-title'>
            <div>
              <h3>{t(ALL_TEXTS.VISIBLE_ON)}</h3>
              <p>{t(ALL_TEXTS.CHOOSE_WHERE_THE_CHATBOT_SHOULD_APPEAR)}</p>
            </div>
          </div>
          <Dropdown
            colorStyle='default'
            size='small'
            theme='light'
            value={visibleDevice}
            onChange={value => handleVisibleDeviceChange(value)}
          >
            {DEVICES.map(({ value, text }) => (
              <option
                key={value}
                value={value}
              >
                {t(text)}
              </option>
            ))}
          </Dropdown>
        </div>
        <hr className='jfpContent-wrapper--line' />
        {/* advanced visibility */}
        <AdvancedVisibility />
        {/* remove from website */}
        <div className='remove-chatbot'>
          <Button
            className='remove-chatbot-btn'
            onClick={() => setIsDeleteModalOpen(true)}
            aria-haspopup='dialog'
            aria-expanded={isDeleteModalOpen}
          >
            {t(ALL_TEXTS.REMOVE_AI_CHATBOT_FROM_MY_WEBSITE)}
          </Button>
        </div>
      </div>
      <DeleteModal
        isOpen={isDeleteModalOpen}
        isDeleteLoading={isDeletePlatformAgentLoading}
        isUnpublishLoading={isPublishLoading}
        onDeleteClick={handleDeleteWpChatbot}
        onUnpublishClick={handleUnpublishClick}
        onCloseClick={() => setIsDeleteModalOpen(false)}
        isPublished={isPublished}
      />
    </>
  );
};

VisibilityStep.propTypes = {
  unpublishAgent: func.isRequired
};

export default VisibilityStep;
