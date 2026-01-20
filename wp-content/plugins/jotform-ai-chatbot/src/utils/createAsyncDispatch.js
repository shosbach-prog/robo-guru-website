export const createAsyncDispatch = dispatch => async (
  asyncAction = f => f,
  requestAction = f => f,
  successAction = f => f,
  errorAction = f => f,
  ...additionalParams
) => {
  let result = null;
  if (requestAction) dispatch(requestAction(...additionalParams));
  try {
    result = await asyncAction();
    if (successAction) dispatch(successAction(result, ...additionalParams));
  } catch (error) {
    if (errorAction) dispatch(errorAction(error, ...additionalParams));
    return result;
  }
  return result;
};
