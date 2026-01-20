import React, { useEffect, useState } from 'react';
import cx from 'classnames';

import { TAB_STEPS } from '../constants';
import { useWizard } from '../hooks';
import { ACTION_CREATORS } from '../store';
import { setStepAsQueryParam, t } from '../utils';

const WizardTabs = () => {
  const { state, dispatch } = useWizard();
  const { step } = state;

  const [activeIndex, setActiveIndex] = useState(0);

  useEffect(() => {
    const index = TAB_STEPS.findIndex(({ name }) => name === step);
    if (index !== -1) setActiveIndex(index);
  }, [step]);

  const handleTabClick = async (tabStep) => {
    dispatch(ACTION_CREATORS.setStep(tabStep));
    setStepAsQueryParam(tabStep);
  };

  const isAnyTabActive = TAB_STEPS.some(({ name }) => name === step);

  const toggleStyle = {
    transform: `translateX(${activeIndex * 100}%) translateY(-50%)`,
    width: 'calc(25% - 2px)'
  };

  return (
    <div className={cx('jfpContent-wrapper--main-tabs')}>
      <div className={cx('jfpContent-wrapper--main-tabs-container')} role='tablist'>
        <div
          className={cx('jfpContent-wrapper--main-tabs-toggle', {
            invisible: !isAnyTabActive
          })}
          style={toggleStyle}
          aria-hidden='true'
        />
        {TAB_STEPS.map(({ label, name }) => (
          <button
            type='button'
            key={name}
            onClick={() => handleTabClick(name)}
            className={cx('jfpContent-wrapper--main-tabs-button', {
              isActive: step === name
            })}
            role='tab'
            aria-selected={step === name}
          >
            {t(label)}
          </button>
        ))}
      </div>
    </div>
  );
};

export default WizardTabs;
