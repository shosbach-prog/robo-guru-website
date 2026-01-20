const sanitizeFilename = filename => (
  filename
    .replace(/[^a-zA-Z0-9_\-.]/g, '_')
    .replace(/^\.+|\.+$/g, '')
    .slice(0, 255)
);

export const prepareFile = files => {
  const file = files[0] || null;

  return new File([file], sanitizeFilename(file.name), {
    type: file.type,
    lastModified: file.lastModified
  });
};

export const base64ToFile = (base64, filename, mimeType) => {
  const byteCharacters = atob(base64.split(',')[1]);
  const byteArrays = [];

  // eslint-disable-next-line no-plusplus
  for (let offset = 0; offset < byteCharacters.length; offset++) {
    const byteArray = byteCharacters.charCodeAt(offset);
    byteArrays.push(byteArray);
  }

  const blob = new Blob([new Uint8Array(byteArrays)], { type: mimeType });
  const file = new File([blob], filename, { type: mimeType });

  return file;
};

export const formatFileSize = (bytes) => {
  if (bytes >= 1024 * 1024) {
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
  }
  return `${(bytes / 1024).toFixed(2)} KB`;
};
