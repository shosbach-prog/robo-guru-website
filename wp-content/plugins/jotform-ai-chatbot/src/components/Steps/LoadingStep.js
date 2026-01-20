/* eslint-disable no-plusplus */
/* eslint-disable max-len */
import React, {
  useEffect, useMemo, useRef, useState
} from 'react';
import { string } from 'prop-types';

import '../../styles/loading.scss';

const LoadingStep = ({ type = 'default' }) => {
  const _texts = [
    'Hang tight! 🌟 We\'re connecting your prompt with your website to create the perfect AI assistant. 🤖',
    'Your chatbot is learning everything it needs to know. 📚 This won\'t take long! ⏳',
    'Did you know? 💡 You can customize your chatbot’s tone and responses later for a personal touch. 🎨',
    'We’re fine-tuning the chatbot to understand your website and deliver great answers. 🔧✨',
    'Almost there! 🚀 Your AI assistant is getting ready to shine. ✨'
  ];

  const [texts] = useState(() => {
    const textObjects = _texts.map((text, index) => ({
      id: index,
      value: text
    }));
    textObjects.push({
      id: 'clone',
      value: textObjects[0].value
    });

    return textObjects;
  });
  const [itemHeights, setItemHeights] = useState([]);

  const textContainer = useRef(null);
  const animationInterval = useRef(null);
  const animationTimeout = useRef(null);

  useEffect(() => {
    const measureHeights = () => {
      if (textContainer.current?.children) {
        const heights = Array.from(textContainer.current.children).map(
          (child) => child.offsetHeight
        );
        setItemHeights(heights);
      }
    };

    measureHeights();
    window.addEventListener('resize', measureHeights);

    return () => {
      window.removeEventListener('resize', measureHeights);
    };
  }, []);

  const cumulativeOffsets = useMemo(() => {
    if (itemHeights.length === 0) return [];

    const offsets = [0];
    let accumulatedHeight = 0;
    for (let i = 0; i < itemHeights.length - 1; i++) {
      accumulatedHeight += itemHeights[i];
      offsets.push(accumulatedHeight);
    }
    return offsets;
  }, [itemHeights]);

  useEffect(() => {
    if (cumulativeOffsets.length === 0 || !textContainer.current) {
      return undefined;
    }

    const textContainerEl = textContainer.current;
    const animationContainerEl = textContainerEl.parentElement;

    textContainerEl.style.transition = 'none';
    textContainerEl.style.transform = 'translateY(0px)';
    if (animationContainerEl) {
      animationContainerEl.style.height = `${itemHeights[0]}px`;
    }

    let count = 0;
    const textDelay = 2500;
    const transitionDuration = 300;

    animationInterval.current = setInterval(() => {
      count++;

      const offset = cumulativeOffsets[count];
      const height = itemHeights[count];

      textContainerEl.style.transition = `transform ${transitionDuration}ms ease-in-out`;
      textContainerEl.style.transform = `translateY(-${offset}px)`;
      if (animationContainerEl && height) {
        animationContainerEl.style.transition = `height ${transitionDuration}ms ease-in-out`;
        animationContainerEl.style.height = `${height}px`;
      }

      if (count === _texts.length) {
        animationTimeout.current = setTimeout(() => {
          textContainerEl.style.transition = 'none';
          textContainerEl.style.transform = 'translateY(0px)';
          if (animationContainerEl) {
            animationContainerEl.style.height = `${itemHeights[0]}px`;
          }
          count = 0;
        }, transitionDuration);
      }
    }, textDelay);

    return () => {
      clearInterval(animationInterval.current);
      clearTimeout(animationTimeout.current);
    };
  }, [cumulativeOffsets, itemHeights, _texts.length]);

  return (
    <div className='create-page-loading' role='status' aria-live='polite' aria-atomic='true'>
      {type === 'default' && (
        <div className='create-page-loading--spinner' role='status'>
          <span className='sr-only'>Loading...</span>
        </div>
      )}
      {type === 'text' && (
        <>
          <div className='create-page-loading--icon'>
            <svg
              xmlns='http://www.w3.org/2000/svg'
              width='64'
              height='64'
              viewBox='0 0 64 64'
              fill='none'
              aria-hidden='true'
            >
              <path
                fillRule='evenodd'
                clipRule='evenodd'
                d='M26.6667 5.33334C19.303 5.33334 13.3334 11.3029 13.3334 18.6667C13.3334 26.0305 19.303 32 26.6667 32C34.0305 32 40.0001 26.0305 40.0001 18.6667C40.0001 11.3029 34.0305 5.33334 26.6667 5.33334ZM54.3633 24.7171C54.0095 23.761 52.6573 23.761 52.3035 24.7171L51.7828 26.1242C51.0414 28.1281 49.4614 29.708 47.4576 30.4495L46.0505 30.9702C45.0944 31.3239 45.0944 32.6762 46.0505 33.0299L47.4576 33.5506C49.4614 34.2921 51.0414 35.872 51.7828 37.8759L52.3035 39.283C52.6573 40.2391 54.0095 40.2391 54.3633 39.283L54.884 37.8759C55.6255 35.872 57.2054 34.2921 59.2093 33.5506L60.6164 33.0299C61.5724 32.6762 61.5724 31.3239 60.6164 30.9702L59.2093 30.4495C57.2054 29.708 55.6255 28.1281 54.884 26.1242L54.3633 24.7171ZM54.9245 50.8783C56.3586 50.3476 56.3586 48.3193 54.9245 47.7886L52.8138 47.0076C49.8081 45.8954 47.4382 43.5255 46.3259 40.5197L45.5449 38.409C45.0143 36.9749 42.9859 36.9749 42.4553 38.409L41.6742 40.5197C40.562 43.5255 38.1921 45.8954 35.1863 47.0076L33.0756 47.7886C31.6416 48.3193 31.6416 50.3476 33.0756 50.8783L35.1863 51.6593C38.1921 52.7716 40.562 55.1415 41.6742 58.1473L42.4553 60.258C42.9859 61.692 45.0142 61.692 45.5449 60.258L46.3259 58.1473C47.4382 55.1415 49.8081 52.7716 52.8138 51.6593L54.9245 50.8783ZM26.6667 34.6667C13.7575 34.6667 2.66675 43.8946 2.66675 56C2.66675 57.4728 3.86066 58.6667 5.33341 58.6667H28.8823C30.1737 58.6667 31.2796 57.7413 31.5072 56.4701C31.7349 55.1989 31.0189 53.9472 29.8077 53.4991L29.4835 53.3791C25.7278 51.9894 25.7278 46.6773 29.4835 45.2876L31.5942 44.5066C33.8701 43.6644 35.6645 41.87 36.5066 39.5942L36.5732 39.4141C36.8253 38.733 36.7886 37.9785 36.4717 37.325C36.1547 36.6716 35.585 36.1756 34.894 35.9518C32.3217 35.1187 29.5496 34.6667 26.6667 34.6667Z'
                fill='url(#paint0_linear_1017_36002)'
              />
              <defs>
                <linearGradient
                  id='paint0_linear_1017_36002'
                  x1='3.27786'
                  y1='5.65145'
                  x2='45.8044'
                  y2='71.4473'
                  gradientUnits='userSpaceOnUse'
                >
                  <stop stopColor='white' />
                  <stop offset='1' stopColor='#D1B3E6' />
                </linearGradient>
              </defs>
            </svg>
          </div>
          <div className='create-page-loading--animation'>
            <span className='sr-only'>Your chatbot is being created. Please wait...</span>
            <ul className='create-page-loading--text' ref={textContainer} aria-hidden='true'>
              {texts.map((textItem) => (
                <li key={textItem.id}>
                  <span>{textItem.value}</span>
                </li>
              ))}
            </ul>
          </div>
        </>
      )}
    </div>

  );
};

LoadingStep.propTypes = {
  type: string
};

export default LoadingStep;
