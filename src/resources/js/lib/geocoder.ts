import type { MeetingPartialUpdate } from 'bmlt-server-client';
import { spinner } from '../stores/spinner';
import { initGoogleMaps, loadLibraries } from './googleMapsLoader';
import { errorModal } from '../stores/errorModal';
import { translations } from '../stores/localization';

export type GeocodeResult = {
  lat: number;
  lng: number;
  county: string;
  zipCode: string;
};

type GeocodeResponse = {
  results: google.maps.GeocoderResult[] | null;
  status: google.maps.GeocoderStatus;
};

const POSTAL_CODE_TYPE = 'postal_code';
const ADMIN_AREA_LEVEL_2 = 'administrative_area_level_2';
const COUNTY_SUFFIX = ' County';

function removeCountySuffix(county: string): string {
  return county.endsWith(COUNTY_SUFFIX) ? county.slice(0, -COUNTY_SUFFIX.length) : county;
}

function promisifyGeocode(geocoder: google.maps.Geocoder, address: string): Promise<GeocodeResponse> {
  return new Promise<GeocodeResponse>((resolve) => {
    geocoder.geocode({ address }, (results, status) => {
      resolve({ results, status });
    });
  });
}

(window as any).gm_authFailure = () => {
  settings.googleApiKeyIsBad = true;
  console.error('Google Maps authentication failed - detected in gm_authFailure callback');
  errorModal.show({
    title: translations.getString('googleKeyProblemTitle'),
    message: translations.getString('googleKeyProblemDescription'),
    timestamp: new Date()
  });
};

export class Geocoder {
  private readonly address: string;

  constructor(meeting: MeetingPartialUpdate) {
    if (!meeting.locationNation) {
      meeting.locationNation = settings.regionBias;
    }

    this.address = [
      meeting.locationStreet,
      meeting.locationCitySubsection,
      meeting.locationMunicipality,
      meeting.locationProvince,
      // Omit county (stored as locationSubProvince) or zip (stored as locationPostalCode1) if these are computed automatically by the geocoder.
      // This is an issue if we are updating an address -- the meeting object will have their previously computed values, since the user can't
      // update them.  We don't want to feed these old and perhaps incorrect values to the geocoder.
      settings.countyAutoGeocodingEnabled ? undefined : meeting.locationSubProvince,
      settings.zipAutoGeocodingEnabled ? undefined : meeting.locationPostalCode1,
      meeting.locationNation
    ]
      .filter(Boolean)
      .join(', ');
  }

  private async geocodeWithGoogle(): Promise<GeocodeResult | string> {
    if (settings.googleApiKeyIsBad) {
      const e = translations.getString('googleGeocodingFailed');
      console.error(e);
      return e;
    }
    const geocoder = new google.maps.Geocoder();
    const { results, status } = await promisifyGeocode(geocoder, this.address);

    if (status === google.maps.GeocoderStatus.OK && results) {
      const location = results[0].geometry.location;
      let county = '';
      let zipCode = '';

      results[0].address_components.forEach((component) => {
        if (component.types.includes(POSTAL_CODE_TYPE)) {
          zipCode = component.long_name;
        }
        if (component.types.includes(ADMIN_AREA_LEVEL_2)) {
          county = removeCountySuffix(component.long_name);
        }
      });

      return {
        lat: location.lat(),
        lng: location.lng(),
        county,
        zipCode
      };
    } else {
      const e = translations.getString('googleGeocodingFailed') + ': ' + status;
      console.error(e);
      return e;
    }
  }

  private async geocodeWithNominatim(): Promise<GeocodeResult | string> {
    const nominatimUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.address)}`;
    const response = await fetch(nominatimUrl);
    const data = await response.json();

    if (data && data.length > 0) {
      const result = data[0];
      const lat = parseFloat(result.lat);
      const lon = parseFloat(result.lon);
      let county = '';
      let zipCode = '';

      if (settings.countyAutoGeocodingEnabled || settings.zipAutoGeocodingEnabled) {
        const reverseUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;
        const reverseResponse = await fetch(reverseUrl);
        const reverseData = await reverseResponse.json();
        const address = reverseData.address || {};
        county = removeCountySuffix(address.county || '');
        zipCode = address.postcode || '';
      }

      return { lat, lng: lon, county, zipCode };
    } else {
      const e = translations.getString('nominatimGeocodingFailed');
      console.error(e);
      return e;
    }
  }

  private isGoogleMapsLoaded(): boolean {
    return typeof google !== 'undefined' && !!google.maps;
  }

  public async geocode(): Promise<GeocodeResult | string> {
    spinner.show();

    try {
      const shouldUseGoogle = await this.ensureGoogleMapsReady();
      return shouldUseGoogle ? await this.geocodeWithGoogle() : await this.geocodeWithNominatim();
    } catch (error) {
      const e = translations.getString('geocodingFailed') + ': ' + error;
      console.error(e);
      return e;
    } finally {
      spinner.hide();
    }
  }

  private async ensureGoogleMapsReady(): Promise<boolean> {
    if (!settings.googleApiKey) {
      return false;
    }

    if (this.isGoogleMapsLoaded()) {
      return true;
    }

    try {
      await initGoogleMaps(settings.googleApiKey);
      await loadLibraries('geocoding');
      return true;
    } catch (loadError) {
      console.error('Failed to load Google Maps:', loadError);
      return false;
    }
  }
}
