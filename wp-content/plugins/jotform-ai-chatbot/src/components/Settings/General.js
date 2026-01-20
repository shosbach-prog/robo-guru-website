import React from 'react';

// import ConnectedChatbot from './general/ConnectedChatbot';
import ConnectedJotformAccount from './general/ConnectedJotformAccount';

const General = () => (
  <div className='jfpContent-wrapper--settings-options-wrapper general'>
    <ConnectedJotformAccount />
    {/* <ConnectedChatbot /> */}
  </div>
);

export default General;
