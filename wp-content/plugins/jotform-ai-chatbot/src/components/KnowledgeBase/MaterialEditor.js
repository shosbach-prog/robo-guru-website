import React, { useEffect, useState } from 'react';
import get from 'lodash/get';
import isEmpty from 'lodash/isEmpty';
import { func, object, string } from 'prop-types';

import '../../styles/material-editor.scss';

import { t } from '../../utils/index.js';
import {
  QuestionAnswer, TrainText, UploadDocument, URLInput
} from './KnowledgeTypes/index.js';

const MaterialEditor = ({
  materialType,
  setMaterialType,
  setEditingMaterialId,
  setStep,
  handleAdd,
  handleEdit,
  editingMaterialID,
  editingMaterial
}) => {
  const [errorMsg, setErrorMsg] = useState('');
  const [isSaving, setIsSaving] = useState(false);
  const editorType = materialType || editingMaterial?.type;

  useEffect(() => {
    let timeout;
    if (errorMsg) {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        setErrorMsg('');
      }, 5000);
    }
  }, [errorMsg]);

  if (!editorType) return null;

  const resetView = () => {
    setMaterialType('');
    setEditingMaterialId('');
    setStep('list');
    setErrorMsg('');
  };
  const _handleAdd = payload => {
    setIsSaving(true);
    // wrapped in promise to avoid checking wether it returns a promise or not
    Promise.resolve(
      handleAdd?.(payload)
    )
      .then(resetView)
      .catch(() => { setErrorMsg(t('An error occured.')); })
      .finally(() => { setIsSaving(false); });
  };

  const _handleEdit = payload => {
    const { status, type, ...payloadData } = payload;
    if (isEmpty(payloadData)) {
      return resetView();
    }

    setIsSaving(true);
    // wrapped in promise to avoid checking wether it returns a promise or not
    Promise.resolve(
      handleEdit?.(editingMaterialID, payload)
    )
      .then(resetView)
      .catch(() => { setErrorMsg(t('An error occured.')); })
      .finally(() => { setIsSaving(true); });
  };

  const handleSave = payload => {
    if (editingMaterialID) {
      _handleEdit(payload);
      return;
    }
    _handleAdd(payload);
  };

  const TRAIN_COMPONENTS = {
    QA: {
      component: <QuestionAnswer {...{ isLoading: isSaving, handleSave, editingMaterial }} />
    },
    URL: {
      component: <URLInput
        {...{
          setErrorMsg, isLoading: isSaving, handleSave, editingMaterial
        }}
      />
    },
    TEXT: {
      component: <TrainText {...{ isLoading: isSaving, handleSave, editingMaterial }} />
    },
    DOCUMENT: {
      component: <UploadDocument
        {...{
          isLoading: isSaving, handleSave, setErrorMsg, editingMaterial
        }}
      />
    }
  };

  const { component } = get(TRAIN_COMPONENTS, editorType, null);

  return (
    <div className='jfMaterialEditor'>
      {component}
      {!!errorMsg && (
        <p className='jfMaterialEditor--error-message'>{t(errorMsg)}</p>
      )}
    </div>
  );
};

MaterialEditor.propTypes = {
  materialType: string.isRequired,
  setMaterialType: func.isRequired,
  setEditingMaterialId: func.isRequired,
  setStep: func.isRequired,
  handleAdd: func.isRequired,
  handleEdit: func.isRequired,
  editingMaterialID: string.isRequired,
  editingMaterial: object
};

export default MaterialEditor;
