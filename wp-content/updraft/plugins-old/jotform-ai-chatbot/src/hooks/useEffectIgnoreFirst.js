import { useEffect, useRef } from 'react';

export const useEffectIgnoreFirst = (effect, deps) => {
  const isFirstRun = useRef(true);

  useEffect(() => {
    if (isFirstRun.current) {
      isFirstRun.current = false;
      return;
    }
    return effect();
  }, deps);
};

export default useEffectIgnoreFirst;
