import { useEffect, useRef } from 'react';

export const useAvatarFocus = (avatarsLength) => {
  const avatarButtonRefs = useRef([]);
  const prevAvatarsCount = useRef(0);
  const isShowMoreClicked = useRef(false);

  useEffect(() => {
    if (isShowMoreClicked.current && avatarsLength > prevAvatarsCount.current) {
      const firstNewAvatarButton = avatarButtonRefs.current[prevAvatarsCount.current];
      if (firstNewAvatarButton) {
        firstNewAvatarButton.focus();
      }
      isShowMoreClicked.current = false;
    }
    prevAvatarsCount.current = avatarsLength;
  }, [avatarsLength]);

  return {
    avatarButtonRefs,
    prevAvatarsCount,
    isShowMoreClicked
  };
};
