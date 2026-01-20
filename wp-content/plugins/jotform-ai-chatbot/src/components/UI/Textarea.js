import React, { forwardRef, useEffect, useState } from 'react';
import { func, number } from 'prop-types';

import '../../styles/textarea.scss';

const Textarea = forwardRef(({ maxLength, onChange = f => f, ...props }, ref) => {
  const [count, setCount] = useState(0);

  const handleChange = (e) => {
    setCount(e.target.value.length);
    if (onChange) {
      onChange(e);
    }
  };

  useEffect(() => {
    if (ref?.current?.value) {
      setCount(ref.current.value.length);
    }
  }, [ref]);

  return (
    <div className='jfTextarea-container'>
      <textarea
        ref={ref}
        {...props}
        maxLength={maxLength}
        onChange={handleChange}
      />
      {maxLength && (
        <div className='jfTextarea-container--counter'>
          {`${count} / ${maxLength}`}
        </div>
      )}
    </div>
  );
});

Textarea.propTypes = {
  maxLength: number,
  onChange: func
};

export default Textarea;
