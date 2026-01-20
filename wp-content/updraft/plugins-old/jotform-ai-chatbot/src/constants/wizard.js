/* eslint-disable max-len */
/**
 * Wizard and step-related constants
 */

import { ALL_TEXTS } from './texts.js';

export const STEPS = {
  INITIAL: 'INITIAL',
  USECASE_SELECTION: 'USECASE_SELECTION',
  AI_PERSONA: 'AI_PERSONA',
  STYLE: 'STYLE',
  VISIBILITY: 'VISIBILITY',
  KNOWLEDGE: 'KNOWLEDGE',
  CONVERSATIONS: 'CONVERSATIONS',
  SETTINGS: 'SETTINGS'
};

export const SETTINGS_TABS = {
  GENERAL: 'GENERAL',
  AGENT_SKILLS: 'AGENT_SKILLS',
  WOOCOMMERCE: 'WOOCOMMERCE',
  UPGRADE_PLAN: 'UPGRADE_PLAN'
};

export const STEP_TO_BUILDER_PATH = {
  [STEPS.AI_PERSONA]: '/train/persona',
  [STEPS.STYLE]: '',
  [STEPS.VISIBILITY]: '/publish/chatbot',
  [STEPS.KNOWLEDGE]: '/train',
  [STEPS.SETTINGS]: ''
};

export const TAB_STEPS = [
  {
    label: ALL_TEXTS.AI_PERSONA,
    name: STEPS.AI_PERSONA
  },
  {
    label: ALL_TEXTS.AGENT_STYLE,
    name: STEPS.STYLE
  },
  {
    label: ALL_TEXTS.VISIBILITY,
    name: STEPS.VISIBILITY
  },
  {
    label: ALL_TEXTS.KNOWLEDGE_BASE,
    name: STEPS.KNOWLEDGE
  }
];

export const PROMPTS = [{
  id: 1,
  buttonText: 'Registration',
  text: 'Create a course registration agent suitable for any school or institution. The agent should be capable of collecting information for registration processes while being adaptable to various course structures, schedules, and user demographics (students, teachers, administrators).'
}, {
  id: 2,
  buttonText: 'Job Application',
  text: 'Develop a basic job application agent that serves as a one-page solution for collecting essential information from applicants. This agent should encompass personal details, educational background, and reference information. You can use your imagination to generate more fields related to the topic.'
}, {
  id: 3,
  buttonText: 'Feedback',
  text: 'Create a client feedback agent to gather valuable insights from my clients. The agent should be adaptable to all stages of feedback collection, from engaging with clients in a user-friendly way to gathering data.'
}, {
  id: 4,
  buttonText: 'Appointment',
  text: 'Develop an appointment request agent tailored for medical practices. This agent should collect information needed to schedule health appointments, such as a patient\'s name, address, and contact details.'
}];
