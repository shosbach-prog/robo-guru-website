import React, {
  useCallback, useEffect, useRef, useState
} from 'react';
import debounce from 'lodash/debounce';

import '../../styles/chat-guidelines.scss';

import { updateAgentProperty } from '../../api';
import { ALL_TEXTS, DELETE_INSTRUCTION_DEBOUNCE_TIMEOUT } from '../../constants';
import { useEffectIgnoreFirst, useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { generateTempId, scrollToBottom, t } from '../../utils';
import DeleteInstructionModal from '../DeleteInstructionModal';
import Button from '../UI/Button';
import { IconPlusSquareFilled, IconTrashFilled } from '../UI/Icon';
import Input from '../UI/Input';

const ChatGuidelines = () => {
  const { state, asyncDispatch, dispatch } = useWizard();

  const lastInputRef = useRef();
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [deleteId, setDeleteId] = useState(null);

  const {
    persona,
    previewAgentId,
    platformSettings: { PROVIDER_API_KEY }
  } = state;

  const formatInstructions = (personaText) => {
    if (!personaText) return [];
    return personaText.split('\n')
      .map(line => line.replace(/^- /, ''))
      .filter(line => line.trim() !== '')
      .map(line => ({ text: line, id: generateTempId() }));
  };

  const [instructions, setInstructions] = useState(formatInstructions(persona));

  const prepareInstructionsForUpdate = (guidelinesArray) => guidelinesArray.map(({ text }) => `${text}`).join('\n');

  useEffect(() => {
    dispatch(ACTION_CREATORS.setPersona(prepareInstructionsForUpdate(instructions)));
  }, [instructions]);

  useEffectIgnoreFirst(() => {
    const aiPersonaWrapper = document.querySelector('.jfpContent-wrapper--ai-persona');
    scrollToBottom(aiPersonaWrapper);
  }, [instructions]);

  const updateInstructions = async guidelinesData => {
    const data = { prop: 'persona', type: 'agent', value: prepareInstructionsForUpdate(guidelinesData) };
    await asyncDispatch(
      () => updateAgentProperty(previewAgentId, data, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentPropertyRequest,
      ACTION_CREATORS.updateAgentPropertySuccess,
      ACTION_CREATORS.updateAgentPropertyError
    );
  };

  const debouncedUpdateInstructions = useCallback(debounce(updateInstructions, DELETE_INSTRUCTION_DEBOUNCE_TIMEOUT), []);

  const handleChange = (value, id) => {
    setInstructions(prev => {
      const nextInstructions = prev.map(instruction => (instruction.id === id ? { ...instruction, text: value } : instruction));
      debouncedUpdateInstructions(nextInstructions);
      return nextInstructions;
    });
  };

  const handleModalDelete = async (id) => {
    setInstructions(prev => {
      const nextInstructions = prev.filter(instruction => instruction.id !== id);
      debouncedUpdateInstructions(nextInstructions);
      return nextInstructions;
    });
    setIsDeleteModalOpen(false);
  };

  const handleDeleteButtonClick = (id) => {
    setIsDeleteModalOpen(true);
    setDeleteId(id);
  };

  const handleAddClick = () => {
    if (lastInputRef.current?.value === '') return;
    setInstructions(prev => prev.concat({ id: generateTempId(), text: '' }));
    setTimeout(() => {
      lastInputRef.current?.focus();
    }, 0);
  };

  const handleKeyDown = e => {
    if (e.key === 'Enter') {
      handleAddClick();
    }
  };

  return (
    <div className='jfpContent-wrapper--ai-persona-title'>
      <div>
        <h3>{t(ALL_TEXTS.CHAT_GUIDELINES)}</h3>
        <p>{t(ALL_TEXTS.SET_CLEAR_RULES)}</p>
      </div>
      {instructions.map(({ id, text }) => (
        <div key={id} className='chat-guidelines'>
          <Input
            value={text}
            ref={lastInputRef}
            onChange={e => handleChange(e.target.value, id)}
            onKeyDown={handleKeyDown}
          />
          <Button
            className='chat-guidelines-delete-btn'
            startIcon={<IconTrashFilled />}
            onClick={() => handleDeleteButtonClick(id)}
          />
        </div>
      ))}
      <Button
        className='chat-guidelines-new-btn'
        startIcon={<IconPlusSquareFilled />}
        disabled={lastInputRef.current?.value === ''}
        onClick={handleAddClick}
      >{t(ALL_TEXTS.ADD_NEW)}
      </Button>
      <DeleteInstructionModal
        isOpen={isDeleteModalOpen}
        onDeleteClick={() => handleModalDelete(deleteId)}
        onCloseClick={() => setIsDeleteModalOpen(false)}
      />
    </div>
  );
};

export default ChatGuidelines;
