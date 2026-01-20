import React, { useCallback, useEffect, useState } from 'react';
import {
  bool, func, string
} from 'prop-types';

import { getAgentAvatarAsBase64, saveInstallment } from '../api';
import { ALL_TEXTS } from '../constants';
import { useWizard } from '../hooks';
import { t } from '../utils';
import { CropImageModal } from './ImageUploadWizard';
import { base64ToFile, formatFileSize, prepareFile } from './ImageUploadWizard/utils';
import Button from './UI/Button';

const CustomAvatar = ({
  onChangeAvatar = f => f,
  username = '',
  onSuccess = f => f,
  onError = f => f,
  handleUploadToServer = f => f,
  useRemoveBg = false,
  useImageEditor = false,
  onRemoveImageBackground = f => f
}) => {
  const { state } = useWizard();

  const {
    selectedAvatar,
    previewAgentId,
    platformSettings: { PROVIDER_API_KEY },
    isAgentPropertyLoading
  } = state;

  const { avatarIconLink = '' } = selectedAvatar;

  const [imageSize, setImageSize] = useState('');
  const [imageBlob, setImageBlob] = useState(null);
  const [isEditLoading, setIsEditLoading] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);

  const fetchImage = useCallback(async () => {
    if (!previewAgentId || !PROVIDER_API_KEY) return;
    try {
      setIsEditLoading(true);
      const base64 = await getAgentAvatarAsBase64(previewAgentId, PROVIDER_API_KEY);
      const file = base64ToFile(base64, `avatar_${Date.now()}.png`, 'image/png');
      return file;
    } catch (error) {
      console.error(error);
    } finally {
      setIsEditLoading(false);
    }
  }, [previewAgentId, PROVIDER_API_KEY, selectedAvatar.avatarIconLink]);

  useEffect(() => {
    const refreshBlob = async () => {
      try {
        const file = await fetchImage();
        setImageBlob(file);
        setImageSize(formatFileSize(file.size));
      } catch (error) {
        console.error(error);
      }
    };
    refreshBlob();
  }, [fetchImage]);

  const handleFileInputChange = (e) => {
    const file = e.target.files && prepareFile(e.target.files);
    setImageBlob(file);
  };

  const handleEditAvatar = async () => {
    saveInstallment('editAvatarButton');
    const file = await fetchImage();
    setImageBlob(file);
    setIsEditModalOpen(true);
  };

  return (
    <div className='jfpContent-wrapper--avatar-custom'>
      <div className='jfpContent-wrapper--avatar-custom-avatar'>
        <div className='jfpContent-wrapper--avatar-custom-avatar-image'>
          <img
            src={avatarIconLink}
            alt='Avatar Img'
          />
        </div>
        <div className='jfpContent-wrapper--avatar-custom-avatar-properties'>
          <h4>{t(ALL_TEXTS.IMAGE_SIZE)}</h4>
          <span>{imageSize || t(ALL_TEXTS.LOADING)}</span>
        </div>
      </div>
      <div className='jfpContent-wrapper--avatar-custom-buttons'>
        <Button
          variant='ghost'
          colorStyle='neutral'
          onClick={onChangeAvatar}
        >
          {t(ALL_TEXTS.CHANGE_AVATAR)}
        </Button>
        <Button
          loader={isEditLoading || isAgentPropertyLoading}
          disabled={isEditLoading || isAgentPropertyLoading}
          onClick={handleEditAvatar}
        >
          {t(ALL_TEXTS.EDIT_AVATAR)}
        </Button>
      </div>
      <CropImageModal
        image={imageBlob}
        username={username}
        useRemoveBg={useRemoveBg}
        useImageEditor={useImageEditor}
        isOpen={isEditModalOpen}
        onSuccess={onSuccess}
        onError={onError}
        onClose={() => {
          setIsEditModalOpen(false);
        }}
        onFileInputChange={handleFileInputChange}
        handleUploadToServer={handleUploadToServer}
        closeModal={() => setIsEditModalOpen(false)}
        onRemoveImageBackground={onRemoveImageBackground}
      />
    </div>
  );
};

CustomAvatar.propTypes = {
  onChangeAvatar: func.isRequired,
  username: string.isRequired,
  onSuccess: func.isRequired,
  onError: func.isRequired,
  handleUploadToServer: func.isRequired,
  useRemoveBg: bool.isRequired,
  useImageEditor: bool.isRequired,
  onRemoveImageBackground: func.isRequired
};

export default CustomAvatar;
