import React, {
  useCallback, useEffect, useState
} from 'react';
import debounce from 'lodash/debounce';

import '../../styles/chattiness.scss';

import { saveInstallment, updateAgent, updateAgentProperty } from '../../api';
import {
  ALL_TEXTS, CHATTINESS_LEVELS, CUSTOMIZATION_KEYS, GREETING_TEXT_REQ_DEBOUNCE_TIMEOUT,
  LANGUAGES, TONE_OF_VOICES, VERBAL_TOGGLE, VISIBILITY_LAYOUT, WRITING_DEBOUNCE_TIMEOUT
} from '../../constants';
import { useHideGreetingTooltip, useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { t, toCamelCase } from '../../utils';
import Avatar from '../Avatar';
import { ChatGuidelines } from '../ChatGuidelines';
import Button from '../UI/Button';
import Dropdown from '../UI/Dropdown';
import Input from '../UI/Input';
import Toggle from '../UI/Toggle';

const AiPersonaStep = () => {
  const {
    asyncDispatch, state, dispatch
  } = useWizard();

  const indexToPercentage = index => `${(Number(index) - 1) * (100 / (CHATTINESS_LEVELS.length - 1))}%`;

  const {
    step,
    agentName,
    agentRole,
    agentLanguage,
    agentToneOfVoice,
    customizations,
    agentChattiness,
    previewAgentId,
    platformSettings: { PROVIDER_API_KEY }
  } = state;

  const { greeting, greetingMessage, layout } = customizations;

  const greetingBool = greeting === VERBAL_TOGGLE.YES;

  const [agentNameState, setAgentNameState] = useState(agentName);
  const [agentRoleState, setAgentRoleState] = useState(agentRole);
  const [greetingMessageState, setGreetingMessageState] = useState(greetingMessage);

  useEffect(() => {
    setAgentNameState(agentName);
  }, [agentName]);

  useEffect(() => {
    saveInstallment(`${toCamelCase(step)}Step`);
  }, []);

  useHideGreetingTooltip(greetingBool);

  // agent name
  const updateAgentName = async value => {
    await asyncDispatch(
      () => updateAgent(previewAgentId, { name: value }, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentRequest,
      ACTION_CREATORS.updateAgentSuccess,
      ACTION_CREATORS.updateAgentError
    );
    dispatch(ACTION_CREATORS.setAgentName(value));
  };

  const debouncedUpdateAgentName = useCallback(debounce(updateAgentName, WRITING_DEBOUNCE_TIMEOUT), []);

  const handleNameChange = value => {
    setAgentNameState(value);
    debouncedUpdateAgentName(value);
  };

  // greeting message
  const updateCustomization = async ({ key, value }) => {
    const updatedCustomizations = { ...customizations, [key]: value };
    await asyncDispatch(
      () => updateAgentProperty(previewAgentId, { prop: 'popover', type: 'embed', value: JSON.stringify(updatedCustomizations) }, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentPropertyRequest,
      ACTION_CREATORS.updateAgentPropertySuccess,
      ACTION_CREATORS.updateAgentPropertyError
    );
  };

  const updateGreetingText = value => {
    dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.GREETING_MESSAGE, value));
    updateCustomization({ key: CUSTOMIZATION_KEYS.GREETING_MESSAGE, value });
  };

  const debouncedUpdateCustomization = useCallback(debounce(updateGreetingText, GREETING_TEXT_REQ_DEBOUNCE_TIMEOUT), []);
  const handleChangeGreetingText = value => {
    setGreetingMessageState(value);
    debouncedUpdateCustomization(value);
  };

  const handleChangeGreeting = value => {
    const verbalVal = value ? VERBAL_TOGGLE.YES : VERBAL_TOGGLE.NO;
    dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.GREETING, verbalVal));
    updateCustomization({ key: CUSTOMIZATION_KEYS.GREETING, value: verbalVal });
  };

  // agent role & chattiness
  const updateAgentProp = async (prop, value) => {
    await asyncDispatch(
      () => updateAgentProperty(previewAgentId, { prop, type: 'agent', value }, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentPropertyRequest,
      ACTION_CREATORS.updateAgentPropertySuccess,
      ACTION_CREATORS.updateAgentPropertyError
    );
    if (prop === 'chattiness') dispatch(ACTION_CREATORS.setAgentChattiness(value));
    if (prop === 'language') dispatch(ACTION_CREATORS.setAgentLanguage(value));
    if (prop === 'tone') dispatch(ACTION_CREATORS.setAgentToneOfVoice(value));
  };

  const updateAgentRole = async value => {
    await updateAgentProp('role', value);
    dispatch(ACTION_CREATORS.setAgentRole(value));
  };

  const debouncedUpdateAgentRole = useCallback(debounce(updateAgentRole, WRITING_DEBOUNCE_TIMEOUT), []);
  const handleAgentRoleChange = value => {
    setAgentRoleState(value);
    debouncedUpdateAgentRole(value);
  };

  const handleAgentPropChange = (prop, value) => {
    updateAgentProp(prop, value);
  };

  const roleOptions = ['Customer Service Agent', 'Human Resources Agent', 'Contact Sales Agent'];

  return (
    <>
      <div className='jfpContent-wrapper--ai-persona'>
        <h2 className='sr-only'>{t(ALL_TEXTS.AI_PERSONA)}</h2>
        <Avatar />
        <hr className='jfpContent-wrapper--line line-2x' />
        {/* agent name */}
        <div className='jfpContent-wrapper--ai-persona-title'>
          <div>
            <h3>{t(ALL_TEXTS.AGENT_NAME)}</h3>
            <p>{t(ALL_TEXTS.GIVE_A_NAME_TO_YOUR_AGENT_THAT_WILL_BE_DISPLAYED_IN_THE_CONVERSATION)}</p>
          </div>
          <Input
            type='text'
            value={agentNameState}
            onChange={e => handleNameChange(e.target.value)}
          />
        </div>
        <hr className='jfpContent-wrapper--line line-2x' />
        {/* agent role */}
        <div className='jfpContent-wrapper--ai-persona-title'>
          <div>
            <h3>{t(ALL_TEXTS.AGENT_ROLE)}</h3>
            <p>{t(ALL_TEXTS.DESCRIPTION_YOUR_AGENTS_JOB_TITLE)}</p>
          </div>
          <Input
            type='text'
            value={agentRoleState}
            onChange={e => handleAgentRoleChange(e.target.value)}
          />
          <div className='role-options'>
            {roleOptions.map(option => (
              <Button
                key={option}
                variant='ghost'
                rounded
                title={option}
                size='small'
                onClick={() => {
                  handleAgentRoleChange(option);
                }}
              >
                {option}
              </Button>
            ))}
          </div>
        </div>
        <hr className='jfpContent-wrapper--line line-2x' />
        {/* default language */}
        <div className='jfpContent-wrapper--ai-persona-title'>
          <div>
            <h3>{t(ALL_TEXTS.DEFAULT_LANGUAGE)}</h3>
            <p>{t(ALL_TEXTS.SELECT_THE_LANGUAGE)}</p>
          </div>
          <Dropdown
            colorStyle='default'
            size='small'
            theme='light'
            value={agentLanguage}
            onChange={value => handleAgentPropChange('language', value)}
          >
            {LANGUAGES.map(({ value, text, icon }) => (
              <option
                key={value}
                value={value}
              >
                {`${icon} ${t(text)}`}
              </option>
            ))}
          </Dropdown>
        </div>
        {/* tone of voice */}
        <div className='jfpContent-wrapper--ai-persona-title'>
          <div>
            <h3>{t(ALL_TEXTS.TONE_OF_VOICE)}</h3>
            <p>{t(ALL_TEXTS.SELECT_HOW_TO_COMMUNICATE)}</p>
          </div>
          <Dropdown
            colorStyle='default'
            size='small'
            theme='light'
            value={agentToneOfVoice}
            onChange={value => handleAgentPropChange('tone', value)}
          >
            {TONE_OF_VOICES.map(({ value, text, emoji }) => (
              <option
                key={value}
                value={value}
              >
                {`${emoji} ${t(text)}`}
              </option>
            ))}
          </Dropdown>
        </div>
        <hr className='jfpContent-wrapper--line line-2x' />
        {/* greeting message */}
        {layout === VISIBILITY_LAYOUT.MINIMAL.value && (
        <>
          <div className='jfpContent-wrapper--ai-persona-title'>
            <div className='jfpContent-wrapper--ai-persona-greeting'>
              <div>
                <h3>{t(ALL_TEXTS.GREETING_MESSAGE)}</h3>
                <p>{t(ALL_TEXTS.SHOW_A_MESSAGE_TO_GREET_USERS)}</p>
              </div>
              <Toggle checked={greetingBool} onChange={() => handleChangeGreeting(!greetingBool)} />
            </div>
            <Input
              maxLength={80}
              value={greetingMessageState}
              placeholder={t(ALL_TEXTS.HOW_CAN_I_HELP_YOU)}
              onChange={e => handleChangeGreetingText(e.target.value)}
              disabled={!greetingBool}
            />
          </div>
          <hr className='jfpContent-wrapper--line' />
        </>
        )}
        {/* agent chattiness */}
        <div className='jfpContent-wrapper--ai-persona-title'>
          <div>
            <h3>{t(ALL_TEXTS.CHATTINESS)}</h3>
            <p>{t(ALL_TEXTS.SPECIFY_THE_DESIRED_LEVEL_OF_DETAIL_IN_THE_AGENTS_RESPONSES)}</p>
          </div>
          <input
            className='chattiness-slider'
            type='range'
            min='1'
            max={CHATTINESS_LEVELS.length}
            value={agentChattiness}
            onChange={e => handleAgentPropChange('chattiness', e.target.value)}
            style={{ '--value': `${indexToPercentage(agentChattiness) || '0%'}` }}
          />
          <div className='chattiness-slider--labels'>
            {CHATTINESS_LEVELS.map(level => (
              <div key={level.title}>
                <span>{level.title}</span>
              </div>
            ))}
          </div>
        </div>
        <ChatGuidelines />
      </div>
    </>
  );
};

export default AiPersonaStep;
