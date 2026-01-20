import { useCallback, useState } from 'react';

import { ALL_TEXTS } from '../constants';
import { t } from '../utils';

export const STAGES = {
  UNPUBLISHED: 'UNPUBLISHED',
  LOADING1: 'LOADING1',
  LOADING2: 'LOADING2',
  PUBLISHED: 'PUBLISHED'
};

const stageConfigs = {
  [STAGES.UNPUBLISHED]: {
    text: t(ALL_TEXTS.PUBLISH),
    colorStyle: 'primary',
    variant: 'default',
    disabled: false,
    opacity: 1
  },
  [STAGES.LOADING1]: {
    text: t(ALL_TEXTS.PUBLISHING),
    colorStyle: 'primary',
    variant: 'default',
    disabled: true,
    opacity: 1
  },
  [STAGES.LOADING2]: {
    text: t(ALL_TEXTS.PUBLISHED),
    colorStyle: 'primary',
    variant: 'default',
    disabled: true,
    opacity: 0.5
  },
  [STAGES.PUBLISHED]: {
    text: t(ALL_TEXTS.UNPUBLISH),
    colorStyle: 'error',
    variant: 'outline',
    disabled: false,
    opacity: 1
  }
};

export const usePublishButton = (initialStage = STAGES.UNPUBLISHED) => {
  const [stage, setStage] = useState(STAGES[initialStage]);

  const startPublish = useCallback(() => {
    setStage(STAGES.LOADING1);

    // First loading stage
    setTimeout(() => {
      setStage(STAGES.LOADING2);

      // Second loading stage
      setTimeout(() => {
        setStage(STAGES.PUBLISHED);
      }, 2000);
    }, 1000);
  }, []);

  const resetToUnpublished = useCallback(() => {
    setStage(STAGES.UNPUBLISHED);
  }, []);

  return {
    stage,
    buttonProps: stageConfigs[stage],
    startPublish,
    resetToUnpublished
  };
};
