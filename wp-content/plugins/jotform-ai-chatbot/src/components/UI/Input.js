import React, {
  forwardRef, useMemo, useRef, useState
} from 'react';
import {
  elementType, func, node, number, shape, string
} from 'prop-types';

import '../../styles/input.scss';

import { IconEyeFilled } from './Icon';

const Input = forwardRef(({
  value, // when provided -> controlled
  defaultValue, // used only for uncontrolled
  onChange = f => f,
  maxLength,
  prefix = null, // { as, icon, text }
  suffix = null, // { as, icon, onClick }
  type = 'text',
  ...props
}, ref) => {
  // detect controlled-ness only once (React warns if it changes mid-life)
  const isControlled = value != null;
  const wasControlled = useRef(isControlled);
  if (process.env.NODE_ENV !== 'production') {
    if (wasControlled.current !== isControlled) {
      // eslint-disable-next-line no-console
      console.warn('Input switched between controlled and uncontrolled. This is not recommended.');
    }
  }

  // Internal value only for uncontrolled usage
  const [innerValue, setInnerValue] = useState(defaultValue ?? '');

  // Password visibility
  const [showPassword, setShowPassword] = useState(false);

  // Current value for rendering/counter
  const currentValue = isControlled ? (value ?? '') : innerValue;

  // Counter
  const count = useMemo(() => (maxLength ? (currentValue?.length ?? 0) : 0), [currentValue, maxLength]);

  const isPasswordType = type === 'password';
  let inputType = type;

  if (isPasswordType) {
    inputType = showPassword ? 'text' : 'password';
  }

  const togglePasswordVisibility = () => setShowPassword(prev => !prev);

  const handleChange = (e) => {
    if (!isControlled) {
      setInnerValue(e.target.value);
    }
    onChange?.(e);
  };

  const PrefixTag = prefix?.as || null;
  const SuffixTag = suffix?.as || (isPasswordType ? 'button' : null);

  let suffixIcon = null;
  if (isPasswordType) {
    suffixIcon = <IconEyeFilled />;
  } else if (suffix?.icon) {
    suffixIcon = suffix.icon;
  }

  const suffixClick = isPasswordType ? togglePasswordVisibility : suffix?.onClick;

  return (
    <div className='jfInput'>
      {PrefixTag && (
        <PrefixTag className='jfInput--prefix'>
          {prefix.icon && <span className='jfInput--prefix-icon'>{prefix.icon}</span>}
          {prefix.text && <span className='jfInput--prefix-text'>{prefix.text}</span>}
        </PrefixTag>
      )}
      <input
        ref={ref}
        {...props}
        {...(isControlled ? { value: currentValue } : { defaultValue: currentValue })}
        type={inputType}
        maxLength={maxLength}
        onChange={handleChange}
      />
      {maxLength && (
        <div className='jfInput--counter'>
          {`${count} / ${maxLength}`}
        </div>
      )}
      {SuffixTag && (
        <SuffixTag
          className='jfInput--suffix'
          onClick={suffixClick}
          type='button'
          {...(isPasswordType
            ? { 'aria-label': showPassword ? 'Hide password' : 'Show password' }
            : {})}
        >
          {suffixIcon && <span className='jfInput--suffix-icon' aria-hidden='true'>{suffixIcon}</span>}
        </SuffixTag>
      )}
    </div>
  );
});

Input.defaultProps = {
  prefix: null,
  suffix: null,
  defaultValue: ''
};

Input.propTypes = {
  // control
  value: string, // provide for controlled usage
  defaultValue: string, // initial value for uncontrolled usage
  onChange: func,

  // UI
  maxLength: number,
  type: string,

  // decorations
  prefix: shape({
    as: elementType,
    icon: node,
    text: node
  }),
  suffix: shape({
    as: elementType,
    icon: node,
    onClick: func
  })
};

export default Input;
