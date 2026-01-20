import React from 'react';
import classnames from 'classnames';
import { node, string } from 'prop-types';

import { t } from '../../utils';

const LabelWrapperItem = ({
  children,
  heading,
  desc,
  customClass
}) => (
  <div className={classnames(
    'jfMaterialEditor--label-wrapper',
    customClass
  )}
  >
    <div>
      <h3>{t(heading)}</h3>
      <p>{t(desc)}</p>
    </div>
    {children}
  </div>
);

LabelWrapperItem.propTypes = {
  children: node.isRequired,
  heading: string.isRequired,
  desc: string.isRequired,
  customClass: string
};

export default LabelWrapperItem;
