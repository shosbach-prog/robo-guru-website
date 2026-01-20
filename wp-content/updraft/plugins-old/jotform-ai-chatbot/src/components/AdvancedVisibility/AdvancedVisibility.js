import React, { useEffect, useMemo, useState } from 'react';

import { interactWithPlatform, saveInstallment } from '../../api';
import {
  ALL_TEXTS,
  POSITION, SELECTION_OPTIONS, SELECTION_TYPE_LABELS, SELECTION_TYPE_VALUES,
  URL_MATCH_OPTIONS, URL_MATCH_TYPE_LABELS, URL_MATCH_TYPE_VALUES, VISIBILITY_TOGGLE
} from '../../constants';
import { useEffectIgnoreFirst, useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import {
  generateTempId, isNumericString, t, toCamelCase
} from '../../utils';
import Button from '../UI/Button';
import Dropdown from '../UI/Dropdown';
import { IconPlus, IconTrashFilled } from '../UI/Icon';
import Input from '../UI/Input';
import Radio from '../UI/Radio';
import Chip from './Chip';
import InfoBox from './InfoBox';

const AdvancedVisibility = () => {
  const { state, dispatch, asyncDispatch } = useWizard();
  const {
    step, platformSettings: { PLATFORM_PAGES }, selectedPages, isPublished
  } = state;

  const [inputValue, setInputValue] = useState('');
  const [selectedItems, setSelectedItems] = useState(selectedPages);
  const [urlMatchType, setUrlMatchType] = useState(URL_MATCH_TYPE_VALUES.IS);
  const [selectionType, setSelectionType] = useState(SELECTION_TYPE_VALUES.URL);
  const [isAddDisabled, setIsAddDisabled] = useState(false);

  const activeMode = useMemo(() => selectedItems.active, [selectedItems]);

  const isDuplicate = useMemo(() => {
    const currentList = selectedItems[activeMode] || [];
    return currentList.some(item => item.value === inputValue && item.type === selectionType && (selectionType === SELECTION_TYPE_VALUES.URL ? item.match === urlMatchType : true));
  }, [selectedItems, activeMode, selectionType, urlMatchType, inputValue]);

  useEffect(() => {
    if (!inputValue || (selectionType === SELECTION_TYPE_VALUES.URL && isNumericString(inputValue)) || isDuplicate) {
      setIsAddDisabled(true);
    } else {
      setIsAddDisabled(false);
    }
  }, [inputValue, selectionType, urlMatchType, selectedItems]);

  useEffectIgnoreFirst(() => {
    const handleSaveWpPageChanges = async () => {
      saveInstallment(`saveWpPageChangesButton_${toCamelCase(step)}Step`);
      const pagesData = { action: 'update', key: 'pages', value: JSON.stringify(selectedItems) };
      await asyncDispatch(
        () => interactWithPlatform(pagesData),
        ACTION_CREATORS.savePlatformAgentPagesRequest,
        ACTION_CREATORS.savePlatformAgentPagesSuccess,
        ACTION_CREATORS.savePlatformAgentPagesError
      );
    };
    handleSaveWpPageChanges();
    dispatch(ACTION_CREATORS.setSelectedPages(selectedItems));
  }, [selectedItems]);

  const handleRadioChange = (value) => {
    setSelectedItems(prev => ({ ...prev, active: value }));
    saveInstallment(`advancedVisibility_${value}`);
  };

  const handleAdd = () => {
    if (!inputValue || isDuplicate) return;
    setSelectedItems(prev => ({
      ...prev,
      [activeMode]: [
        ...prev[activeMode],
        {
          id: generateTempId(),
          type: selectionType,
          match: urlMatchType,
          value: inputValue
        }
      ]
    }));
    setInputValue('');
    saveInstallment('advancedVisibilityAddButton');
  };

  const handleDelete = (index) => {
    setSelectedItems(prev => ({ ...prev, [activeMode]: prev[activeMode].filter((_, i) => i !== index) }));
    saveInstallment('advancedVisibilityDeleteButton');
  };

  return (
    <div className='customize-option visibility'>
      <div className='jfpContent-wrapper--customization-title'>
        <div>
          <h3>{t(ALL_TEXTS.ADVANCED_VISIBILITY)}</h3>
          <p>{t(ALL_TEXTS.CHOOSE_WHERE_CHATBOT_WILL_BE_SHOWN_OR_HIDDEN)}</p>
        </div>
        <ul className='jfpContent-wrapper--visibility-selection'>
          <li>
            <Radio
              size='small'
              name='visibility'
              value={POSITION.LEFT}
              label={t(VISIBILITY_TOGGLE.SHOW_ON.label)}
              checked={activeMode === VISIBILITY_TOGGLE.SHOW_ON.value}
              onChange={() => handleRadioChange(VISIBILITY_TOGGLE.SHOW_ON.value)}
            />
          </li>
          <li>
            <Radio
              size='small'
              name='visibility'
              value={POSITION.RIGHT}
              label={t(VISIBILITY_TOGGLE.HIDE_ON.label)}
              checked={activeMode === VISIBILITY_TOGGLE.HIDE_ON.value}
              onChange={() => handleRadioChange(VISIBILITY_TOGGLE.HIDE_ON.value)}
            />
          </li>
        </ul>
      </div>
      <div className='visibility-filter'>
        <div className='visibility-wrapper'>
          <div className='visibility-input'>
            <div className='visibility-domain'>
              {/* type dropdown */}
              <Dropdown
                value={selectionType}
                onChange={value => {
                  setSelectionType(value);
                  setInputValue('');
                }}
              >
                {SELECTION_OPTIONS.map(option => (
                  <option key={option.value} value={option.value}>{option.label}</option>
                ))}
              </Dropdown>
            </div>

            {selectionType === SELECTION_TYPE_VALUES.URL ? (
              <div className='visibility-selector'>
                {/* match type dropdown */}
                <Dropdown
                  colorStyle='default'
                  size='small'
                  theme='light'
                  value={urlMatchType}
                  onChange={value => setUrlMatchType(value)}
                >
                  {URL_MATCH_OPTIONS.map(option => (
                    <option key={option.value} value={option.value}>{option.label}</option>
                  ))}
                </Dropdown>
                <Input
                  type='text'
                  placeholder={t(ALL_TEXTS.URL)}
                  value={inputValue}
                  onChange={e => setInputValue(e.target.value)}
                  style={{ flex: 1 }}
                  className='visibility-input'
                />
              </div>
            ) : (
              // page dropdown
              <Dropdown
                colorStyle='default'
                size='small'
                theme='light'
                value={inputValue}
                onChange={value => setInputValue(value)}
                style={{ flex: 1 }}
              >
                <option value=''>Select Page</option>
                {PLATFORM_PAGES.map(page => (
                  <option key={page.value} value={page.value}>{page.text}</option>
                ))}
              </Dropdown>
            )}
          </div>
          <Button
            startIcon={<IconPlus />}
            disabled={isAddDisabled}
            onClick={handleAdd}
            aria-label='Add Option to the visibility list'
          >
            {`${t(ALL_TEXTS.ADD)}`}
          </Button>
        </div>
        {/* selected pages */}
        <ul className='condition-wrapper'>
          {((selectedItems.showOn?.length === 0 && activeMode === VISIBILITY_TOGGLE.SHOW_ON.value)
            || (selectedItems.hideOn?.length === 0 && activeMode === VISIBILITY_TOGGLE.HIDE_ON.value))
            && <InfoBox isPublished={isPublished} />}
          {selectedItems[activeMode]?.map((item, index) => (
            <li
              key={item.id}
              className='condition-chips'
            >
              {item.type === SELECTION_TYPE_VALUES.URL ? (
                <>
                  <Chip>{t(SELECTION_TYPE_LABELS.URL)}</Chip>{' '}
                  {item.match === URL_MATCH_TYPE_VALUES.IS ? (
                    <Chip>{t(URL_MATCH_TYPE_LABELS.IS)}</Chip>
                  ) : (
                    <Chip>{t(URL_MATCH_TYPE_LABELS.STARTS_WITH)}</Chip>
                  )}{' '}
                  <span className='value'>{item.value}</span>
                </>
              ) : (
                <>
                  <Chip>{t(SELECTION_TYPE_LABELS.PAGE)}</Chip>{' '}
                  <span className='value'>
                    {PLATFORM_PAGES.find(p => p.value === item.value)?.text || item.text}
                  </span>
                </>
              )}

              <Button className='delete-btn' startIcon={<IconTrashFilled />} onClick={() => handleDelete(index)} />
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default AdvancedVisibility;
