import React, { forwardRef } from 'react';
import { Dialog as HeadlessModal } from '@headlessui/react';
import classnames from 'classnames';
import PropTypes from 'prop-types';

import '../../styles/modal.scss';

import Button from './Button';
import { IconXmark } from './Icon';

export { Portal } from '@headlessui/react';

const Modal = forwardRef(({
  open,
  onClose,
  initialFocus,
  zIndex,
  children,
  ariaLabel,
  size,
  noFade,
  as: InnerComponent,
  fitOnMobile,
  className,
  ...rest
}, ref) => {
  const containerClassName = classnames(
    'jfModal',
    {
      'fit-on-mobile': fitOnMobile,
      'no-fade': noFade
    },
    className
  );

  const modalClassName = classnames(
    'jfModal--container',
    {
      'jfModal--container-small': size === 'small',
      'jfModal--container-medium': size === 'medium',
      'jfModal--container-large': size === 'large'
    }
  );

  return (
    <HeadlessModal
      onClose={onClose}
      open={open}
      initialFocus={initialFocus}
      style={{ zIndex }}
      className={containerClassName}
      aria-modal
      ref={ref}
      data-magnet-modal
    >
      <InnerComponent
        aria-label={ariaLabel}
        className={modalClassName}
        {...rest}
      >
        <div className='jfModal--close'>
          <Button
            onClick={onClose}
            startIcon={<IconXmark />}
            colorStyle='secondary'
            rounded
            aria-label='Close Button'
          />
        </div>
        {children}
      </InnerComponent>
    </HeadlessModal>
  );
});

Modal.propTypes = {
  open: PropTypes.bool,
  onClose: PropTypes.func.isRequired,
  initialFocus: PropTypes.oneOfType([
    PropTypes.shape({
      current: PropTypes.instanceOf(Element)
    }),
    PropTypes.func
  ]),
  zIndex: PropTypes.number,
  children: PropTypes.node.isRequired,
  ariaLabel: PropTypes.string,
  size: PropTypes.oneOf(['small', 'medium', 'large']),
  noFade: PropTypes.bool,
  as: PropTypes.elementType,
  fitOnMobile: PropTypes.bool,
  className: PropTypes.string
};

Modal.defaultProps = {
  open: false,
  zIndex: 1000,
  size: 'medium',
  noFade: false,
  as: 'div',
  fitOnMobile: false,
  className: '',
  initialFocus: null,
  ariaLabel: null
};

export default Modal;
