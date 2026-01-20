import React, { useEffect } from 'react';
import debounce from 'lodash/debounce';
import { func, string } from 'prop-types';

import { t } from '../../utils';
import Dropdown from '../UI/Dropdown';
import { IconMagnifyingGlass } from '../UI/Icon';
import Input from '../UI/Input';

const MaterialSearch = ({
  materialTypeFilter,
  materialStatusFilter,
  setMaterialTypeFilter,
  setMaterialStatusFilter,
  setSearchText
}) => {
  const typingDebounce = debounce((e) => { setSearchText(e.target.value.trim()); }, 250);

  useEffect(() => () => typingDebounce.cancel(), []);

  return (
    <div className='knowledge-filter-container'>
      <Input
        placeholder={t('Search')}
        className='knowledge-filter-search'
        onChange={typingDebounce}
        prefix={{
          as: 'span',
          icon: <IconMagnifyingGlass />
        }}
      />
      <div className='knowledge-filter-dropdown grow-0 shrink-1'>
        <Dropdown
          placeholder=''
          value={materialStatusFilter ? `${materialTypeFilter}_${materialStatusFilter}` : materialTypeFilter}
          onChange={value => {
            if (value === 'QA_ACTION_REQUIRED') {
              setMaterialTypeFilter('QA');
              setMaterialStatusFilter('ACTION_REQUIRED');
            } else {
              setMaterialTypeFilter(value);
              setMaterialStatusFilter('');
            }
          }}
        >
          {[
            {
              text: 'See All',
              value: 'ALL'
              // icon: () => <Icon width='20' height='20' name='eye_filled' />
            },
            {
              text: 'Knowledge',
              value: 'TEXT'
              // icon: () => <Icon width='20' height='20' name='annotation_info' />
            },
            {
              text: 'Document',
              value: 'DOCUMENT'
              // icon: () => <Icon width='20' height='20' name='arrow_up_from_bracket' />
            },
            {
              text: 'URL',
              value: 'URL'
              // icon: () => <Icon width='20' height='20' name='link_diagonal' />
            },
            {
              text: 'Q&A',
              value: 'QA'
              // icon: () => <Icon width='20' height='20' name='message_question_filled' />
            },
            {
              text: 'Q&A - Unanswered',
              value: 'QA_ACTION_REQUIRED'
              // icon: () => <Icon width='20' height='20' name='message_question_filled' />
            }
          ].map(({ value, text }) => (
            <option
              key={value}
              value={value}
            >
              {`${t(text)}`}
            </option>
          ))}
        </Dropdown>
      </div>
    </div>
  );
};

MaterialSearch.propTypes = {
  materialTypeFilter: string,
  materialStatusFilter: string,
  setMaterialTypeFilter: func,
  setMaterialStatusFilter: func,
  setSearchText: func
};

export default MaterialSearch;
