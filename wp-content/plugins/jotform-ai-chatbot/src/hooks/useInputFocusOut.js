import { useEffect, useRef, useState } from 'react';
import debounce from 'lodash/debounce';

export const useInputFocusOut = (inputRef = { current: '' }) => {
  const [triggerFocusOut, setTriggerFocusOut] = useState(false);
  const previousValRef = useRef(inputRef?.current?.value);

  useEffect(() => {
    if (!inputRef.current) return;

    const DEBOUNCE_MS = 1500;
    const debouncedCallback = debounce(() => setTriggerFocusOut(curr => !curr), DEBOUNCE_MS);

    const handleFocusOut = () => {
      if (previousValRef.current && previousValRef.current !== inputRef.current.value) {
        debouncedCallback();
      }
      previousValRef.current = inputRef.current.value;
    };

    const node = inputRef.current;
    node.addEventListener('focusout', handleFocusOut);

    return () => {
      node.removeEventListener('focusout', handleFocusOut);
      if (debouncedCallback.cancel) {
        debouncedCallback.cancel();
      }
    };
  }, [inputRef.current]);

  return triggerFocusOut;
};
