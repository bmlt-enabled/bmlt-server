declare global {
  declare const settings: {
    apiBaseUrl: string;
    bmltTitle: string;
    bmltNotice: string;
    autoGeocodingEnabled: true;
    centerLongitude: number;
    centerLatitude: number;
    centerZoom: number;
    countyAutoGeocodingEnabled: boolean;
    customFields: { name: string; displayName: string; language: string }[];
    defaultClosedStatus: boolean;
    defaultDuration: string;
    defaultLanguage: string;
    distanceUnits: string;
    googleApiKey: string;
    googleApiKeyIsBad: boolean;
    isLanguageSelectorEnabled: boolean;
    languageMapping: Record<string, string>;
    meetingStatesAndProvinces: string[];
    meetingCountiesAndSubProvinces: string[];
    regionBias: string;
    version: string;
    zipAutoGeocodingEnabled: false;
  };
}

declare module 'localized-strings' {
  export interface LocalizedStringsMethods {
    getContent(): any;
  }
}

export {};
