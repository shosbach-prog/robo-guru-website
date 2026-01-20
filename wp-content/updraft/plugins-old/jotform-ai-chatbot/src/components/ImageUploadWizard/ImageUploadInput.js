import React, { forwardRef } from 'react';
import { func, string } from 'prop-types';

import { ALL_TEXTS } from '../../constants';
import Button from '../UI/Button';

const ImageUploadInput = forwardRef(({
  accept,
  onChange,
  size,
  text
}, fileInputRef) => {
  const handleChangeImageClick = () => {
    fileInputRef.current.click();
  };

  return (
    <>
      <Button
        onClick={(handleChangeImageClick)}
        variant='outline'
        size={size}
      >
        {text}
      </Button>
      <input
        className='hidden'
        ref={fileInputRef}
        type='file'
        accept={accept}
        onChange={onChange}
      />
    </>
  );
});

ImageUploadInput.defaultProps = {
  size: '',
  text: ALL_TEXTS.CUSTOM_AVATAR
};

ImageUploadInput.propTypes = {
  accept: string.isRequired,
  onChange: func.isRequired,
  size: string,
  text: string
};

export default ImageUploadInput;
