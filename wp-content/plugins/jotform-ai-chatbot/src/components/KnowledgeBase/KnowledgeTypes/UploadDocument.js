import React, { useMemo, useRef, useState } from 'react';
import { getOr } from 'lodash/fp';
import { bool, func, object } from 'prop-types';

import { TRAIN_TYPES } from '../../../constants';
import { t } from '../../../utils';
import Button from '../../UI/Button.js';
import {
  IconCloudArrowUp, IconDocumentDocFilled, IconDocumentPdfFilled, IconXmark
} from '../../UI/Icon';
import Input from '../../UI/Input.js';
import LabelWrapperItem from '../LabelWrapperItem.js';

const ALLOWED_FILE_TYPES = [
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];

// eslint-disable-next-line no-unused-vars
const getIconName = file => (file?.type.indexOf('pdf') > -1 ? 'document_pdf' : 'document_doc');

const ListFile = ({
  // eslint-disable-next-line react/prop-types
  selectedFile, setMockFile, setActualFile
}) => {
  const iconName = getIconName(selectedFile);
  return (
    <div
      className='upload-document-preview'
    >
      {iconName === 'document_doc' ? (
        <IconDocumentDocFilled className='upload-document-preview-icon' />
      ) : (
        <IconDocumentPdfFilled className='upload-document-preview-icon' />
      )}
      {/* eslint-disable-next-line react/prop-types */}
      <p className='upload-document-preview-text'>{selectedFile?.name}</p>
      <Button
        variant='ghost'
        className='upload-document-preview-text-close'
        endIcon={<IconXmark />}
        onClick={() => {
          setMockFile();
          setActualFile();
        }}
      />
    </div>
  );
};

const UploadDocument = ({
  isLoading,
  handleSave,
  setErrorMsg,
  editingMaterial
}) => {
  const [actualFile, setActualFile] = useState();
  const [mockFile, setMockFile] = useState(editingMaterial?.meta ? { name: editingMaterial.meta.fileName, type: editingMaterial.meta.fileName } : undefined);
  const selectedFile = useMemo(() => actualFile || mockFile, [actualFile, mockFile]);
  const fileInputRef = useRef(null);
  const [changedMaterialData, setChangedMaterialData] = useState({});

  const handleMaterialDataChange = e => {
    const { id, value } = e.target;
    setChangedMaterialData({ ...changedMaterialData, [id]: value });
  };
  const isEditingMode = !!editingMaterial;

  const handleFileSelect = file => {
    if (ALLOWED_FILE_TYPES.includes(file.type)) {
      setActualFile(file);
      handleMaterialDataChange({ target: { id: 'file', value: file } });
    } else {
      setErrorMsg(t('Please upload a PDF or DOCX file'));
    }
  };

  const handleDrop = e => {
    e.preventDefault();

    const file = getOr(null, '[0]', e.dataTransfer.files);
    if (file) {
      handleFileSelect(file);
    }
  };

  const handleFileInputChange = e => {
    const file = getOr(null, '[0]', e.target.files);
    if (file) {
      handleFileSelect(file);
    }
  };

  const handleUploadButtonClick = () => {
    handleSave({
      type: 'DOCUMENT',
      ...changedMaterialData,
      ...(isEditingMode && { status: editingMaterial?.status === 'ACTION_REQUIRED' ? 'PROCESSED' : editingMaterial?.status })
    });
  };

  // eslint-disable-next-line react/no-unstable-nested-components
  const UploadFile = () => (
    <div
      onDragOver={e => e.preventDefault()}
      onDrop={handleDrop}
      className='upload-area-container'
    >
      <IconCloudArrowUp className='cloud-icon' />
      <span className='upload-area-title'>{t('Upload a Document')}</span>
      <p className='upload-area-desc'>
        <span>
          Drag and drop your files here or
          {/* eslint-disable-next-line jsx-a11y/no-static-element-interactions,jsx-a11y/click-events-have-key-events */}
          <span className='upload-area-link' onClick={() => fileInputRef.current?.click()}> upload a file</span>
        </span>
      </p>
      <input
        type='file'
        onChange={handleFileInputChange}
        className='hidden'
        ref={fileInputRef}
      />
    </div>
  );

  return (
    <div className='jfMaterialEditor--container'>
      <div className='jfMaterialEditor--inner'>
        <div className='upload-document'>
          <div className='upload-document-container'>
            {isEditingMode && (
              <LabelWrapperItem
                heading='Title'
                desc=''
                customClass='p-0'
              >
                <Input
                  id='title'
                  onChange={handleMaterialDataChange}
                  defaultValue={editingMaterial?.title || TRAIN_TYPES.DOCUMENT.name}
                />
              </LabelWrapperItem>
            )}

            <LabelWrapperItem
              heading='Upload Document'
              desc='Train the AI based on content from the document'
              customClass='label'
            />
            {selectedFile
              ? <ListFile {...{ selectedFile, setMockFile, setActualFile }} />
              : <UploadFile />}
          </div>
        </div>
      </div>
      <div className='jfMaterialEditor--footer'>
        <div className='upload-document-btn'>
          <Button
            size='medium'
            colorStyle='success'
            loader={isLoading}
            onClick={handleUploadButtonClick}
            disabled={!selectedFile}
          >
            {t('Save')}
          </Button>
        </div>
      </div>
    </div>
  );
};

UploadDocument.propTypes = {
  handleSave: func.isRequired,
  isLoading: bool,
  setErrorMsg: func.isRequired,
  editingMaterial: object
};

export default UploadDocument;
