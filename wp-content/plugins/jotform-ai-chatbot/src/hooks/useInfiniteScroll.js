import { useEffect } from 'react';

export function useInfiniteScroll(containerRef, {
  loading,
  hasMore,
  onLoadMore,
  threshold = 1
}) {
  useEffect(() => {
    const el = containerRef.current;
    if (!el) return;

    const handleScroll = () => {
      if (loading || !hasMore) return;
      const { scrollTop, scrollHeight, clientHeight } = el;
      if (scrollTop + clientHeight >= scrollHeight - threshold) {
        onLoadMore();
      }
    };

    el.addEventListener('scroll', handleScroll, { passive: true });
    return () => el.removeEventListener('scroll', handleScroll);
  }, [containerRef, loading, hasMore, onLoadMore, threshold]);
}
