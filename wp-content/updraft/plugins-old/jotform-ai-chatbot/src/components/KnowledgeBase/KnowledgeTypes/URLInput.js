import React, { useRef, useState } from 'react';
import { bool, func, object } from 'prop-types';

import { TRAIN_TYPES } from '../../../constants';
import { addHttpsPrefix, getNonValidInputs, t } from '../../../utils';
import Button from '../../UI/Button.js';
import Input from '../../UI/Input.js';
import LabelWrapperItem from '../LabelWrapperItem.js';

const URLInput = ({
  handleSave,
  isLoading,
  setErrorMsg,
  editingMaterial
}) => {
  const inputRef = useRef(null);
  const [inputValidation, setInputValidation] = useState([]);
  const [changedMaterialData, setChangedMaterialData] = useState({});

  const handleMaterialDataChange = e => {
    const { id, value } = e.target;
    setChangedMaterialData({ ...changedMaterialData, [id]: value });
  };
  const isEditingMode = !!editingMaterial;

  const validateAndSend = () => {
    if (inputRef.current) {
      const inputValue = inputRef.current.value;
      const value = addHttpsPrefix(inputValue);
      const validationResult = getNonValidInputs(value, 'url');
      setInputValidation(validationResult);
      if (validationResult.length === 0) {
        return handleSave({
          type: 'URL',
          ...changedMaterialData,
          ...(isEditingMode && { status: editingMaterial?.status === 'ACTION_REQUIRED' ? 'PROCESSED' : editingMaterial?.status })
        });
      }
      if (validationResult.includes('invalid-url')) {
        setErrorMsg('Please enter a valid URL.');
      }
    }
  };

  const handleKeyDown = e => {
    if (e.key === 'Enter') {
      validateAndSend();
    }
  };

  return (
    <div className='jfMaterialEditor--container'>
      <div className='jfMaterialEditor--inner'>
        <div className='crawl-url'>
          <div className='crawl-url-container'>
            {isEditingMode && (
              <LabelWrapperItem heading='Title' desc='' customClass='p-0'>
                <Input
                  id='title'
                  onChange={handleMaterialDataChange}
                  defaultValue={editingMaterial?.title || TRAIN_TYPES.URL.name}
                />
              </LabelWrapperItem>
            )}

            <LabelWrapperItem
              heading='Enter a URL'
              desc='Provide a URL for your agent to analyze'
            >
              <div className='crawl-url-input-container'>
                <Input
                  id='url'
                  onChange={handleMaterialDataChange}
                  className='crawl-url-input'
                  size='large'
                  placeholder='example.com'
                  onKeyDown={handleKeyDown}
                  defaultValue={editingMaterial?.meta?.url.replace(/^https?:\/\//, '')}
                  prefix={{
                    as: 'span',
                    text: 'https://'
                  }}
                  type='url'
                  colorStyle={inputValidation.includes('url') ? 'error' : 'default'}
                  ref={inputRef}
                />
              </div>
            </LabelWrapperItem>
          </div>
        </div>
      </div>
      <div className='jfMaterialEditor--footer'>
        <Button
          size='medium'
          // colorStyle='default'
          loader={isLoading}
          onClick={validateAndSend}
        >
          {t('Crawl')}
        </Button>
      </div>
    </div>
  );
};

URLInput.propTypes = {
  handleSave: func.isRequired,
  isLoading: bool,
  setErrorMsg: func.isRequired,
  editingMaterial: object
};

export default URLInput;
