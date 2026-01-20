import React from 'react';

const LineLoader = ({
  // eslint-disable-next-line react/prop-types
  status
}) => {
  const percentage = {
    IN_PROGRESS: '20%',
    STEP1: '40%',
    STEP2: '60%',
    STEP3: '80%',
    PROCESSED: '100%'
  };

  return (
    <div className='jfMaterialStatus--line-loader'>
      <div className='jfMaterialStatus--line-loader-bar' style={{ width: percentage[status] }} />
    </div>
  );
};

export default LineLoader;
