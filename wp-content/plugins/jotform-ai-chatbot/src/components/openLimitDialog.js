import React from 'react';
import { createRoot } from 'react-dom/client';

import LimitDialog from './LimitDialog';

export const openLimitDialog = ({ container, onClose = f => f, ...props }) => {
  const root = createRoot(container);

  const handleClose = () => {
    root.unmount();
    onClose();
  };

  root.render(
    <LimitDialog open onCloseClick={handleClose} {...props} />
  );
};
