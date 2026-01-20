import React, { useState } from 'react';
import classnames from 'classnames';
import get from 'lodash/get';
import { func, object, string } from 'prop-types';

import '../../styles/material-list.scss';

import { MATERIAL_STATUS, TRAIN_TYPES } from '../../constants';
import {
  convertServerDateToUserTimezone, safeJSONParse, t
} from '../../utils';
import Button from '../UI/Button';
import { IconAnnotationInfoFilled, IconArrowsFromCenter, IconExclamationCircleFilled } from '../UI/Icon';
import LineLoader from './LineLoader';
import MaterialDeleteModal from './MaterialDeleteModal';
import MaterialItemContextMenu from './MaterialItemContextMenu';
import MaterialSummaryModal from './MaterialSummaryModal';

// eslint-disable-next-line react/prop-types
const MaterialStatus = ({ status, materialStatus }) => (
  <div className='jfMaterialStatus'>
    <LineLoader {...{ status }} />
    <div className='jfMaterialStatus--text'>
      <div className='jfMaterialStatus--text-loader loader' />
      {t(materialStatus)}
    </div>
  </div>
);

const getTitle = props => {
  const {
    type, title = '', data = '', name = ''
  } = props;

  if (type === 'QA') {
    return get(safeJSONParse(data), 'question', '');
  }

  return title || name;
};

const getMaterialURL = props => {
  const { type, meta } = props;
  const url = get(meta, type === 'URL' ? 'url' : 'filePath', '');

  return url
    ? (
      <a
        className='jfMaterialList--item-content-title-text-link'
        href={url}
        target='_blank'
        rel='noreferrer'
      >
        {meta?.fileName || url}
      </a>
    )
    : null;
};

const getMaterialContent = material => {
  const { type, data, meta } = material;
  if (['URL', 'DOCUMENT', 'TEXT'].includes(type)) {
    return get(meta, 'summary', data || '');
  }
  // QA
  return get(safeJSONParse(data), 'answer', '');
};

const MaterialListItem = ({
  material,
  onEditClick,
  onDeleteClick
}) => {
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [isSummaryModalOpen, setIsSummaryModalOpen] = useState(false);

  const {
    uuid, type, status, created_at: createdAt
  } = material;

  const {
    icon, name, iconClassName
  } = get(TRAIN_TYPES, type, {
    color: '', icon: '', fillColor: '', name: '', iconClassName: ''
  });

  const convertedDate = convertServerDateToUserTimezone(
    createdAt || 'Invalid Date',
    'America/New_York',
    'MMM D, YYYY h:mm A'
  );

  const materialStatus = MATERIAL_STATUS[type]?.[status];
  const showSummary = type !== 'QA' && status === 'PROCESSED';
  const materialURL = getMaterialURL(material);
  const materialTitle = getTitle({ ...material, name });
  const materialContent = getMaterialContent(material);

  const handleDeleteMaterialConfirm = () => {
    onDeleteClick?.(uuid);
    setIsDeleteModalOpen(false);
  };

  const handleEditMaterialClick = () => {
    onEditClick?.(uuid, type);
  };

  const openMaterialSummary = () => {
    setIsSummaryModalOpen(true);
  };
  const closeMaterialSummary = () => {
    setIsSummaryModalOpen(false);
  };

  const openMaterialDeleteConfirmation = () => {
    setIsDeleteModalOpen(true);
  };
  const closeMaterialDeleteConfirmation = () => {
    setIsDeleteModalOpen(false);
  };

  return (
    <div
      className={classnames(
        'jfMaterialList--item',
        {
          isActionRequired: status === 'ACTION_REQUIRED'
        }
      )}
    >
      <MaterialSummaryModal
        {...{
          material,
          icon,
          iconClassName,
          materialURL,
          materialTitle,
          materialContent: showSummary ? materialContent : '',
          isOpen: isSummaryModalOpen,
          onClose: closeMaterialSummary
        }}
      />
      <MaterialDeleteModal
        isOpen={isDeleteModalOpen}
        onCloseClick={closeMaterialDeleteConfirmation}
        onDeleteClick={handleDeleteMaterialConfirm}
      />
      <div className='jfMaterialList--item-content'>
        <div className='jfMaterialList--item-content-title'>
          <div className={classnames('jfMaterialList--item-content-title-icon', iconClassName)}>
            {icon || <IconAnnotationInfoFilled />}
          </div>
          <div className='jfMaterialList--item-content-title-text'>
            <strong>
              {materialTitle}
            </strong>
            {(type === 'DOCUMENT' || type === 'URL') && (
            <span>
              {materialURL}
            </span>
            )}
          </div>
          {showSummary && (
            <Button
              size='small'
              colorStyle='secondary'
              startIcon={<IconArrowsFromCenter />}
              onClick={openMaterialSummary}
            />
          )}
        </div>
        {(status === 'ACTION_REQUIRED' || materialContent) && (
          <div className='jfMaterialList--item-content-text'>
            {status === 'ACTION_REQUIRED'
              ? (
                <div className='jfMaterialList--item-error'>
                  <IconExclamationCircleFilled />
                  <span>{t('Answer needed.')}</span>
                </div>
              )
              : materialContent}
          </div>
        )}
      </div>
      {['PROCESSED', 'FAILED', 'ACTION_REQUIRED'].includes(status) ? (
        <div className='jfMaterialList--item-footer'>
          <div className='jfMaterialList--item-footer-text'>
            {materialStatus ? t(`${materialStatus} on `) : ''}
            {convertedDate}
          </div>
          <div>
            <MaterialItemContextMenu
              {...{
                type,
                showSummary
              }}
              onEditClick={handleEditMaterialClick}
              onDeleteClick={openMaterialDeleteConfirmation}
              onSummaryClick={openMaterialSummary}
            />
          </div>
        </div>
      ) : (
        <MaterialStatus {...{ materialStatus, status }} />
      )}
    </div>
  );
};

export default MaterialListItem;

MaterialListItem.propTypes = {
  material: object,
  type: string,
  onEditClick: func,
  onDeleteClick: func
};
