import React, { useEffect, useRef, useState } from 'react';
import {
  bool, func, shape, string
} from 'prop-types';
import { Cropper } from 'react-cropper';

import 'cropperjs/dist/cropper.css';

import { saveInstallment } from '../../api';
import { ALL_TEXTS } from '../../constants';
import { t } from '../../utils';
import Button from '../UI/Button';
import {
  IconArrowLeftHalf, IconArrowRotateLeft, IconArrowRotateRight,
  IconArrowsToLineHorizontal, IconArrowsToLineVertical, IconEraserFilled, IconWandMagicFilled
} from '../UI/Icon';
import Modal from '../UI/Modal';
import ImageUploadInput from './ImageUploadInput';
import { base64ToFile } from './utils';

const CropImageModal = ({
  isOpen,
  image,
  onSuccess,
  onError,
  useRemoveBg,
  useImageEditor,
  username,
  handleUploadToServer,
  onSelectImage,
  onClose = f => f,
  onRemoveImageBackground,
  closeModal,
  onFileInputChange,
  allowedTypes
}) => {
  useEffect(() => {
    if (isOpen) {
      saveInstallment('cropImageModal');
    }
  }, [isOpen]);

  const cropperRef = useRef(null);
  const fileInputRef = useRef(null);

  const [removeBg, setRemoveBg] = useState(false);
  const [removeImageBackgroundLoading, setRemoveImageBackgroundLoading] = useState(false);
  const [uploadImageLoading, setUploadImageLoading] = useState(false);
  const [nonBgImage, setNonBgImage] = useState('');
  const [imageSrc, setImageSrc] = useState(image);

  useEffect(() => {
    setImageSrc(image);
  }, [image]);

  const handleRemoveImageBackground = async ({ payload }) => (onRemoveImageBackground(username, payload));

  const handleCrop = () => {
    const cropperElement = cropperRef?.current?.cropper;
    if (cropperElement) {
      const croppedCanvas = cropperElement.getCroppedCanvas({
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
      });
      const croppedImage = croppedCanvas.toDataURL('image/png', 1);
      cropperElement.disable();
      const file = base64ToFile(croppedImage, imageSrc.name, imageSrc.type);
      if (onSelectImage) {
        onSelectImage(file);
        closeModal();
        return;
      }
      setUploadImageLoading(true);
      if (handleUploadToServer && onSuccess) {
        handleUploadToServer(file)
          .then(({ message }) => {
            onSuccess(message);
            closeModal();
          })
          .catch(error => {
            console.error(error);
            onError?.(error);
          })
          .finally(() => {
            setUploadImageLoading(false);
            cropperElement.enable();
          });
      }
    }
  };

  const handleRemoveBackgroundBtn = () => {
    const cropperElement = cropperRef?.current?.cropper;
    if (!removeBg && !nonBgImage && cropperElement) {
      setRemoveImageBackgroundLoading(true);
      cropperElement.disable();
      return handleRemoveImageBackground({ payload: { image: imageSrc } })
        .then(data => {
          if (data) {
            setNonBgImage(`data:image/png;base64,${data}`);
            setRemoveBg(true);
            cropperElement.enable();
          }
        })
        .finally(() => {
          setRemoveImageBackgroundLoading(false);
          cropperElement.enable();
        });
    }
    setRemoveBg(!removeBg);
  };

  const onCropChange = () => {
    if (cropperRef.current) {
      cropperRef?.current?.cropper.getCropBoxData();
    }
  };

  const handleRotate = (angle = 90) => {
    const cropper = cropperRef.current?.cropper;
    if (cropper) {
      cropper.clear();
      cropper.rotate(angle);
      cropper.zoomTo(0);

      const containerData = cropper.getContainerData();
      const imageData = cropper.getImageData();
      const zoomRatio = Math.min(
        containerData.width / imageData.naturalWidth,
        containerData.height / imageData.naturalHeight
      );
      cropper.zoomTo(zoomRatio);
      cropper.crop();
      const canvasData = cropper.getCanvasData();

      cropper.setCropBoxData({
        left: canvasData.left,
        top: canvasData.top,
        width: canvasData.width,
        height: canvasData.height
      });
    }
  };

  const handleScale = (target) => { // 'vertical' | 'horizontal'
    const cropper = cropperRef.current?.cropper;
    if (cropper) {
      const { scaleX, scaleY } = cropper.getData();
      if (target === 'horizontal') {
        cropper.scaleX(scaleX * -1);
        return;
      }
      cropper.scaleY(scaleY * -1);
    }
  };

  return (
    <Modal
      open={isOpen}
      onClose={onClose}
      size='large'
      className='jfModal--image-upload'
    >
      <div className='jfModal--title jfModal--title-row jfModal--title-bordered'>
        <div className='jfModal--title-icon jfModal--title-icon-small jfModal--title-icon-informative' aria-hidden='true'>
          <IconWandMagicFilled />
        </div>
        <div className='jfModal--title-content'>
          <h3>Edit Image</h3>
          <p>Crop and edit your image</p>
        </div>
      </div>
      <div className='jfModal--body'>
        <Cropper
          ref={cropperRef}
          src={(removeBg && nonBgImage) ? nonBgImage : imageSrc && URL.createObjectURL(imageSrc)}
          className='jfModal--image-upload-cropper'
          style={{ height: 400, width: '100%' }}
          zoomable={false}
          viewMode={1}
          autoCropArea={1}
          cropend={onCropChange}
          guides
        />
        <div className='jfModal--image-upload-adjust'>
          {useImageEditor && (<span className='jfModal--image-upload-adjust-text'>{t('Adjust Image')}</span>)}
          <div className='jfModal--image-upload-adjust-buttons'>
            {useImageEditor && (
              <div className='jfModal--image-upload-adjust-buttons-option'>
                <div className='jfModal--image-upload-adjust-buttons-option-inner'>
                  <Button
                    colorStyle='secondary'
                    variant='outline'
                    startIcon={<IconArrowsToLineHorizontal />}
                    onClick={() => handleScale('horizontal')}
                  />
                  <Button
                    colorStyle='secondary'
                    variant='outline'
                    startIcon={<IconArrowsToLineVertical />}
                    onClick={() => handleScale('vertical')}
                  />
                </div>
                <div className='jfModal--image-upload-adjust-buttons-option-inner'>
                  <Button
                    colorStyle='secondary'
                    variant='outline'
                    startIcon={<IconArrowRotateLeft />}
                    onClick={() => handleRotate(270)}
                  />
                  <Button
                    colorStyle='secondary'
                    variant='outline'
                    startIcon={<IconArrowRotateRight />}
                    onClick={() => handleRotate(90)}
                  />
                </div>
              </div>
            )}
            {useRemoveBg && (
              <Button
                colorStyle='secondary'
                variant='outline'
                className=''
                loader={removeImageBackgroundLoading}
                onClick={handleRemoveBackgroundBtn}
                startIcon={removeBg ? <IconArrowLeftHalf /> : <IconEraserFilled />}
              >
                {t(removeBg ? 'Reset' : 'Remove background')}
              </Button>
            )}
          </div>
        </div>
      </div>
      <div className='jfModal--actions jfModal--actions-end'>
        <ImageUploadInput
          ref={fileInputRef}
          accept={allowedTypes}
          onChange={onFileInputChange}
          size='medium'
          text={ALL_TEXTS.CHANGE_IMAGE}
        />
        <Button
          onClick={handleCrop}
          colorStyle='success'
          variant='filled'
          loader={uploadImageLoading}
        >
          {t(ALL_TEXTS.SAVE)}
        </Button>
      </div>
    </Modal>
  );
};

CropImageModal.propTypes = {
  isOpen: bool.isRequired,
  image: shape({ name: string, type: string }).isRequired,
  onSuccess: func.isRequired,
  onError: func.isRequired,
  useRemoveBg: bool.isRequired,
  useImageEditor: bool.isRequired,
  username: string.isRequired,
  handleUploadToServer: func.isRequired,
  onSelectImage: func.isRequired,
  onClose: func.isRequired,
  onRemoveImageBackground: func.isRequired,
  closeModal: func.isRequired,
  onFileInputChange: func.isRequired,
  allowedTypes: string.isRequired
};

export default CropImageModal;
