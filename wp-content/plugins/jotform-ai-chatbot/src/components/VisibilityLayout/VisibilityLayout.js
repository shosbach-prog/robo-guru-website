import React, { useEffect, useState } from 'react';

import { saveInstallment, updateAgentProperty } from '../../api';
import {
  ALL_TEXTS, AUTO_OPEN_CHAT_VALUES, CUSTOMIZATION_KEYS, OPEN_BY_DEFAULT_OPTIONS, POSITION, VERBAL_TOGGLE, VISIBILITY_LAYOUT
} from '../../constants';
import { useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { t, toCamelCase } from '../../utils';
import Dropdown from '../UI/Dropdown';
import Radio from '../UI/Radio';
import Toggle from '../UI/Toggle';

const VisibilityLayout = () => {
  const { state, dispatch, asyncDispatch } = useWizard();

  const {
    step,
    previewAgentId,
    customizations,
    platformSettings: {
      PROVIDER_API_KEY
    }
  } = state;

  const {
    pulse, position, autoOpenChatIn, layout
  } = customizations;

  const [selectedLayout, setSelectedLayout] = useState(layout);

  const pulseBool = pulse === VERBAL_TOGGLE.YES;

  useEffect(() => {
    saveInstallment(`${toCamelCase(step)}Step`);
  }, []);

  const updateCustomization = async ({ key, value }) => {
    let updatedCustomizations = { ...customizations, [key]: value };
    if (key === CUSTOMIZATION_KEYS.LAYOUT && value === VISIBILITY_LAYOUT.EXTENDED.value) {
      updatedCustomizations = { ...updatedCustomizations, [CUSTOMIZATION_KEYS.AUTO_OPEN_CHAT]: AUTO_OPEN_CHAT_VALUES.NEVER };
      dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.AUTO_OPEN_CHAT, AUTO_OPEN_CHAT_VALUES.NEVER));
    }
    await asyncDispatch(
      () => updateAgentProperty(previewAgentId, { prop: 'popover', type: 'embed', value: JSON.stringify(updatedCustomizations) }, PROVIDER_API_KEY),
      ACTION_CREATORS.updateAgentPropertyRequest,
      ACTION_CREATORS.updateAgentPropertySuccess,
      ACTION_CREATORS.updateAgentPropertyError
    );
  };

  const handleChangeLayout = newLayout => {
    if (newLayout === selectedLayout) return;
    setSelectedLayout(newLayout);
    dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.LAYOUT, newLayout));
    updateCustomization({ key: CUSTOMIZATION_KEYS.LAYOUT, value: newLayout });
    saveInstallment(`layout_${newLayout}`);
  };

  const handleChangePulsing = value => {
    const verbalVal = value ? VERBAL_TOGGLE.YES : VERBAL_TOGGLE.NO;
    dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.PULSE, verbalVal));
    updateCustomization({ key: CUSTOMIZATION_KEYS.PULSE, value: verbalVal });
  };

  const handleChangePosition = value => {
    dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.POSITION, value));
    updateCustomization({ key: CUSTOMIZATION_KEYS.POSITION, value });
  };

  const handleOpenByDefaultChange = value => {
    dispatch(ACTION_CREATORS.updateCustomization(CUSTOMIZATION_KEYS.AUTO_OPEN_CHAT, value));
    updateCustomization({ key: CUSTOMIZATION_KEYS.AUTO_OPEN_CHAT, value });
  };

  return (
    <>
      <div className='jfpContent-wrapper--customization-layout'>
        {/* visible on */}
        <div className='customize-option layout'>
          <div className='customize-option layout-new'>
            <div className='jfpContent-wrapper--customization-title'>
              <div>
                <h3>{t(ALL_TEXTS.LAYOUT)}</h3>
                <p>{t(ALL_TEXTS.CHOOSE_HOW_CHATBOT_APPEARS)}</p>
              </div>
            </div>
            <Dropdown
              colorStyle='default'
              size='small'
              theme='light'
              value={selectedLayout}
              onChange={handleChangeLayout}
            >
              {Object.values(VISIBILITY_LAYOUT).map(({ value, text }) => (
                <option
                  key={value}
                  value={value}
                >
                  {t(text)}
                </option>
              ))}
            </Dropdown>
          </div>
        </div>
        {/* position */}
        <div className='customize-option position'>
          <div className='jfpContent-wrapper--customization-title'>
            <div>
              <h3>{t(ALL_TEXTS.POSITION)}</h3>
              <p>{t(ALL_TEXTS.CHOOSE_THE_AI_AGENT)}</p>
            </div>
          </div>
          <ul className='jfpContent-wrapper--customization-position'>
            <li>
              <Radio
                label={t(ALL_TEXTS.LEFT)}
                onChange={() => handleChangePosition(POSITION.LEFT)}
                size='small'
                value={POSITION.LEFT}
                name='position'
                checked={position === POSITION.LEFT}
              />
            </li>
            <li>
              <Radio
                description={`(${t(ALL_TEXTS.RIGHT)})`}
                label={t(ALL_TEXTS.RIGHT)}
                onChange={() => handleChangePosition(POSITION.RIGHT)}
                size='small'
                value={POSITION.RIGHT}
                name='position'
                checked={position === POSITION.RIGHT}
              />
            </li>
          </ul>
        </div>
        {selectedLayout === VISIBILITY_LAYOUT.MINIMAL.value && (
          <>
            {/* pulse */}
            <div className='customize-option pulse'>
              <div className='jfpContent-wrapper--customization-title'>
                <div>
                  <h3>{t(ALL_TEXTS.PULSING)}</h3>
                  <p>{t(ALL_TEXTS.ADD_A_PULSE_EFFECT)}</p>
                </div>
                <Toggle checked={pulseBool} onChange={() => handleChangePulsing(!pulseBool)} />
              </div>
            </div>
            {/* open by default */}
            <div className='customize-option open'>
              <div className='jfpContent-wrapper--customization-title'>
                <div>
                  <h3>{t(ALL_TEXTS.OPEN_BY_DEFAULT)}</h3>
                  <p>{t(ALL_TEXTS.CHOOSE_WHEN_CHATBOT_WILL_APPEAR)}</p>
                </div>
              </div>
              <Dropdown
                colorStyle='default'
                size='small'
                theme='light'
                value={autoOpenChatIn}
                onChange={value => handleOpenByDefaultChange(value)}
              >
                {OPEN_BY_DEFAULT_OPTIONS.map(({ value, text }) => (
                  <option
                    key={value}
                    value={value}
                  >
                    {t(text)}
                  </option>
                ))}
              </Dropdown>
            </div>
          </>
        )}
      </div>
    </>
  );
};

export default VisibilityLayout;
