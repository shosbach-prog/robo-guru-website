import React, { useEffect } from 'react';
import cx from 'classnames';
import { isEmpty } from 'lodash';

import '../../styles/limit-warning.scss';

import { saveInstallment } from '../../api';
import AlertSvg from '../../assets/svg/alert-badge.svg';
import WarningSvg from '../../assets/svg/warning-badge.svg';
import { ALL_TEXTS } from '../../constants';
import { useWizard } from '../../hooks';
import { t, translationRenderer } from '../../utils';
import Button from '../UI/Button';

const AI_WORDPRESS_AGENT_LIMITS_MAPPING = {
  aiConversations: 'AI conversations',
  aiKnowledgeBase: 'AI knowledge base',
  aiSessions: 'AI agent sessions',
  aiPhoneCall: 'AI voice call'
};

const getActiveLimitWarning = (limitWarnings) => {
  const limitWarningKey = Object.keys(limitWarnings)
    .filter((limitKey) => Object.keys(AI_WORDPRESS_AGENT_LIMITS_MAPPING).includes(limitKey) && limitWarnings[limitKey].display !== 'hide')
    .sort((a, b) => Number(limitWarnings[b]?.data?.percent ?? 0) - Number(limitWarnings[a]?.data?.percent ?? 0))?.[0];
  if (limitWarningKey) {
    return limitWarnings[limitWarningKey];
  }
  return {};
};

const LimitWarning = () => {
  const {
    state: {
      limitWarnings,
      platformSettings: {
        PROVIDER_URL
      }
    }
  } = useWizard();
  const limitWarning = getActiveLimitWarning(limitWarnings);
  const limitKey = limitWarning.reason;
  const isOverLimit = limitWarning.color === 'red';

  const handleUpgradeNowClick = () => {
    const utms = new URLSearchParams({
      utm_content: 'wordpress-agent-plugin',
      utm_medium: isOverLimit ? 'overlimit-banner' : 'warning-banner',
      utm_campaign: limitKey,
      utm_term: 'upgrade-now-text'
    });
    window.open(`${PROVIDER_URL}/ai/wordpress-agent/pricing?${utms.toString()}`, '_blank');
  };

  useEffect(() => {
    if (limitKey) {
      saveInstallment(`seeLimitWarning_${limitKey}_${isOverLimit ? 'overlimit' : 'almostFull'}`);
    }
  }, [limitKey, isOverLimit]);

  if (isEmpty(limitWarning)) {
    return <></>;
  }

  return (
    <div className={cx('jf-limit-warning-banner', {
      isOverLimit
    })}
    >
      <div className='jf-limit-warning-content'>
        <div className='jf-limit-warning-content-icon'>
          {isOverLimit ? <AlertSvg /> : <WarningSvg />}
        </div>
        <p>{translationRenderer(isOverLimit
          ? ALL_TEXTS.OVERLIMIT_LIMIT_WARNING_TEXT
          : ALL_TEXTS.ALMOST_FULL_LIMIT_WARNING_TEXT)({
          renderer1: () => <strong>{t(AI_WORDPRESS_AGENT_LIMITS_MAPPING[limitKey])}</strong>,
          renderer2: (txt) => <strong>{txt}</strong>
        })}
        </p>
      </div>
      <Button colorStyle={isOverLimit ? 'error' : 'primary'} onClick={handleUpgradeNowClick}>{t(ALL_TEXTS.UPGRADE_NOW)} </Button>
    </div>
  );
};

export default LimitWarning;
