/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */
import React, { useCallback, useEffect } from 'react';
import debounce from 'lodash/debounce';

import { saveInstallment, updateAgentProperty } from '../../api';
import {
  ALL_TEXTS,
  FONTS, THEME_CUSTOMIZATION_KEYS,
  THEME_MAP, THEMES
} from '../../constants';
import { useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import {
  getThemeColor,
  isValidHex, isValidRgba, t,
  toCamelCase
} from '../../utils';
import Dropdown from '../UI/Dropdown';
import InputColor from '../UI/InputColor';

const StyleStep = () => {
  const { state, asyncDispatch } = useWizard();

  const {
    step,
    themeName,
    previewAgentId,
    themeCustomizations,
    platformSettings: { PROVIDER_API_KEY }
  } = state;

  useEffect(() => {
    saveInstallment(`${toCamelCase(step)}Step`);
  }, []);

  const handleChangeTheme = async themeNm => {
    await asyncDispatch(
      () => updateAgentProperty(previewAgentId, THEME_MAP[themeNm], PROVIDER_API_KEY),
      ACTION_CREATORS.updateThemeRequest,
      ACTION_CREATORS.updateThemeSuccess,
      ACTION_CREATORS.updateThemeError,
      themeNm
    );
  };

  const handleChangeThemeProperty = async (prop, value) => {
    const data = { type: 'style', prop, value };
    await asyncDispatch(
      () => updateAgentProperty(previewAgentId, data, PROVIDER_API_KEY),
      ACTION_CREATORS.updateThemePropertyRequest,
      ACTION_CREATORS.updateThemePropertySuccess,
      ACTION_CREATORS.updateThemePropertyError
    );
  };

  const debouncedHandleChangeThemeProperty = useCallback(debounce(handleChangeThemeProperty, 300), [asyncDispatch, previewAgentId]);

  const validateColor = color => {
    if (isValidHex(color) || isValidRgba(color)) {
      return true;
    }
    return false;
  };

  const handleInputChange = prop => value => {
    const color = validateColor(value);
    if (color) {
      debouncedHandleChangeThemeProperty(prop, value);
    }
  };

  return (
    <>
      <div className='jfpContent-wrapper--style'>
        <h2 className='sr-only'>{t(ALL_TEXTS.AGENT_STYLE)}</h2>
        <div className='jfpContent-wrapper--style-color-select'>
          <h3>{t(ALL_TEXTS.COLOR_SCHEME)}</h3>
          <ul className='jfpContent-wrapper--style-colors'>
            {THEMES.map(theme => (
              <li
                key={theme.id}
                style={{ background: getThemeColor(THEME_MAP[theme.name], 'pageBackgroundStart') }}
                onClick={() => handleChangeTheme(theme.name)}
                className={themeName === theme.name ? 'isSelected' : ''}
              >
                <span className='chatBg' style={{ background: getThemeColor(THEME_MAP[theme.name], 'chatBackground') }}>
                  <span className='chatText' style={{ color: getThemeColor(THEME_MAP[theme.name], 'inputTextColor') }}>A</span>
                  <span className='agentBg' style={{ background: getThemeColor(THEME_MAP[theme.name], 'agentBackgroundStart') }} />
                </span>
              </li>
            ))}
          </ul>
        </div>
        <hr className='jfpContent-wrapper--line' />
        <div className='jfpContent-wrapper--style-color-select'>
          <h3>{t(ALL_TEXTS.AGENT_BACKGROUND_STYLE)}</h3>
          <div className='jfpContent-wrapper--style-color-select-col'>
            <h4>{t(ALL_TEXTS.START_COLOR)}</h4>
            <InputColor
              defaultValue={themeCustomizations[THEME_CUSTOMIZATION_KEYS.AGENT_BG_START_COLOR]}
              onChange={handleInputChange(THEME_CUSTOMIZATION_KEYS.AGENT_BG_START_COLOR)}
            />
          </div>
          <div className='jfpContent-wrapper--style-color-select-col'>
            <h4>{t(ALL_TEXTS.END_COLOR)}</h4>
            <InputColor
              defaultValue={themeCustomizations[THEME_CUSTOMIZATION_KEYS.AGENT_BG_END_COLOR]}
              onChange={handleInputChange(THEME_CUSTOMIZATION_KEYS.AGENT_BG_END_COLOR)}
            />
          </div>
        </div>
        <hr className='jfpContent-wrapper--line' />
        <div className='jfpContent-wrapper--style-color-select'>
          <h3>{t(ALL_TEXTS.CHAT_STYLE)}</h3>
          <div className='jfpContent-wrapper--style-color-select-full'>
            <h4>{t(ALL_TEXTS.CHAT_BACKGROUND_COLOR)}</h4>
            <InputColor
              defaultValue={themeCustomizations[THEME_CUSTOMIZATION_KEYS.CHAT_BG_COLOR]}
              onChange={handleInputChange(THEME_CUSTOMIZATION_KEYS.CHAT_BG_COLOR)}
            />
          </div>
          <div className='jfpContent-wrapper--style-color-select-col'>
            <h4>{t(ALL_TEXTS.FONT_FAMILY)}</h4>
            <Dropdown
              value={themeCustomizations.fontFamily}
              onChange={value => handleChangeThemeProperty(THEME_CUSTOMIZATION_KEYS.FONT_FAMILY, value)}
            >
              {FONTS.map(font => (
                <option
                  key={font.value}
                  value={font.value}
                >
                  {font.label}
                </option>
              ))}
            </Dropdown>
          </div>
          <div className='jfpContent-wrapper--style-color-select-col'>
            <h4>{t(ALL_TEXTS.CHAT_THEME_COLOR)}</h4>
            <InputColor
              defaultValue={themeCustomizations[THEME_CUSTOMIZATION_KEYS.FONT_COLOR]}
              onChange={handleInputChange(THEME_CUSTOMIZATION_KEYS.FONT_COLOR)}
            />
          </div>
        </div>
        <hr className='jfpContent-wrapper--line' />
        <div className='jfpContent-wrapper--style-color-select'>
          <h3>{t(ALL_TEXTS.BUTTON_STYLE)}</h3>
          <div className='jfpContent-wrapper--style-color-select-col'>
            <h4>{t(ALL_TEXTS.BUTTON_COLOR)}</h4>
            <InputColor
              defaultValue={themeCustomizations[THEME_CUSTOMIZATION_KEYS.BUTTON_BG_COLOR]}
              onChange={handleInputChange(THEME_CUSTOMIZATION_KEYS.BUTTON_BG_COLOR)}
            />
          </div>
          <div className='jfpContent-wrapper--style-color-select-col'>
            <h4>{t(ALL_TEXTS.ICON_COLOR)}</h4>
            <InputColor
              defaultValue={themeCustomizations[THEME_CUSTOMIZATION_KEYS.BUTTON_ICON_BG_COLOR]}
              onChange={handleInputChange(THEME_CUSTOMIZATION_KEYS.BUTTON_ICON_BG_COLOR)}
            />
          </div>
        </div>
      </div>
    </>
  );
};

export default StyleStep;
