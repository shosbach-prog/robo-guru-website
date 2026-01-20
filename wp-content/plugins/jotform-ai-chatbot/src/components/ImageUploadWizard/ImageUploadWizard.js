import React, { useEffect, useRef, useState } from 'react';
import { bool, func, string } from 'prop-types';

import CropImageModal from './CropImageModal';
import ImageUploadInput from './ImageUploadInput';
import { prepareFile } from './utils';

const ImageUploadWizard = ({
  username = '',
  onSuccess = f => f,
  onError = f => f,
  handleUploadToServer = f => f,
  allowedTypes = 'image/*',
  useCrop = false,
  useRemoveBg = false,
  useUploadNewImage = false,
  useImageEditor = false,
  limitImageSize = false,
  onSelectImage = null,
  onRemoveImageBackground = f => f
}) => {
  const [isCropImageModalOpen, setIsCropImageModalOpen] = useState(false);
  const fileInputRef = useRef(null);
  const [selectedFile, setSelectedFile] = useState(null);
  const [errorMsg, setErrorMsg] = useState('');
  const MAX_SIZE = 10 * 1024 * 1024;

  useEffect(() => {
    if (selectedFile) {
      setIsCropImageModalOpen(true);
    }
  }, [selectedFile]);

  useEffect(() => {
    let timeout;
    if (errorMsg) {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        setErrorMsg('');
      }, 5000);
    }
  }, [errorMsg]);

  const handleCloseCropImageModal = () => {
    setIsCropImageModalOpen(false);
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
    setSelectedFile(null);
  };

  const handleImageSelect = (file) => {
    if (limitImageSize && file.size > MAX_SIZE) {
      setErrorMsg('Upload failed: File size exceeds the 10MB limit.');
      return;
    }

    if (allowedTypes !== 'image/*' && !allowedTypes.split(',').includes(file.type)) {
      setErrorMsg('Unsupported file type.');
      return;
    }

    if (useCrop) {
      setSelectedFile(file);
      return;
    }

    if (onSelectImage) {
      onSelectImage(file);
      return;
    }

    if (handleUploadToServer && onSuccess) {
      handleUploadToServer(file)
        .then(({ message: url }) => {
          onSuccess(url);
        })
        .catch(error => {
          console.error(error);
          onError?.(error);
        });
    }
  };

  const handleCropSuccess = (file) => {
    // Cropping images may result in files exceeding the size limits, even if they were originally within the limits.
    // Use the original file in such cases to avoid issues with parameter validation on the backend.
    // This workaround was applied because various configurations in react-cropper had no effect.
    if (onSelectImage && file && selectedFile) {
      onSelectImage(limitImageSize && file.size > MAX_SIZE ? selectedFile : file);
    }
  };

  const handleFileInputChange = (e) => {
    const file = e.target.files && prepareFile(e.target.files);
    if (file) {
      handleImageSelect(file);
    }
  };

  return (
    <div className='jfpContent-wrapper--image-upload-wizard'>
      <ImageUploadInput
        ref={fileInputRef}
        accept={allowedTypes}
        onChange={handleFileInputChange}
        size='small'
      />
      <CropImageModal
        {...{
          isOpen: isCropImageModalOpen,
          image: selectedFile,
          onSuccess,
          onError,
          useRemoveBg,
          useImageEditor,
          useUploadNewImage,
          username,
          handleUploadToServer,
          onSelectImage: onSelectImage && handleCropSuccess,
          onClose: () => {
            handleCloseCropImageModal();
          },
          onRemoveImageBackground,
          closeModal: handleCloseCropImageModal,
          onFileInputChange: handleFileInputChange
        }}
      />
    </div>
  );
};

ImageUploadWizard.propTypes = {
  useCrop: bool,
  useRemoveBg: bool,
  username: string,
  useImageEditor: bool,
  useUploadNewImage: bool,
  allowedTypes: string,
  limitImageSize: bool,
  onSelectImage: func,
  onSuccess: func,
  onError: func,
  handleUploadToServer: func,
  onRemoveImageBackground: func
};

export default ImageUploadWizard;
