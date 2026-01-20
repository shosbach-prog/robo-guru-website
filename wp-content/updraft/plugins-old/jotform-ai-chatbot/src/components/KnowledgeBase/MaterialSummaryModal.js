import React, { useEffect, useState } from 'react';
import classNames from 'classnames';
import {
  bool, func, object, string
} from 'prop-types';

import '../../styles/material-summary-modal.scss';

import { getMaterialById } from '../../api';
import { platformSettings, t } from '../../utils';
import Button from '../UI/Button';
import { IconAnnotationInfoFilled } from '../UI/Icon';
import Modal from '../UI/Modal';

const getTextBlock = (title, content, contentCustomClass) => (
  <div className='getTextBlock'>
    <h3 className='getTextBlock--title'>{t(title)}</h3>
    <div className={`getTextBlock--content ${contentCustomClass}`}>
      {content}
    </div>
  </div>
);

const MaterialSummaryModal = ({
  material,
  icon,
  iconClassName,
  materialTitle,
  materialURL,
  materialContent,
  isOpen,
  onClose
}) => {
  const [trainedData, setTrainedData] = useState('');

  const closeModal = () => {
    onClose();
  };

  useEffect(() => {
    if (!isOpen) return;
    getMaterialById(material.agent_id, material.uuid, platformSettings.PROVIDER_API_KEY)
      .then(materialWithTrainedData => { setTrainedData(materialWithTrainedData.trained_data); });
  }, [isOpen]);

  return (
    <Modal
      open={isOpen}
      onClose={closeModal}
      ariaLabel={t('Material Summary Modal')}
      size='large'
    >
      <div className='material-summary-modal--title'>
        <span className={classNames('material-summary-modal--title-icon', iconClassName)}>
          {icon || <IconAnnotationInfoFilled />}
        </span>
        <div className='material-summary-modal--title-text'>
          <strong>{materialTitle}</strong>
          <span>{materialURL}</span>
        </div>
      </div>
      <div className='material-summary-modal--content'>
        {materialContent && getTextBlock('Summary', materialContent, 'summary')}
        {getTextBlock('Content', trainedData || '', 'content')}
      </div>
      <div className='material-summary-modal--footer'>
        <Button onClick={closeModal} colorStyle='primary' variant='filled'>{t('Done')}</Button>
      </div>
    </Modal>
  );
};

export default MaterialSummaryModal;

MaterialSummaryModal.propTypes = {
  material: object,
  icon: string,
  iconClassName: string,
  materialURL: string,
  materialTitle: string,
  materialContent: string,
  isOpen: bool,
  onClose: func
};
