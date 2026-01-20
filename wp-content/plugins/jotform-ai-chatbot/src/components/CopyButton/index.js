import React, { useEffect, useRef } from 'react';
import ClipboardJS from 'clipboard';
import {
  bool,
  func, node, string
} from 'prop-types';

import { isPressedKeyEnter } from '../../utils';

const CopyButton = ({
  children, textToCopy, onCopy, hideOnUnsupportedEnvironment, ...props
}) => {
  const ref = useRef();
  useEffect(() => {
    if (ref.current) {
      const clipboard = new ClipboardJS(ref.current, { text: () => textToCopy });
      clipboard.on('success', e => onCopy(e, textToCopy));
      return () => clipboard.destroy();
    }
  }, [ref, textToCopy]);
  const isHidden = hideOnUnsupportedEnvironment && !ClipboardJS.isSupported();

  const handleKeyDown = e => isPressedKeyEnter(e) && ref.current.click(); // shitty clipboardJS, so workaround

  return !isHidden && (
    <div
      ref={ref}
      {...props}
      tabIndex={0}
      role='button'
      onKeyDown={handleKeyDown}
    >
      {children}
    </div>
  );
};

CopyButton.propTypes = {
  children: node.isRequired,
  textToCopy: string.isRequired,
  hideOnUnsupportedEnvironment: bool,
  onCopy: func
};

CopyButton.defaultProps = {
  onCopy: f => f,
  hideOnUnsupportedEnvironment: false
};

export default CopyButton;
