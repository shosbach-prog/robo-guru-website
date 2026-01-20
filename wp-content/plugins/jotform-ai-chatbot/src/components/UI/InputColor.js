import React, { useEffect, useRef, useState } from 'react';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/react';
import PropTypes from 'prop-types';
import { SketchPicker } from 'react-color';

import '../../styles/input-color.scss';

import Input from './Input';

const InputColor = ({ defaultValue = '', onChange }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [value, setValue] = useState(defaultValue);
  const containerRef = useRef(null);

  useEffect(() => setValue(defaultValue), [defaultValue]);

  const handleClickOutside = (event) => {
    if (containerRef.current && !containerRef.current.contains(event.target)) {
      setIsOpen(false);
    }
  };

  useEffect(() => {
    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    } else {
      document.removeEventListener('mousedown', handleClickOutside);
    }
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isOpen]);

  const handleColorPickerChange = (val) => {
    setValue(val.hex);
    onChange(val.hex);
  };

  const handleInputChange = (val) => {
    setValue(val);
    onChange(val);
  };

  return (
    <div className='jfInputColor' ref={containerRef}>
      <Input value={value} onChange={(e) => handleInputChange(e.target.value)} />
      <Popover className='jfInputColor--picker'>
        <PopoverButton style={{ background: value }}>
          <span className='sr-only'>
            Open color picker
          </span>
        </PopoverButton>
        <PopoverPanel
          transition
          anchor='top'
          className='jfInputColor--popover'
        >
          <SketchPicker
            color={value}
            onChange={handleColorPickerChange}
            disableAlpha
          />
        </PopoverPanel>
      </Popover>
    </div>
  );
};

InputColor.propTypes = {
  defaultValue: PropTypes.string,
  onChange: PropTypes.func.isRequired
};

export default InputColor;
