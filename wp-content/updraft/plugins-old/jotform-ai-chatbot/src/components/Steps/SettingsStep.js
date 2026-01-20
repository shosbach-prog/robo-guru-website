import React from 'react';

import { useWizard } from '../../hooks';
import {
  AgentSkills, General, SettingsTab, UpgradePlan, Woocommerce
} from '../Settings';

const SettingsStep = () => {
  const { state } = useWizard();

  const { activeSettingsTab } = state;

  const SETTINGS_TABS = {
    GENERAL: General,
    AGENT_SKILLS: AgentSkills,
    WOOCOMMERCE: Woocommerce,
    UPGRADE_PLAN: UpgradePlan
  };

  const ActiveTabComponent = SETTINGS_TABS[activeSettingsTab] || General;

  return (
    <div className='jfpContent-wrapper--settings'>
      <SettingsTab />
      <div className='jfpContent-wrapper--settings-options'>
        <ActiveTabComponent />
      </div>
    </div>
  );
};

export default SettingsStep;
