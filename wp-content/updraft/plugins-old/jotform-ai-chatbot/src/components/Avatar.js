/* eslint-disable react/button-has-type */
import React, { useCallback, useEffect, useState } from 'react';

import {
  getAvatars, removeImageBackground, saveInstallment, updateAgent, updateAgentProperty,
  uploadImageFile
} from '../api';
import { ALL_TEXTS, AVATAR_VIEW_MODE } from '../constants';
import { useAvatarFocus, useWizard } from '../hooks';
import { ACTION_CREATORS } from '../store';
import {
  generateTempId,
  prepareAvatarPayload, swapItem, t
} from '../utils';
import CustomAvatar from './CustomAvatar';
import { ImageUploadWizard } from './ImageUploadWizard';
import Button from './UI/Button';

const Avatar = () => {
  const { state, asyncDispatch, dispatch } = useWizard();

  const {
    avatars,
    agentRole,
    selectedAvatar,
    avatarsOffset,
    previewAgentId,
    areAvatarsLoading,
    allAvatarsFetched,
    user: { username },
    platformSettings: { PROVIDER_API_KEY }
  } = state;

  const { id: selectedAvatarId } = selectedAvatar;

  const { isShowMoreClicked, avatarButtonRefs, prevAvatarsCount } = useAvatarFocus(avatars.length);

  const [viewMode, setViewMode] = useState(AVATAR_VIEW_MODE.LIST);

  useEffect(() => {
    if (selectedAvatar.customAvatar) {
      setViewMode(AVATAR_VIEW_MODE.CUSTOM);
    }
  }, [selectedAvatar]);

  const fetchAvatars = useCallback(async () => {
    if (areAvatarsLoading) return;

    const data = { limit: 17, nextPageOffset: avatarsOffset };
    await asyncDispatch(
      () => getAvatars(previewAgentId, data, PROVIDER_API_KEY),
      ACTION_CREATORS.getAvatarsRequest,
      ACTION_CREATORS.getAvatarsSuccess,
      ACTION_CREATORS.getAvatarsError
    );
  }, [previewAgentId, avatarsOffset, areAvatarsLoading, asyncDispatch, PROVIDER_API_KEY]);

  // initial fetch
  useEffect(() => {
    fetchAvatars();
  }, []);

  // show more button function
  const handleShowMoreClick = () => {
    isShowMoreClicked.current = true;
    prevAvatarsCount.current = avatars.length;
    fetchAvatars();
  };

  // select avatar
  const handleAvatarClick = async nextAvatar => {
    if (nextAvatar.customAvatar) return;
    let avatarsWithNewOrder = swapItem(avatars, nextAvatar.id);
    avatarsWithNewOrder = avatarsWithNewOrder.filter(avatar => !avatar.customAvatar);
    dispatch(ACTION_CREATORS.setAvatars(avatarsWithNewOrder, nextAvatar));
    // update avatar
    const data = prepareAvatarPayload(nextAvatar);
    asyncDispatch(
      () => updateAgentProperty(previewAgentId, data, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentPropertyRequest,
      ACTION_CREATORS.updateAgentPropertySuccess,
      ACTION_CREATORS.updateAgentPropertyError,
      { isAvatar: true, agentName: nextAvatar.avatarName }
    );
    // update agent name
    asyncDispatch(
      () => updateAgent(previewAgentId, { name: nextAvatar.avatarName }, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentRequest,
      ACTION_CREATORS.updateAgentSuccess,
      ACTION_CREATORS.updateAgentError
    );
    // update agent title
    asyncDispatch(
      () => updateAgent(previewAgentId, { title: `${nextAvatar.avatarName}: ${agentRole}` }, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentRequest,
      ACTION_CREATORS.updateAgentSuccess,
      ACTION_CREATORS.updateAgentError
    );
  };

  const handleAvatarKeyDown = (e, avatar) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      handleAvatarClick(avatar);
    }
  };

  const handleImageUploadWizardSuccess = (url) => {
    const customAvatar = {
      ...selectedAvatar, avatarLink: url, avatarIconLink: url, id: generateTempId(), customAvatar: true
    };
    const avatarsWithNewOrder = [customAvatar, ...avatars.filter(avatar => !avatar.customAvatar)];
    dispatch(ACTION_CREATORS.setAvatars(avatarsWithNewOrder, customAvatar));
    const data = prepareAvatarPayload(customAvatar);
    asyncDispatch(
      () => updateAgentProperty(previewAgentId, data, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentPropertyRequest,
      ACTION_CREATORS.updateAgentPropertySuccess,
      ACTION_CREATORS.updateAgentPropertyError,
      { isAvatar: true, agentName: customAvatar.avatarName }
    );
  };

  const handleImageUploadWizardError = (error) => {
    console.log(error);
  };

  const handleUploadS3 = async (file) => {
    const message = await uploadImageFile(username, file, 'AVATAR', true, PROVIDER_API_KEY);
    return { message };
  };

  const handleRemoveImageBackground = (usrname, payload) => removeImageBackground(usrname, payload, PROVIDER_API_KEY);

  const handleChangeAvatar = () => {
    saveInstallment('changeAvatarButton');
    setViewMode(AVATAR_VIEW_MODE.LIST);
  };

  return (
    <>
      <div className='jfpContent-wrapper--ai-persona-title'>
        <div className='jfpContent-wrapper--avatar-top'>
          <div>
            <h3>{ALL_TEXTS.AGENT_AVATAR}</h3>
            <p>{ALL_TEXTS.SELECT_AN_AVATAR}</p>
          </div>
          {viewMode === AVATAR_VIEW_MODE.LIST && (
            <ImageUploadWizard
              username={username}
              onSuccess={handleImageUploadWizardSuccess}
              onError={handleImageUploadWizardError}
              handleUploadToServer={handleUploadS3}
              allowedTypes='image/jpeg,image/jpg,image/png,image/bmp,image/webp'
              useCrop
              useRemoveBg
              useImageEditor
              limitImageSize
              onRemoveImageBackground={handleRemoveImageBackground}
            />
          )}
        </div>
        {viewMode === AVATAR_VIEW_MODE.LIST && (
          <>
            <div
              className='jfpContent-wrapper--avatar-gallery'
              role='radiogroup'
              aria-label={ALL_TEXTS.AGENT_AVATAR}
            >
              {avatars
                .map((avatar, index) => (
                  <button
                    ref={el => { avatarButtonRefs.current[index] = el; }}
                    className='avatar-button'
                    type='button'
                    key={avatar.id}
                    onClick={() => handleAvatarClick(avatar)}
                    onKeyDown={e => handleAvatarKeyDown(e, avatar)}
                    aria-pressed={selectedAvatarId === avatar.id}
                    aria-label={`${avatar.avatarName} ${selectedAvatarId === avatar.id ? ALL_TEXTS.CURRENT_AVATAR : ''}`}
                    tabIndex={0}
                  >
                    <img src={avatar.avatarIconLink} alt={`Avatar ${avatar.avatarName}`} />
                    {selectedAvatarId === avatar.id && (
                      <div className='avatar-button--selected'>
                        <span>{ALL_TEXTS.CURRENT_AVATAR}</span>
                      </div>
                    )}
                  </button>
                ))}
            </div>
            <div className='jfpContent-wrapper--ai-persona-show-more'>
              {!allAvatarsFetched && (
                <Button
                  colorStyle='primary'
                  variant='ghost'
                  fullWidth
                  onClick={handleShowMoreClick}
                  disabled={areAvatarsLoading}
                >
                  {areAvatarsLoading ? t(ALL_TEXTS.LOADING) : t(ALL_TEXTS.SHOW_MORE)}
                </Button>
              )}
            </div>
          </>
        )}
        {viewMode === AVATAR_VIEW_MODE.CUSTOM && (
        <CustomAvatar
          onChangeAvatar={handleChangeAvatar}
          username={username}
          onSuccess={handleImageUploadWizardSuccess}
          onError={handleImageUploadWizardError}
          handleUploadToServer={handleUploadS3}
          allowedTypes='image/jpeg,image/jpg,image/png,image/bmp,image/webp'
          useCrop
          useRemoveBg
          useImageEditor
          limitImageSize
          onRemoveImageBackground={handleRemoveImageBackground}
        />
        )}
      </div>
    </>
  );
};

export default Avatar;
