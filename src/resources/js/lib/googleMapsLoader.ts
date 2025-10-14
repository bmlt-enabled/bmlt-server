import { setOptions, importLibrary } from '@googlemaps/js-api-loader';

let isInitialized = false;

export async function initGoogleMaps(apiKey: string) {
  if (!isInitialized) {
    setOptions({
      key: apiKey,
      v: 'weekly'
    });
    isInitialized = true;
  }
}

export async function loadLibraries(...libraries: string[]) {
  return await Promise.all(libraries.map((lib) => importLibrary(lib)));
}
