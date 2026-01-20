import { useEffectIgnoreFirst } from './useEffectIgnoreFirst';

export const useHideGreetingTooltip = greetingBool => {
  useEffectIgnoreFirst(() => {
    if (greetingBool) return;

    const MAX_CHECKS = 100;
    let checks = 0;
    const interval = setInterval(() => {
      const el = document.querySelector('.ai-agent-chat-avatar-tooltip');
      if (el) {
        el.style.display = 'none';
        clearInterval(interval);
      } else if (checks >= MAX_CHECKS) {
        clearInterval(interval);
      } else {
        checks += 1;
      }
    }, 50);

    return () => clearInterval(interval);
  }, [greetingBool]);
};
