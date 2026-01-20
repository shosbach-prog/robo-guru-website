import React from 'react';

import '../styles/index.scss';

import { WizardProvider } from '../context';
import Wizard from './Wizard';

export const ChatbotGenerator = props => (
  <WizardProvider {...props}>
    <Wizard {...props} />
  </WizardProvider>
);
