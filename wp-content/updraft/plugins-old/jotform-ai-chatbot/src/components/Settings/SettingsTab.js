import React from 'react';
import cx from 'classnames';

import { ALL_TEXTS } from '../../constants';
import { SETTINGS_TABS } from '../../constants/wizard';
import { useWizard } from '../../hooks';
import { ACTION_CREATORS } from '../../store';
import { t } from '../../utils';
import Button from '../UI/Button';

const SettingsTab = () => {
  const { dispatch, state } = useWizard();

  const { activeSettingsTab } = state;

  const { GENERAL, WOOCOMMERCE } = SETTINGS_TABS;

  const handleTabClick = (tab) => {
    dispatch(ACTION_CREATORS.setActiveSettingsTab(tab));
  };

  return (
    <div className='jfpContent-wrapper--settings-panel'>
      <div className='jfpContent-wrapper--settings-panel-title'>
        <h2 className='jfpContent-wrapper--settings-panel-title-content' aria-hidden='true'>{t(ALL_TEXTS.SETTINGS)}</h2>
      </div>
      <ul role='tablist' aria-label='Plugin Settings'>
        <li className='jfpContent-wrapper--settings-panel-btn'>
          <Button
            colorStyle='secondary'
            variant='ghost'
            fullWidth
            role='tab'
            className={cx({ isActive: activeSettingsTab === GENERAL })}
            onClick={() => handleTabClick(GENERAL)}
          >
            {t(ALL_TEXTS.GENERAL)}
          </Button>
        </li>
        {/* <li className='jfpContent-wrapper--settings-panel-btn'>
          <Button
            colorStyle='secondary'
            variant='ghost'
            fullWidth
            role='tab'
            className={cx({ isActive: activeSettingsTab === AGENT_SKILLS })}
            onClick={() => handleTabClick(AGENT_SKILLS)}
          >
            {t(ALL_TEXTS.AGENT_SKILLS)}
          </Button>
        </li> */}
        <li className='jfpContent-wrapper--settings-panel-btn'>
          <Button
            colorStyle='secondary'
            variant='ghost'
            fullWidth
            role='tab'
            className={cx({ isActive: activeSettingsTab === WOOCOMMERCE })}
            onClick={() => handleTabClick(WOOCOMMERCE)}
          >
            {t(ALL_TEXTS.WOO_COMMERCE)}
            {' '}
            <span className='new-badge'>New</span>
          </Button>
        </li>
        {/* <li className='jfpContent-wrapper--settings-panel-btn'>
          <Button
            colorStyle='secondary'
            variant='ghost'
            fullWidth
            role='tab'
            className={cx({ isActive: activeSettingsTab === UPGRADE_PLAN })}
            onClick={() => handleTabClick(UPGRADE_PLAN)}
          >
            {t(ALL_TEXTS.UPGRADE_PLAN)}
          </Button>
        </li> */}
      </ul>
    </div>

  );
};

export default SettingsTab;
