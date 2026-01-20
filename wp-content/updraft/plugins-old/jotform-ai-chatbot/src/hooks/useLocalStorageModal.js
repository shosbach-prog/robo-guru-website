import { useEffect, useState } from 'react';

/**
 * useLocalStorageModal
 * - Reads a localStorage flag to determine if modal should show.
 * - If the flag is falsy or missing, shows modal and sets the flag.
 *
 * @param {string} key - The localStorage key to use for the flag.
 * @returns {[boolean, Function]} - [modalVisible, closeModal]
 */
export const useLocalStorageModal = (key) => {
  const [isModalVisible, setIsModalVisible] = useState(false);

  useEffect(() => {
    try {
      const shown = localStorage.getItem(key);
      if (!shown) {
        setIsModalVisible(true);
        localStorage.setItem(key, 'true');
      }
    } catch (err) {
      console.error('LocalStorage error:', err);
      // fallback: show modal if localStorage is unavailable
      setIsModalVisible(true);
    }
  }, [key]);

  const closeModal = () => setIsModalVisible(false);

  return [isModalVisible, closeModal];
};
