import React from 'react';
import classNames from 'classnames';
import {
  bool, func, node, object, oneOf, string
} from 'prop-types';

import '../../styles/button.scss';

const Button = ({
  startIcon,
  endIcon,
  size = 'medium',
  children = null,
  variant = 'default',
  colorStyle = 'primary',
  onClick = f => f,
  loader = false,
  rounded = false,
  className = '',
  buttonRef = null,
  fullWidth = false,
  href = '',
  role,
  disabled = false,
  ...props
}) => {
  const buttonClass = classNames(
    'jfButton',
    `jfButton--${variant}`,
    `jfButton--${colorStyle}`,
    `jfButton--${size}`,
    {
      'jfButton--loading': loader,
      'jfButton--rounded': rounded,
      'jfButton--full-width': fullWidth
    },
    className
  );

  const content = (
    <>
      {loader && <span className='jfButton--spin' />}
      {startIcon && <span className='jfButton--icon' aria-hidden='true'>{startIcon}</span>}
      {children && <span className='jfButton--text'>{children}</span>}
      {endIcon && <span className='jfButton--icon' aria-hidden='true'>{endIcon}</span>}
    </>
  );

  if (href) {
    return (
      <a
        {...props}
        href={href}
        className={buttonClass}
        onClick={onClick}
        ref={buttonRef}
        role='button'
        disabled={loader || disabled}
      >
        {content}
      </a>
    );
  }

  return (
    <button
      {...props}
      type='button'
      className={buttonClass}
      onClick={onClick}
      disabled={loader || disabled}
      ref={buttonRef}
      role={role}
    >
      {content}
    </button>
  );
};

Button.propTypes = {
  children: node,
  variant: oneOf(['default', 'ghost', 'filled', 'outline']),
  colorStyle: oneOf(['error', 'primary', 'success', 'teams', 'pdf', 'apps', 'reports', 'forms', 'sign', 'tables', 'inbox', 'approvals', 'analytics', 'pages', 'secondary', 'neutral']),
  onClick: func,
  startIcon: node,
  endIcon: node,
  loader: bool,
  size: oneOf(['small', 'medium', 'large']),
  rounded: bool,
  className: string,
  buttonRef: object,
  fullWidth: bool,
  href: string,
  role: string,
  disabled: bool
};

export default Button;
