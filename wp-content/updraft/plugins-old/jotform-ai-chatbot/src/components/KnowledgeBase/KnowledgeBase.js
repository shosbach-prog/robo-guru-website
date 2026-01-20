import React, { useEffect, useState } from 'react';
import get from 'lodash/get';
import { array, bool, func } from 'prop-types';

import '../../styles/knowledge-base.scss';

import { TRAIN_TYPES } from '../../constants';
import { IconMessagePlusFilled } from '../UI/Icon';
import AutoTrainInfoBox from './AutoTrainInfoBox';
import InfoBox from './InfoBox';
import MaterialEditor from './MaterialEditor';
import MaterialList from './MaterialList';
import MaterialSelection from './MaterialSelection';

const shouldRenderBackButton = (step, totalMaterials) => {
  if (step === 'list') {
    return false;
  }
  if (step === 'select') {
    return totalMaterials > 0;
  }
  return true;
};

const KnowledgeBase = ({
  materials,
  isLoadingMaterials,
  handleAdd,
  handleEdit,
  handleDelete,
  handleBulkDelete
}) => {
  const [step, setStep] = useState('list'); // 'list' | 'select' | 'editor'
  const [editingMaterialID, setEditingMaterialId] = useState('');
  const editingMaterial = materials.find(material => material.uuid === editingMaterialID);
  const [materialType, setMaterialType] = useState(''); // 'TEXT' | 'URL' | 'DOCUMENT' | 'QA';

  const totalMaterials = materials.length;
  const isEditMode = !!editingMaterialID;

  useEffect(() => {
    if (editingMaterialID) {
      setStep('editor');
    }
  }, [editingMaterialID]);

  useEffect(() => {
    if (isLoadingMaterials || materials.length > 0) {
      setStep('list');
      return;
    }
    if (!isLoadingMaterials && materials.length === 0) {
      setStep('select');
    }
  }, [isLoadingMaterials, materials]);

  const handleBack = () => {
    const orderedSteps = ['list', 'select', 'editor'];
    const currStepIndex = orderedSteps.indexOf(step);
    const stepCount = isEditMode ? 2 : 1;
    if (currStepIndex > 0) {
      setMaterialType('');
      setStep(orderedSteps[currStepIndex - stepCount]);
      setEditingMaterialId('');
    }
  };

  const stepComponents = {
    list: <MaterialList {...{
      materials,
      editingMaterialID,
      setEditingMaterialId,
      step,
      setStep,
      isLoadingMaterials,
      handleAdd,
      onEditClick: handleEdit,
      onDeleteClick: handleDelete,
      handleBulkDelete,
      setMaterialType
    }}
    />,
    select: <MaterialSelection {...{
      materialType, setMaterialType, setStep
    }}
    />,
    editor: <MaterialEditor {...{
      materialType,
      step,
      editingMaterialID,
      editingMaterial,
      setEditingMaterialId,
      setMaterialType,
      setStep,
      handleAdd,
      handleEdit
    }}
    />
  };

  const isDetailsVisible = (step !== 'list');

  return (
    <div className='jfKnowledgeBase'>
      {step === 'list' && !isLoadingMaterials && <AutoTrainInfoBox />}
      {(step !== 'list') && (
      <InfoBox
        {...get(TRAIN_TYPES, materialType, {
          name: 'Knowledge Base',
          desc: 'Train your chatbot for context-aware responses to ensure accurate replies',
          icon: <IconMessagePlusFilled />
        })}
        {...(shouldRenderBackButton(step, totalMaterials) && { isBackVisible: true, handleBack })}
        {...(isDetailsVisible && {
          isNameVisible: true,
          isDescVisible: true,
          isIconVisible: true
        })}
      />
      )}
      {stepComponents[step]}
    </div>
  );
};

export default KnowledgeBase;

KnowledgeBase.propTypes = {
  materials: array,
  isLoadingMaterials: bool,
  handleAdd: func,
  handleEdit: func,
  handleDelete: func,
  handleBulkDelete: func
};
