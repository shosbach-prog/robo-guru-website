import { useEffect } from 'react';

export const useElementScrollListener = ({ targetElement = null, callback = f => f, threshold = 100 }) => {
  useEffect(() => {
    if (!targetElement) return;

    let totalScroll = 0;
    let lastScrollTop = targetElement.scrollTop;

    const handleScroll = () => {
      const currentScrollTop = targetElement.scrollTop;
      totalScroll += Math.abs(currentScrollTop - lastScrollTop);
      lastScrollTop = currentScrollTop;

      if (totalScroll >= threshold) {
        callback();
        totalScroll = 0; // reset after triggering
      }
    };

    targetElement.addEventListener('scroll', handleScroll);
    return () => targetElement.removeEventListener('scroll', handleScroll);
  }, [threshold, callback]);
};
