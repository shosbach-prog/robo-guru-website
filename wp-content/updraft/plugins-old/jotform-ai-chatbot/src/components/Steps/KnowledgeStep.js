import React, { useEffect } from 'react';

import {
  addMaterial, bulkDeleteMaterial, deleteMaterial, fetchMaterials, saveInstallment, updateMaterial
} from '../../api';
import { ALL_TEXTS } from '../../constants';
import { useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { t, toCamelCase } from '../../utils';
import { KnowledgeBase } from '../KnowledgeBase';

const KnowledgeStep = () => {
  const { state, asyncDispatch } = useWizard();

  const {
    step,
    materials,
    previewAgentId,
    materialsLoading,
    platformSettings: { PROVIDER_API_URL, PROVIDER_API_KEY }
  } = state;

  useEffect(() => {
    if (materials.length > 0) return;
    const getMaterials = async () => {
      await asyncDispatch(
        () => fetchMaterials(previewAgentId, PROVIDER_API_KEY),
        ACTION_CREATORS.fetchMaterialsRequest,
        ACTION_CREATORS.fetchMaterialsSuccess,
        ACTION_CREATORS.fetchMaterialsError
      );
    };
    getMaterials();
  }, []);

  useEffect(() => {
    saveInstallment(`${toCamelCase(step)}Step`);
  }, []);

  const handleAdd = async newMaterial => {
    await asyncDispatch(
      () => addMaterial(previewAgentId, newMaterial, PROVIDER_API_KEY),
      ACTION_CREATORS.addMaterialRequest,
      ACTION_CREATORS.addMaterialSuccess,
      ACTION_CREATORS.addMaterialError
    );
  };

  const handleEdit = async (materialId, updatedMaterialData) => {
    await asyncDispatch(
      () => updateMaterial(previewAgentId, materialId, updatedMaterialData, PROVIDER_API_KEY),
      ACTION_CREATORS.updateMaterialRequest,
      ACTION_CREATORS.updateMaterialSuccess,
      ACTION_CREATORS.updateMaterialError
    );
  };

  const handleDelete = async materialId => {
    await asyncDispatch(
      () => deleteMaterial(previewAgentId, materialId, PROVIDER_API_KEY),
      ACTION_CREATORS.deleteMaterialRequest,
      ACTION_CREATORS.deleteMaterialSuccess,
      ACTION_CREATORS.deleteMaterialError,
      materialId
    );
  };

  const handleBulkDelete = async materialIds => {
    await asyncDispatch(
      () => bulkDeleteMaterial(previewAgentId, materialIds, PROVIDER_API_KEY),
      ACTION_CREATORS.bulkDeleteMaterialRequest,
      ACTION_CREATORS.bulkDeleteMaterialSuccess,
      ACTION_CREATORS.bulkDeleteMaterialError,
      materialIds
    );
  };

  return (
    <>
      <div className='jfpContent-wrapper--knowledge' data-js='knowledge-scroll-container'>
        <h2 className='sr-only'>{t(ALL_TEXTS.KNOWLEDGE_BASE)}</h2>
        <KnowledgeBase
          materials={materials}
          isLoadingMaterials={materialsLoading}
          requestBaseURL={PROVIDER_API_URL}
          handleAdd={handleAdd}
          handleEdit={handleEdit}
          handleDelete={handleDelete}
          handleBulkDelete={handleBulkDelete}
        />
      </div>
    </>
  );
};

export default KnowledgeStep;
