import React, { useCallback, useState } from 'react';
import {
  array, bool, func, string
} from 'prop-types';

import NoResultImg from '../../assets/svg/material-no-result.svg';
import { buildSearchText, scoreItem, tokenizeQuery } from '../../utils';
import Button from '../UI/Button';
import { IconPlusSquareFilled } from '../UI/Icon';
import MaterialListItem from './MaterialListItem';
import MaterialSearch from './MaterialSearch';

// Tighter, size-independent threshold (and/or keep top-k)
const filterMaterials = (
  materials,
  searchText,
  materialStatusFilter,
  selectedMaterial = 'ALL'
) => {
  const q = (searchText || '').trim();
  const base = materials
    .map(m => ({ ...m, _search: buildSearchText(m) }))
    .filter(m => (selectedMaterial === 'ALL' ? true : m.type === selectedMaterial))
    .filter(m => (!materialStatusFilter || m.status === materialStatusFilter));

  // Empty query → return all (sorted by title, or your default)
  if (!q.length) {
    return base
      .map(m => ({ ...m, fuzzyScore: 1 }))
      .sort((a, b) => (a.title || '').localeCompare(b.title || ''));
  }

  const qTerms = tokenizeQuery(q);
  if (!qTerms.length) return [];

  // Score
  const scored = base.map(m => {
    const { hits, titleHits, score } = scoreItem(qTerms, m._search);
    return {
      ...m, fuzzyScore: score, _hits: hits, _titleHits: titleHits
    };
  });

  // Require at least one term hit (pre-filter), then rank
  const candidates = scored
    .filter(m => m._hits > 0)
    .sort((a, b) => {
      // sort by score, then more term hits, then more title hits, then title asc
      if (b.fuzzyScore !== a.fuzzyScore) return b.fuzzyScore - a.fuzzyScore;
      if (b._hits !== a._hits) return b._hits - a._hits;
      if (b._titleHits !== a._titleHits) return b._titleHits - a._titleHits;
      return (a.title || '').localeCompare(b.title || '');
    });

  // Threshold & top-K (keeps list tidy)
  const THRESHOLD = 0.35; // tweakable
  const K = 10;

  const strong = candidates.filter(m => m.fuzzyScore >= THRESHOLD).slice(0, K);

  // If nothing is strong, return top-K anyway (so the UI shows *something*).
  return (strong.length ? strong : candidates.slice(0, K))
    .map(({
      _search, _hits, _titleHits, ...rest
    }) => rest);
};

const MaterialList = ({
  materials,
  isLoadingMaterials = false,
  editingMaterialID = '',
  setEditingMaterialId,
  onDeleteClick,
  setStep,
  setMaterialType
}) => {
  const [materialTypeFilter, setMaterialTypeFilter] = useState('ALL'); // 'TEXT' | 'URL' | 'DOCUMENT' | 'QA'
  const [materialStatusFilter, setMaterialStatusFilter] = useState(''); // '' | 'ACTION_REQUIRED'
  const [searchText, setSearchText] = useState('');

  const filteredMaterials = useCallback(() => (
    filterMaterials(materials, searchText, materialStatusFilter, materialTypeFilter)
  ), [materials, searchText, materialStatusFilter, materialTypeFilter]);

  const handleEditClick = (materialId, materialType) => {
    setEditingMaterialId(materialId);
    setMaterialType(materialType);
  };

  return (
    <>
      <Button
        startIcon={<IconPlusSquareFilled />}
        onClick={() => setStep('select')}
        disabled={isLoadingMaterials}
        fullWidth
      >
        Add new knowledge
      </Button>
      <MaterialSearch
        {...{
          materials,
          materialTypeFilter,
          materialStatusFilter,
          setMaterialTypeFilter,
          setMaterialStatusFilter,
          setSearchText

        }}
      />
      <div className='jfMaterialList'>
        {filteredMaterials().length > 0 && (
          filteredMaterials().map((material) => (
            <MaterialListItem
              key={material.uuid}
              material={material}
              onEditClick={handleEditClick}
              onDeleteClick={onDeleteClick}
              editingMaterialID={editingMaterialID}
            />
          ))
        )}
        {(!isLoadingMaterials && filteredMaterials().length === 0) && (
          <div className='jfMaterialList--no-result'>
            <div className='jfMaterialList--no-result-icon'>
              <NoResultImg />
            </div>
            <div className='jfMaterialList--no-result-content'>
              <h4 className='jfMaterialList--no-result-content-title'>Oops, No Result Found</h4>
              <p className='jfMaterialList--no-result-content-desc'>Sorry we could not find any results</p>
            </div>
          </div>
        )}
      </div>
      {isLoadingMaterials && <div className='knowledge-base-loading-wrapper'><div className='create-page-loading--spinner small' /></div>}
    </>
  );
};

export default MaterialList;

MaterialList.propTypes = {
  materials: array,
  isLoadingMaterials: bool,
  editingMaterialID: string,
  setEditingMaterialId: func,
  setStep: func,
  onDeleteClick: func,
  setMaterialType: string
};
