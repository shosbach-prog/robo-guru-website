import React, { useRef, useState } from 'react';
import { bool, func, object } from 'prop-types';

import { getNonValidInputs, safeJSONParse, t } from '../../../utils/index.js';
import Button from '../../UI/Button.js';
import Input from '../../UI/Input.js';
import Textarea from '../../UI/Textarea.js';
import LabelWrapperItem from '../LabelWrapperItem.js';

const QuestionAnswer = ({
  isLoading,
  handleSave,
  editingMaterial
}) => {
  const questionRef = useRef(null);
  const answerRef = useRef(null);
  const editingQA = safeJSONParse(editingMaterial?.data);
  const [inputValidation, setInputValidation] = useState([]);

  const isEditingMode = !!editingMaterial;

  const validateAndSend = () => {
    // TODO: show warning message
    if (questionRef.current?.value?.trim() === '' || answerRef.current?.value?.trim() === '') return;
    if (questionRef.current && answerRef.current) {
      setInputValidation(getNonValidInputs({ question: questionRef.current, answer: answerRef.current }, 'qa'));
      if (inputValidation.length === 0) {
        return handleSave({
          type: 'QA',
          data: JSON.stringify({ question: questionRef.current.value, answer: answerRef.current.value }),
          ...(isEditingMode && { status: editingMaterial?.status === 'ACTION_REQUIRED' ? 'PROCESSED' : editingMaterial?.status })
        });
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
        <LabelWrapperItem
          heading='Question'
          desc='Train the AI based on your questions & answers'
        >
          <div className='question-wrapper'>
            <Input
              className='w-full'
              size='medium'
              placeholder={t('Enter a brief, clear question that you\'d like the Agent to answer.')}
              colorStyle={inputValidation.includes('question') ? 'error' : 'default'}
              onKeyDown={handleKeyDown}
              defaultValue={editingQA?.question}
              ref={questionRef}
            />
            <div className='answer-wrapper'>
              <span className='answer-title'>{t('Answer')}</span>
              <Textarea
                className='w-full h-52 border border-navy-100 radius'
                size='medium'
                defaultValue={editingQA?.answer}
                placeholder={t('Provide a detailed and informative answer that will help the Agent respond accurately to the question.')}
                colorStyle={inputValidation.includes('answer') ? 'error' : 'default'}
                ref={answerRef}
              />
            </div>
          </div>
        </LabelWrapperItem>
      </div>
      <div className='jfMaterialEditor--footer'>
        <Button
          className='w-24'
          size='medium'
          colorStyle='success'
          loader={isLoading}
          onClick={validateAndSend}
        >
          {t('Save')}
        </Button>
      </div>
    </div>
  );
};

QuestionAnswer.propTypes = {
  isLoading: bool.isRequired,
  handleSave: func.isRequired,
  editingMaterial: object
};

export default QuestionAnswer;
