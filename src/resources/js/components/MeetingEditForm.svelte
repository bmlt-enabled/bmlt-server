<script lang="ts">
  import { SvelteSet } from 'svelte/reactivity';
  import { validator } from '@felte/validator-yup';
  import { createForm } from 'felte';
  import { Button, Checkbox, Hr, Label, Input, Helper, Select, MultiSelect, Badge, Spinner, Textarea, Tooltip } from 'flowbite-svelte';
  import { LockOutline } from 'flowbite-svelte-icons';
  import * as yup from 'yup';
  import L from 'leaflet';
  import { writable } from 'svelte/store';

  const showMap = writable(false);
  import DurationSelector from './DurationSelector.svelte';
  import MapAccordion from './MapAccordion.svelte';
  import BasicTabs from './BasicTabs.svelte';

  import { onMount } from 'svelte';
  import { spinner } from '../stores/spinner';
  import type { MeetingChangeResource } from 'bmlt-server-client';
  import RootServerApi from '../lib/ServerApi';
  import { formIsDirty, isDirty as globalIsDirty, stripLegacyFieldSeparator } from '../lib/utils';
  import { timeZones, timeZoneGroups } from '../lib/timeZone/timeZones';
  import { tzFind } from '../lib/timeZone/find';
  import { Geocoder } from '../lib/geocoder';
  import { initGoogleMaps } from '../lib/googleMapsLoader';
  import { importLibrary } from '@googlemaps/js-api-loader';

  import type { Format, Meeting, MeetingPartialUpdate, ServiceBody } from 'bmlt-server-client';
  import { translations } from '../stores/localization';
  import MeetingDeleteModal from './MeetingDeleteModal.svelte';
  import { TrashBinOutline } from 'flowbite-svelte-icons';

  interface Props {
    selectedMeeting: Meeting | null;
    serviceBodies: ServiceBody[];
    formats: Format[];
    onSaved: (meeting: Meeting) => void;
    onClosed: () => void;
    onDeleted: (meeting: Meeting) => void;
  }

  let { selectedMeeting, serviceBodies, formats, onSaved, onDeleted }: Props = $props();

  const daysOfWeek: string[] = [$translations.day0, $translations.day1, $translations.day2, $translations.day3, $translations.day4, $translations.day5, $translations.day6];

  const tabs = selectedMeeting
    ? [$translations.tabsBasic, $translations.tabsLocation, $translations.tabsOther, $translations.tabsChanges]
    : [$translations.tabsBasic, $translations.tabsLocation, $translations.tabsOther];
  const CHANGES_TAB_INDEX = 3;
  const seenNames = new SvelteSet<string>();
  const ignoredFormats = ['VM', 'HY', 'TC'];
  const filteredFormats = formats
    .map((format) => {
      const translation = format.translations.find((t) => t.language === translations.getLanguage());
      if (translation) {
        return {
          id: format.id,
          type: format.type,
          worldId: format.worldId,
          ...translation
        };
      }
      return null;
    })
    .filter((format) => {
      if (!format) return false;
      if (ignoredFormats.some((ignored) => format.key.includes(ignored))) {
        return false;
      }
      if (seenNames.has(format.name)) return false;
      seenNames.add(format.name);
      return true;
    });

  const serviceBodyIdItems = serviceBodies.map((u) => ({ value: u.id, name: u.name })).sort((a, b) => a.name.localeCompare(b.name));
  const VENUE_TYPE_IN_PERSON = 1;
  const VENUE_TYPE_VIRTUAL = 2;
  const VENUE_TYPE_HYBRID = 3;
  const VALID_VENUE_TYPES = [VENUE_TYPE_IN_PERSON, VENUE_TYPE_VIRTUAL, VENUE_TYPE_HYBRID];

  let map: google.maps.Map | L.Map | null = $state(null);
  let mapElement: HTMLElement | undefined = $state();
  let mapError = $state<string | null>(null);
  let marker: google.maps.marker.AdvancedMarkerElement | L.Marker | null = $state(null);
  let geocodingError: string | null = $state(null);
  let isPublishedChecked = $state(true);
  let showDeleteModal = $state(false);
  let meetingToDelete: Meeting | undefined = $state();
  const weekdayChoices = daysOfWeek.map((day: string, index: number) => ({
    value: index,
    name: day
  }));
  const statesAndProvincesChoices = settings.meetingStatesAndProvinces
    .map((state) => ({
      value: state,
      name: state
    }))
    .sort((a, b) => a.name.localeCompare(b.name));
  const countiesAndSubProvincesChoices = settings.meetingCountiesAndSubProvinces
    .map((county) => ({
      value: county,
      name: county
    }))
    .sort((a, b) => a.name.localeCompare(b.name));
  const venueTypeItems = [
    { value: VENUE_TYPE_IN_PERSON, name: 'In-Person' },
    { value: VENUE_TYPE_VIRTUAL, name: 'Virtual' },
    { value: VENUE_TYPE_HYBRID, name: 'Hybrid' }
  ];

  const defaultLatLng = { lat: Number(settings.centerLatitude ?? -79.793701171875), lng: Number(settings.centerLongitude ?? 36.065752051707) };
  let defaultDuration = '01:00';
  // older autoconfig files store the default duration including seconds -- remove the seconds if needed for compatibility
  if (settings.defaultDuration) {
    const [hours, minutes] = settings.defaultDuration.split(':').map((part) => part.padStart(2, '0'));
    defaultDuration = hours + ':' + minutes;
  }
  const initialValues = {
    serviceBodyId: selectedMeeting?.serviceBodyId ?? (serviceBodies.length === 1 ? serviceBodies[0].id : -1),
    formatIds: selectedMeeting?.formatIds ?? [],
    venueType: selectedMeeting?.venueType ?? VENUE_TYPE_IN_PERSON,
    temporarilyVirtual: selectedMeeting?.temporarilyVirtual ?? false,
    day: selectedMeeting?.day ?? 0,
    startTime: selectedMeeting?.startTime ?? '12:00',
    duration: selectedMeeting?.duration ?? defaultDuration,
    timeZone: selectedMeeting?.timeZone ?? '',
    latitude: selectedMeeting?.latitude ?? defaultLatLng.lat,
    longitude: selectedMeeting?.longitude ?? defaultLatLng.lng,
    published: selectedMeeting?.published ?? true,
    email: selectedMeeting?.email ?? '',
    worldId: selectedMeeting?.worldId ?? '',
    name: selectedMeeting?.name ?? '',
    locationText: selectedMeeting?.locationText ?? '',
    locationInfo: selectedMeeting?.locationInfo ?? '',
    locationStreet: selectedMeeting?.locationStreet ?? '',
    locationNeighborhood: selectedMeeting?.locationNeighborhood ?? '',
    locationCitySubsection: selectedMeeting?.locationCitySubsection ?? '',
    locationMunicipality: selectedMeeting?.locationMunicipality ?? '',
    locationSubProvince: selectedMeeting?.locationSubProvince ?? '',
    locationProvince: selectedMeeting?.locationProvince ?? '',
    locationPostalCode1: selectedMeeting?.locationPostalCode1 ?? '',
    locationNation: selectedMeeting?.locationNation ?? '',
    phoneMeetingNumber: selectedMeeting?.phoneMeetingNumber ?? '',
    virtualMeetingLink: selectedMeeting?.virtualMeetingLink ?? '',
    virtualMeetingAdditionalInfo: selectedMeeting?.virtualMeetingAdditionalInfo ?? '',
    contactName1: selectedMeeting?.contactName1 ?? '',
    contactName2: selectedMeeting?.contactName2 ?? '',
    contactPhone1: selectedMeeting?.contactPhone1 ?? '',
    contactPhone2: selectedMeeting?.contactPhone2 ?? '',
    contactEmail1: selectedMeeting?.contactEmail1 ?? '',
    contactEmail2: selectedMeeting?.contactEmail2 ?? '',
    adminNotes: selectedMeeting?.adminNotes ?? '',
    busLines: stripLegacyFieldSeparator(selectedMeeting?.busLines),
    trainLines: stripLegacyFieldSeparator(selectedMeeting?.trainLines),
    comments: selectedMeeting?.comments ?? '',
    customFields: selectedMeeting?.customFields
      ? {
          ...Object.fromEntries(settings.customFields.map((field) => [field.name, ''])),
          ...Object.fromEntries(Object.entries(selectedMeeting.customFields).map(([key, value]) => [key, stripLegacyFieldSeparator(value)]))
        }
      : Object.fromEntries(settings.customFields.map((field) => [field.name, '']))
  };

  function hasValidCoordinates(lat: number, lng: number): boolean {
    return typeof lat === 'number' && typeof lng === 'number' && !isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180 && lat !== 0 && lng !== 0;
  }

  const initialLat = hasValidCoordinates(initialValues.latitude, initialValues.longitude) ? initialValues.latitude : defaultLatLng.lat;
  const initialLng = hasValidCoordinates(initialValues.latitude, initialValues.longitude) ? initialValues.longitude : defaultLatLng.lng;

  let latitude = $state(initialLat);
  let longitude = $state(initialLng);
  let manualDrag = false;
  let formatIdsSelected = $state(initialValues.formatIds);
  let savedMeeting: Meeting;
  let changes: MeetingChangeResource[] = $state([]);
  let changesLoadedForMeetingId: number | null = $state(null);
  let changesLoading = $state(false);
  let saveAsCopy = $state(false);

  function shouldGeocode(initialValues: MeetingPartialUpdate, values: MeetingPartialUpdate, isNewMeeting: boolean) {
    if (isNewMeeting && values.venueType != VENUE_TYPE_VIRTUAL) {
      return true;
    }

    return (
      initialValues.locationStreet !== values.locationStreet ||
      initialValues.locationCitySubsection !== values.locationCitySubsection ||
      initialValues.locationMunicipality !== values.locationMunicipality ||
      initialValues.locationProvince !== values.locationProvince ||
      initialValues.locationSubProvince !== values.locationSubProvince
    );
  }

  async function handleGeocoding(values: MeetingPartialUpdate) {
    const geocoder = new Geocoder(values);
    const geocodeResult = await geocoder.geocode();
    if (typeof geocodeResult === 'string') {
      geocodingError = geocodeResult;
      spinner.hide();
      throw new Error(geocodeResult);
    }
    if (geocodeResult) {
      values.latitude = geocodeResult.lat;
      values.longitude = geocodeResult.lng;
      if (settings.countyAutoGeocodingEnabled) {
        values.locationSubProvince = geocodeResult.county;
      }
      if (settings.zipAutoGeocodingEnabled) {
        values.locationPostalCode1 = geocodeResult.zipCode;
      }
    }
  }

  const { data, errors, form, setData, isDirty } = createForm({
    initialValues: initialValues,
    onSubmit: async (values) => {
      spinner.show();
      const isNewMeeting = !selectedMeeting;

      if (shouldGeocode(initialValues, values, isNewMeeting)) {
        if (settings.autoGeocodingEnabled && !manualDrag) {
          await handleGeocoding(values);
        }
      }

      if (!values.timeZone && values.latitude && values.longitude) {
        try {
          const tzData = await tzFind(values.latitude, values.longitude);

          if (!tzData || tzData.length === 0) {
            errors.set({
              ...errors.value,
              timeZone: $translations.timeZoneGeocodeError
            });
            spinner.hide();
            return;
          }

          const validTimeZone = tzData.find((tz) => timeZones.includes(tz));

          if (validTimeZone) {
            values.timeZone = validTimeZone;
          } else {
            errors.set({
              ...errors.value,
              timeZone: $translations.timeZoneGeocodeError
            });
            spinner.hide();
            return;
          }
        } catch (error) {
          console.error('Timezone lookup failed:', error);
          if (!errors.value?.timeZone) {
            errors.set({
              ...errors.value,
              timeZone: $translations.timeZoneGeocodeError
            });
          }
          spinner.hide();
          return;
        }
      }

      if (selectedMeeting && saveAsCopy) {
        const copyData = {
          ...values,
          worldId: ''
        };
        savedMeeting = await RootServerApi.createMeeting(copyData);
      } else if (selectedMeeting) {
        await RootServerApi.updateMeeting(selectedMeeting.id, values);
        savedMeeting = await RootServerApi.getMeeting(selectedMeeting.id);
      } else {
        savedMeeting = await RootServerApi.createMeeting(values);
      }
    },
    onError: async (error) => {
      console.log(error);
      await RootServerApi.handleErrors(error as Error, {
        handleValidationError: (error) => {
          errors.set({
            serviceBodyId: (error?.errors?.serviceBodyId ?? []).join(' '),
            formatIds: (error?.errors?.formatIds ?? []).join(' '),
            venueType: (error?.errors?.venueType ?? []).join(' '),
            temporarilyVirtual: (error?.errors?.temporarilyVirtual ?? []).join(' '),
            day: (error?.errors?.day ?? []).join(' '),
            startTime: (error?.errors?.startTime ?? []).join(' '),
            duration: (error?.errors?.duration ?? []).join(' '),
            timeZone: (error?.errors?.timeZone ?? []).join(' '),
            latitude: (error?.errors?.latitude ?? []).join(' '),
            longitude: (error?.errors?.longitude ?? []).join(' '),
            published: (error?.errors?.published ?? []).join(' '),
            email: (error?.errors?.email ?? []).join(' '),
            worldId: (error?.errors?.worldId ?? []).join(' '),
            name: (error?.errors?.name ?? []).join(' '),
            locationText: (error?.errors?.location_text ?? []).join(' '),
            locationInfo: (error?.errors?.location_info ?? []).join(' '),
            locationStreet: (error?.errors?.location_street ?? []).join(' '),
            locationNeighborhood: (error?.errors?.location_neighborhood ?? []).join(' '),
            locationCitySubsection: (error?.errors?.location_city_subsection ?? []).join(' '),
            locationMunicipality: (error?.errors?.location_municipality ?? []).join(' '),
            locationSubProvince: (error?.errors?.location_sub_province ?? []).join(' '),
            locationProvince: (error?.errors?.location_province ?? []).join(' '),
            locationPostalCode1: (error?.errors?.location_postal_code_1 ?? []).join(' '),
            locationNation: (error?.errors?.location_nation ?? []).join(' '),
            phoneMeetingNumber: (error?.errors?.phone_meeting_number ?? []).join(' '),
            virtualMeetingLink: (error?.errors?.virtual_meeting_link ?? []).join(' '),
            virtualMeetingAdditionalInfo: (error?.errors?.virtual_meeting_additional_info ?? []).join(' '),
            contactName1: (error?.errors?.contact_name_1 ?? []).join(' '),
            contactName2: (error?.errors?.contact_name_2 ?? []).join(' '),
            contactPhone1: (error?.errors?.contact_phone_1 ?? []).join(' '),
            contactPhone2: (error?.errors?.contact_phone_2 ?? []).join(' '),
            contactEmail1: (error?.errors?.contact_email_1 ?? []).join(' '),
            contactEmail2: (error?.errors?.contact_email_2 ?? []).join(' '),
            adminNotes: (error?.errors?.adminNotes ?? []).join(' '),
            busLines: (error?.errors?.bus_lines ?? []).join(' '),
            trainLines: (error?.errors?.train_lines ?? []).join(' '),
            comments: (error?.errors?.comments ?? []).join(' '),
            customFields: error?.errors?.customFields ? Object.fromEntries(Object.entries(error.errors.customFields).map(([key, value]) => [key, Array.isArray(value) ? value.join(' ') : value])) : {}
          });
        }
      });
      spinner.hide();
    },
    onSuccess: () => {
      spinner.hide();
      if (savedMeeting) {
        onSaved(savedMeeting);
      }
    },
    extend: validator({
      schema: yup.object({
        serviceBodyId: yup.number().required().min(1, $translations.serviceBodyInvalid),
        formatIds: yup.array().of(yup.number()),
        venueType: yup.number().oneOf(VALID_VENUE_TYPES).required(),
        temporarilyVirtual: yup.bool(),
        day: yup.number().integer().min(0).max(6).required(),
        startTime: yup
          .string()
          .matches(/^([0-1]\d|2[0-3]):([0-5]\d)$/)
          .required(), // HH:mm
        duration: yup
          .string()
          .matches(/^([0-1]\d|2[0-3]):([0-5]\d)$/)
          .required(), // HH:mm
        timeZone: yup
          .string()
          .oneOf([...timeZones, ''], $translations.timeZoneInvalid)
          .max(40),
        latitude: yup.number().min(-90).max(90).required(),
        longitude: yup.number().min(-180).max(180).required(),
        published: yup.bool().required(),
        email: yup.string().max(255).email(),
        worldId: yup
          .string()
          .transform((v) => v.trim())
          .max(30),
        name: yup
          .string()
          .transform((v) => v.trim())
          .max(128)
          .required(),
        locationText: yup.string().transform((v) => v.trim()),
        locationInfo: yup.string().transform((v) => v.trim()),
        locationStreet: yup
          .string()
          .default('')
          .transform((v) => v.trim())
          .max(255)
          .when('venueType', {
            is: (venueType: number) => [VENUE_TYPE_IN_PERSON, VENUE_TYPE_HYBRID].includes(venueType),
            then: (schema) => schema.required($translations.locationStreetErrorMessage),
            otherwise: (schema) => schema.notRequired()
          }),
        locationNeighborhood: yup.string().transform((v) => v.trim()),
        locationCitySubsection: yup.string().transform((v) => v.trim()),
        locationMunicipality: yup.string().transform((v) => v.trim()),
        locationSubProvince: yup.string().transform((v) => v.trim()),
        locationProvince: yup.string().transform((v) => v.trim()),
        locationPostalCode1: yup.string().transform((v) => v.trim()),
        locationNation: yup.string().transform((v) => v.trim()),
        phoneMeetingNumber: yup.string().transform((v) => v.trim()),
        virtualMeetingLink: yup
          .string()
          .transform((v) => v.trim())
          .url(),
        virtualMeetingAdditionalInfo: yup.string().transform((v) => v.trim()),
        contactName1: yup.string().transform((v) => v.trim()),
        contactName2: yup.string().transform((v) => v.trim()),
        contactPhone1: yup.string().transform((v) => v.trim()),
        contactPhone2: yup.string().transform((v) => v.trim()),
        contactEmail1: yup
          .string()
          .transform((v) => v.trim())
          .email(),
        contactEmail2: yup
          .string()
          .transform((v) => v.trim())
          .email(),
        adminNotes: yup.string().transform((v) => v.trim()),
        busLines: yup.string().transform((v) => v.trim()),
        trainLines: yup.string().transform((v) => v.trim()),
        comments: yup.string().transform((v) => v.trim())
      }),
      castValues: true
    })
  });

  const FORMAT_TYPE_GROUPS = [
    { type: 'MEETING_FORMAT', name: $translations.formatTypeCode_MEETING_FORMAT, color: 'green' },
    { type: 'OPEN_OR_CLOSED', name: $translations.formatTypeCode_OPEN_OR_CLOSED, color: 'pink' },
    { type: 'COMMON_NEEDS_OR_RESTRICTION', name: $translations.formatTypeCode_COMMON_NEEDS_OR_RESTRICTION, color: 'blue' },
    { type: 'LOCATION', name: $translations.formatTypeCode_LOCATION, color: 'purple' },
    { type: 'LANGUAGE', name: $translations.formatTypeCode_LANGUAGE, color: 'yellow' },
    { type: 'ALERT', name: $translations.formatTypeCode_ALERT, color: 'red' }
  ];

  function createGroupedFormatItems(filteredFormats: any[]) {
    const formatsByType = filteredFormats.filter(Boolean).reduce(
      (groups, format) => {
        (groups[format.type] ??= []).push(format);
        return groups;
      },
      {} as Record<string, any[]>
    );

    const createGroupHeader = (type: string, name: string) => ({
      value: `_group_${type}_`,
      name: ` ${name} `,
      disabled: true
    });

    const formatItem = (format: any) => ({
      value: format.id,
      name: `  (${format.key}) ${format.name}`,
      type: format.type
    });

    const sortAlphabetically = (items: any[]) => items.sort((a, b) => a.name.localeCompare(b.name));

    const knownGroupItems = FORMAT_TYPE_GROUPS.map((group) => {
      const formatsInGroup = formatsByType[group.type];
      if (!formatsInGroup?.length) return null;
      const header = createGroupHeader(group.type, group.name);
      const items = sortAlphabetically(formatsInGroup.map(formatItem));
      return [header, ...items];
    })
      .filter(Boolean)
      .flat();

    const knownTypes = new SvelteSet(FORMAT_TYPE_GROUPS.map((g) => g.type));
    const unknownTypes = Object.keys(formatsByType).filter((type) => !knownTypes.has(type));

    const noneGroupItems =
      unknownTypes.length > 0
        ? [
            createGroupHeader('none', $translations.formatTypeCode_NONE),
            ...sortAlphabetically(unknownTypes.flatMap((type) => formatsByType[type]).map((format) => formatItem({ ...format, type: format.type || $translations.formatTypeCode_NONE })))
          ]
        : [];
    return [...knownGroupItems, ...noneGroupItems];
  }

  type BadgeColor =
    | 'green'
    | 'red'
    | 'blue'
    | 'purple'
    | 'yellow'
    | 'gray'
    | 'primary'
    | 'secondary'
    | 'emerald'
    | 'orange'
    | 'teal'
    | 'cyan'
    | 'sky'
    | 'indigo'
    | 'lime'
    | 'amber'
    | 'violet'
    | 'fuchsia'
    | 'pink'
    | 'rose';

  function getBadgeColor(formatId: string, formatLookup: Record<string, any>): BadgeColor {
    const format = formatLookup[formatId];
    if (!format) return 'gray';

    const group = FORMAT_TYPE_GROUPS.find((g) => g.type === format.type);
    return (group?.color ?? 'yellow') as BadgeColor;
  }

  const formatItems = createGroupedFormatItems(filteredFormats.filter((f): f is NonNullable<typeof f> => f !== null));
  const formatIdToFormatType = Object.fromEntries(filteredFormats.filter((f) => f !== null).map((f) => [f.id, f]));

  function handleDelete(event: MouseEvent, meeting: Meeting) {
    event.stopPropagation();
    meetingToDelete = meeting;
    showDeleteModal = true;
  }

  // This hack is required until https://github.com/themesberg/flowbite-svelte/issues/1395 is fixed.
  function disableButtonHack(event: MouseEvent) {
    if (!$isDirty && !saveAsCopy) {
      event.preventDefault();
    }
  }

  // Type guards
  function isGoogleMap(map: any): map is google.maps.Map {
    if (map) {
      return (map as google.maps.Map).setCenter !== undefined;
    } else {
      return false;
    }
  }

  let mapInitialized = $state(false);

  function initializeMap() {
    if (!hasValidCoordinates(latitude, longitude)) {
      latitude = defaultLatLng.lat;
      longitude = defaultLatLng.lng;
      setData('latitude', latitude);
      setData('longitude', longitude);
    }

    if (mapInitialized) {
      // If already initialized, just resize/recenter
      if (map) {
        setTimeout(() => {
          if (map && 'invalidateSize' in map) {
            try {
              map.invalidateSize();
              if ('setView' in map) {
                map.setView([latitude, longitude]);
              } else if (isGoogleMap(map)) {
                (map as google.maps.Map).setCenter({ lat: latitude, lng: longitude });
              }
            } catch (e) {
              console.error('Error resizing map:', e);
            }
          }
        }, 200);
      }
      return;
    }

    if (!mapElement) {
      const mapEl = document.getElementById('locationMap');
      if (!mapEl) {
        return;
      }
      mapElement = mapEl;
    }

    // Create the map only if not already initialized
    if (mapElement && !map) {
      if (settings.googleApiKey) {
        createGoogleMap();
      } else {
        createLeafletMap();
      }
      mapInitialized = true;
    }
  }

  function createLeafletMap() {
    if (!mapElement) {
      return;
    }

    try {
      // Clear any existing map
      if (map && 'remove' in map) {
        map.remove();
        map = null;
      }

      const zoomLevel = Math.min(Number(settings.centerZoom ?? 18), 15);

      const leafletMap = L.map(mapElement, {
        preferCanvas: true,
        renderer: L.canvas(),
        zoom: zoomLevel,
        center: [latitude, longitude],
        attributionControl: true,
        zoomControl: true
      });

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        minZoom: 5,
        attribution: '&copy; OpenStreetMap',
        subdomains: ['a', 'b', 'c']
      }).addTo(leafletMap);

      const naMarkerImage = L.icon({
        iconUrl: 'images/NAMarkerR.png',
        iconSize: [44, 64]
      });

      const leafletMarker = L.marker([latitude, longitude], { icon: naMarkerImage, draggable: true }).addTo(leafletMap);

      leafletMarker.on('dragend', (e) => {
        const pos = e.target.getLatLng();
        latitude = pos.lat;
        longitude = pos.lng;
        setData('longitude', longitude);
        setData('latitude', latitude);
        manualDrag = true;
      });

      map = leafletMap;
      marker = leafletMarker;

      const delays = [100, 300, 600, 1000];
      delays.forEach((delay) => {
        setTimeout(() => {
          if (leafletMap && mapElement && mapElement.offsetParent !== null) {
            try {
              leafletMap.invalidateSize();
            } catch (e) {
              console.error('Error invalidating map size:', e);
            }
          }
        }, delay);
      });
    } catch (e) {
      console.error('Error creating Leaflet map:', e);
    }
  }

  async function createGoogleMap() {
    if (!mapElement) return;

    if (settings.googleApiKeyIsBad) {
      mapError = $translations.googleKeyProblemDescription;
      return;
    }

    try {
      await initGoogleMaps(settings.googleApiKey);
      const [{ Map }] = await Promise.all([importLibrary('maps'), importLibrary('marker')]);
      map = new Map(mapElement, {
        center: { lat: latitude, lng: longitude },
        zoom: Math.min(Number(settings.centerZoom ?? 18), 15),
        disableDefaultUI: false,
        mapTypeId: 'roadmap',
        gestureHandling: 'auto',
        zoomControl: true,
        mapId: 'bmlt'
      });

      await createAdvancedGoogleMarker();
    } catch (e) {
      console.error('Error creating Google Map:', e);
    }
  }

  onMount(() => {
    if (selectedMeeting) {
      initializeMap();
    }

    // Only initialize map if accordion is open
    if ($showMap) {
      setTimeout(initializeMap, 300);
    }

    return () => {
      // Clean up the map when component is destroyed
      if (map) {
        if ('remove' in map) {
          map.remove();
        } else if (marker && isGoogleMap(marker) && 'setMap' in marker) {
          (marker as google.maps.marker.AdvancedMarkerElement).map = null;
        }
        map = null;
        marker = null;
      }
    };
  });

  async function createAdvancedGoogleMarker() {
    if (settings.googleApiKeyIsBad || !map || !isGoogleMap(map)) return;

    try {
      const position = { lat: latitude, lng: longitude };
      const naMarkerImage = document.createElement('img');
      naMarkerImage.src = 'images/NAMarkerR.png';
      naMarkerImage.style.width = '44px';
      naMarkerImage.style.height = '64px';

      const advancedMarker = new google.maps.marker.AdvancedMarkerElement({
        position: position,
        map: map,
        gmpDraggable: true,
        content: naMarkerImage,
        title: 'Meeting location'
      });

      advancedMarker.addListener('dragend', () => {
        if (advancedMarker && 'position' in advancedMarker && advancedMarker.position) {
          const newPosition = advancedMarker.position;
          if (newPosition) {
            longitude = typeof newPosition.lng === 'function' ? newPosition.lng() : newPosition.lng;
            latitude = typeof newPosition.lat === 'function' ? newPosition.lat() : newPosition.lat;
            setData('longitude', longitude);
            setData('latitude', latitude);
            manualDrag = true;
          }
        }
      });
    } catch (e) {
      console.error('Error creating advanced Google Marker:', e);
    }
  }

  async function getChanges(meetingId: number): Promise<void> {
    try {
      changesLoading = true;
      changes = await RootServerApi.getMeetingChanges(meetingId);
      changesLoadedForMeetingId = meetingId;
    } catch (error: any) {
      await RootServerApi.handleErrors(error);
    } finally {
      changesLoading = false;
    }
  }

  function hasBasicErrors(errors: any): boolean {
    return Boolean(
      errors.published ||
      errors.name ||
      errors.timeZone ||
      errors.day ||
      errors.startTime ||
      errors.duration ||
      errors.serviceBodyId ||
      errors.email ||
      errors.worldId ||
      errors.formatIds?.map((f: any) => Object.keys(f).length).find((n: number) => n > 0)
    );
  }

  function hasLocationErrors(errors: any): boolean {
    return Boolean(
      errors.venueType ||
      errors.temporarilyVirtual ||
      errors.longitude ||
      errors.latitude ||
      errors.locationText ||
      errors.locationInfo ||
      errors.locationStreet ||
      errors.locationNeighborhood ||
      errors.locationCitySubsection ||
      errors.locationMunicipality ||
      errors.locationSubProvince ||
      errors.locationProvince ||
      errors.locationPostalCode1 ||
      errors.locationNation ||
      errors.locationPostalCode1 ||
      errors.locationNation ||
      errors.phoneMeetingNumber ||
      errors.virtualMeetingLink ||
      errors.virtualMeetingAdditionalInfo
    );
  }

  function hasOtherErrors(errors: any): boolean {
    return Boolean(
      errors.comments ||
      errors.busLines ||
      errors.trainLines ||
      errors.contactName1 ||
      errors.contactName2 ||
      errors.contactPhone1 ||
      errors.contactPhone2 ||
      errors.contactEmail1 ||
      errors.contactEmail2
    );
  }

  let errorTabs: string[] = $derived((hasBasicErrors($errors) ? [tabs[0]] : []).concat(hasLocationErrors($errors) ? [tabs[1]] : []).concat(hasOtherErrors($errors) ? [tabs[2]] : []));

  $effect(() => {
    const dirty = formIsDirty(initialValues, $data);
    globalIsDirty.set(dirty);
    isDirty.set(dirty);
  });
  $effect(() => {
    setData('formatIds', formatIdsSelected);
  });
</script>

<svelte:head>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.1/dist/leaflet.css" />
  <style>
    #locationMap {
      width: 100% !important;
      height: 300px !important;
      border: 1px solid #ccc;
      border-radius: 5px;
      display: block !important;
      overflow: hidden;
    }
    .leaflet-container {
      width: 100% !important;
      height: 100% !important;
    }
    .leaflet-control-zoom {
      margin-top: 10px !important;
      margin-left: 10px !important;
    }
    .leaflet-control-zoom a {
      width: 30px !important;
      height: 30px !important;
      line-height: 30px !important;
      font-size: 18px !important;
    }
  </style>
</svelte:head>

{#snippet basicTabContent()}
  <div class="grid items-center gap-4 md:grid-cols-3">
    <div class="w-full">
      <Checkbox name="published" bind:checked={isPublishedChecked}>
        {$translations.meetingIsPublishedTitle}
      </Checkbox>
      {#if !isPublishedChecked}
        <Helper class="mt-2" color="red">
          {$translations.meetingUnpublishedNote}
        </Helper>
      {/if}
      {#if $errors.published}
        <Helper class="mt-2" color="red">
          {$errors.published}
        </Helper>
      {/if}
    </div>
    {#if selectedMeeting}
      <div class="flex w-full items-center justify-between md:col-span-2">
        <div class="text-gray-700 dark:text-gray-300">
          <strong>{$translations.meetingId}:</strong>
          {selectedMeeting.id}
        </div>
        <Button
          color="alternative"
          onclick={(e: MouseEvent) => selectedMeeting && handleDelete(e, selectedMeeting)}
          class="text-red-600 dark:text-red-500"
          aria-label={$translations.deleteMeeting + ' ' + (selectedMeeting?.id ?? '')}
        >
          <TrashBinOutline title={{ id: 'deleteMeeting', title: $translations.deleteMeeting }} ariaLabel={$translations.deleteMeeting} />
          <span class="sr-only">{$translations.deleteMeeting}</span>
        </Button>
      </div>
    {/if}
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="name" class="mt-2 mb-2">{$translations.nameTitle}</Label>
      <Input type="text" id="name" name="name" required />
      {#if $errors.name}
        <Helper class="mt-2" color="red">
          {$errors.name}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="timeZone" class="mt-2 mb-2">{$translations.timeZoneTitle}</Label>
      <Select id="timeZone" name="timeZone" bind:value={$data.timeZone} class="rounded-lg dark:bg-gray-600" placeholder={$translations.timeZoneSelectPlaceholder}>
        {#each timeZoneGroups as continent}
          <optgroup label={continent.name}>
            {#each continent.values as timezone}
              <option value={timezone.value}>{timezone.name}</option>
            {/each}
          </optgroup>
        {/each}
      </Select>
      {#if $errors.timeZone}
        <Helper class="mt-2" color="red">
          {$errors.timeZone}
        </Helper>
      {:else if settings.regionBias?.toLowerCase() === 'us'}
        <Helper class="mt-2">
          {$translations.timeZoneHelperText}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-3">
    <div class="w-full">
      <Label for="day" class="mt-2 mb-2">{$translations.dayTitle}</Label>
      <Select id="day" items={weekdayChoices} name="day" bind:value={$data.day} class="rounded-lg dark:bg-gray-600" />
      {#if $errors.day}
        <Helper class="mt-2" color="red">
          {$errors.day}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="startTime" class="mt-2 mb-2">{$translations.startTimeTitle}</Label>
      <Input type="time" id="startTime" name="startTime" />
      {#if $errors.startTime}
        <Helper class="mt-2" color="red">
          {$errors.startTime}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <span class="mt-2 mb-2 block text-sm font-medium text-gray-900 rtl:text-right dark:text-gray-300">{$translations.durationTitle}</span>
      <DurationSelector initialDuration={initialValues.duration} updateDuration={(d: string) => setData('duration', d)} />
      {#if $errors.duration}
        <Helper class="mt-2" color="red">
          {$errors.duration}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="serviceBodyId" class="mt-2 mb-2">{$translations.serviceBodyTitle}</Label>
      {#if serviceBodies.length === 1}
        <Input type="text" value={serviceBodies[0].name} disabled />
        <input type="hidden" id="serviceBodyId" name="serviceBodyId" bind:value={$data.serviceBodyId} />
      {:else}
        <Select id="serviceBodyId" items={serviceBodyIdItems} name="serviceBodyId" bind:value={$data.serviceBodyId} class="rounded-lg dark:bg-gray-600" />
      {/if}
      {#if $errors.serviceBodyId}
        <Helper class="mt-2" color="red">
          {$errors.serviceBodyId}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="w-full">
      <Label for="email" class="mt-2 mb-2">{$translations.emailTitle}</Label>
      <Input type="email" id="email" name="email" />
      {#if $errors.email}
        <Helper class="mt-2" color="red">
          {$errors.email}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="worldId" class="mt-2 mb-2">{$translations.worldIdTitle}</Label>
      <Input type="text" id="worldId" name="worldId" />
      {#if $errors.worldId}
        <Helper class="mt-2" color="red">
          {$errors.worldId}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="md:col-span-2">
    <Label for="formatIds" class="mt-2 mb-2">{$translations.formatsTitle}</Label>
    <MultiSelect id="formatIds" items={formatItems} name="formatIds" class="hide-close-button bg-gray-50 dark:bg-gray-700" bind:value={formatIdsSelected}>
      {#snippet children({ item, clear })}
        <Badge rounded color={getBadgeColor(String(item.value), formatIdToFormatType)} dismissable params={{ duration: 100 }} onclose={clear}>
          {item.name}
        </Badge>
      {/snippet}
    </MultiSelect>
    <!-- For some reason yup fills the errors store with empty objects for this array. The === 'string' ensures only server side errors will display. -->
    {#if $errors.formatIds && typeof $errors.formatIds[0] === 'string'}
      <Helper class="mt-2" color="red">
        {$errors.formatIds}
      </Helper>
    {/if}
  </div>
{/snippet}

{#snippet locationTabContent()}
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="venueType" class="mt-2 mb-2">{$translations.venueTypeTitle}</Label>
      <Select id="venueType" items={venueTypeItems} name="venueType" bind:value={$data.venueType} class="rounded-lg dark:bg-gray-600" />
      {#if $errors.venueType}
        <Helper class="mt-2" color="red">
          {$errors.venueType}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="mt-4 grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <MapAccordion
        title={$translations.locationMapTitle}
        onToggle={(isOpen) => {
          showMap.set(isOpen);

          if (isOpen) {
            // ensure map is initialized or refreshed
            setTimeout(() => {
              if (mapInitialized && map) {
                // If map already exists, just resize and recenter it
                if ('invalidateSize' in map) {
                  map.invalidateSize();
                  if ('setView' in map) {
                    map.setView([latitude, longitude]);
                  }
                } else if (isGoogleMap(map)) {
                  map.setCenter({ lat: latitude, lng: longitude });
                }
              } else {
                initializeMap();
              }
            }, 200);
          }
        }}
      >
        <div id="locationMap" bind:this={mapElement} class="h-[300px]">
          {#if mapError}
            <div class="map-error rounded bg-gray-100 p-4 text-center dark:bg-gray-800">
              <strong>{$translations.googleKeyProblemTitle}</strong><br />
              {mapError}
            </div>
          {/if}
        </div>
      </MapAccordion>
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="w-full">
      <Label for="longitude" class="mt-2 mb-2">{$translations.longitudeTitle}</Label>
      <Input type="text" id="longitude" name="longitude" bind:value={longitude} disabled={settings.autoGeocodingEnabled} required />
      {#if settings.autoGeocodingEnabled}
        <Tooltip placement="top" trigger="hover">{$translations.automaticallyCalculatedOnSave}</Tooltip>
      {/if}
      {#if $errors.longitude}
        <Helper class="mt-2" color="red">
          {$errors.longitude}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="latitude" class="mt-2 mb-2">{$translations.latitudeTitle}</Label>
      <Input type="text" id="latitude" name="latitude" bind:value={latitude} disabled={settings.autoGeocodingEnabled} required />
      {#if settings.autoGeocodingEnabled}
        <Tooltip placement="top" trigger="hover">{$translations.automaticallyCalculatedOnSave}</Tooltip>
      {/if}
      {#if $errors.latitude}
        <Helper class="mt-2" color="red">
          {$errors.latitude}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="locationText" class="mt-2 mb-2">{$translations.locationTextTitle}</Label>
      <Input type="text" id="locationText" name="locationText" />
      {#if $errors.locationText}
        <Helper class="mt-2" color="red">
          {$errors.locationText}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="locationInfo" class="mt-2 mb-2">{$translations.extraInfoTitle}</Label>
      <Input type="text" id="locationInfo" name="locationInfo" />
      {#if $errors.locationInfo}
        <Helper class="mt-2" color="red">
          {$errors.locationInfo}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="locationStreet" class="mt-2 mb-2">{$translations.streetTitle}</Label>
      <Input type="text" id="locationStreet" name="locationStreet" />
      {#if $errors.locationStreet}
        <Helper class="mt-2" color="red">
          {$errors.locationStreet}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="w-full">
      <Label for="locationNeighborhood" class="mt-2 mb-2">{$translations.neighborhoodTitle}</Label>
      <Input type="text" id="locationNeighborhood" name="locationNeighborhood" />
      {#if $errors.locationNeighborhood}
        <Helper class="mt-2" color="red">
          {$errors.locationNeighborhood}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="locationCitySubsection" class="mt-2 mb-2">{$translations.boroughTitle}</Label>
      <Input type="text" id="locationCitySubsection" name="locationCitySubsection" />
      {#if $errors.locationCitySubsection}
        <Helper class="mt-2" color="red">
          {$errors.locationCitySubsection}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="w-full">
      <Label for="locationMunicipality" class="mt-2 mb-2">{$translations.cityTownTitle}</Label>
      <Input type="text" id="locationMunicipality" name="locationMunicipality" />
      {#if $errors.locationMunicipality}
        <Helper class="mt-2" color="red">
          {$errors.locationMunicipality}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="locationSubProvince" class="mt-2 mb-2">{$translations.countySubProvinceTitle}</Label>
      {#if countiesAndSubProvincesChoices.length > 0}
        <Select
          id="locationSubProvince"
          items={countiesAndSubProvincesChoices}
          name="locationSubProvince"
          bind:value={$data.locationSubProvince}
          class="rounded-lg dark:bg-gray-600"
          disabled={settings.countyAutoGeocodingEnabled}
        />
      {:else}
        <Input type="text" id="locationSubProvince" name="locationSubProvince" disabled={settings.countyAutoGeocodingEnabled} />
      {/if}
      {#if settings.countyAutoGeocodingEnabled}
        <Tooltip placement="top" trigger="hover">{$translations.automaticallyCalculatedOnSave}</Tooltip>
      {/if}
      {#if $errors.locationSubProvince}
        <Helper class="mt-2" color="red">
          {$errors.locationSubProvince}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-3">
    <div class="w-full">
      <Label for="locationProvince" class="mt-2 mb-2">{$translations.stateTitle}</Label>
      {#if statesAndProvincesChoices.length > 0}
        <Select id="locationProvince" items={statesAndProvincesChoices} name="locationProvince" bind:value={$data.locationProvince} class="rounded-lg dark:bg-gray-600" />
      {:else}
        <Input type="text" id="locationProvince" name="locationProvince" />
      {/if}
      {#if $errors.locationProvince}
        <Helper class="mt-2" color="red">
          {$errors.locationProvince}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="locationPostalCode1" class="mt-2 mb-2">{$translations.zipCodeTitle}</Label>
      <Input type="text" id="locationPostalCode1" name="locationPostalCode1" disabled={settings.zipAutoGeocodingEnabled} />
      {#if settings.zipAutoGeocodingEnabled}
        <Tooltip placement="top" trigger="hover">{$translations.automaticallyCalculatedOnSave}</Tooltip>
      {/if}
      {#if $errors.locationPostalCode1}
        <Helper class="mt-2" color="red">
          {$errors.locationPostalCode1}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="locationNation" class="mt-2 mb-2">{$translations.nationTitle}</Label>
      <Input type="text" id="locationNation" name="locationNation" />
      {#if $errors.locationNation}
        <Helper class="mt-2" color="red">
          {$errors.locationNation}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="phoneMeetingNumber" class="mt-2 mb-2">{$translations.phoneMeetingTitle}</Label>
      <Input type="text" id="phoneMeetingNumber" name="phoneMeetingNumber" />
      {#if $errors.phoneMeetingNumber}
        <Helper class="mt-2" color="red">
          {$errors.phoneMeetingNumber}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="virtualMeetingLink" class="mt-2 mb-2">{$translations.virtualMeetingTitle}</Label>
      <Input type="text" id="virtualMeetingLink" name="virtualMeetingLink" />
      {#if $errors.virtualMeetingLink}
        <Helper class="mt-2" color="red">
          {$errors.virtualMeetingLink}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="virtualMeetingAdditionalInfo" class="mt-2 mb-2">{$translations.virtualMeetingAdditionalInfoTitle}</Label>
      <Input type="text" id="virtualMeetingAdditionalInfo" name="virtualMeetingAdditionalInfo" />
      {#if $errors.virtualMeetingAdditionalInfo}
        <Helper class="mt-2" color="red">
          {$errors.virtualMeetingAdditionalInfo}
        </Helper>
      {/if}
    </div>
  </div>
{/snippet}

{#snippet otherTabContent()}
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="comments" class="mt-2 mb-2">{$translations.commentsTitle}</Label>
      <Input type="text" id="comments" name="comments" />
      {#if $errors.comments}
        <Helper class="mt-2" color="red">
          {$errors.comments}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="w-full">
      <Label for="busLines" class="mt-2 mb-2">{$translations.busLinesTitle}</Label>
      <Input type="text" id="busLines" name="busLines" />
      {#if $errors.busLines}
        <Helper class="mt-2" color="red">
          {$errors.busLines}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="trainLines" class="mt-2 mb-2">{$translations.trainLinesTitle}</Label>
      <Input type="text" id="trainLines" name="trainLines" />
      {#if $errors.trainLines}
        <Helper class="mt-2" color="red">
          {$errors.trainLines}
        </Helper>
      {/if}
    </div>
  </div>
  {#each settings.customFields as { name, displayName }}
    <div class="grid gap-4 md:grid-cols-2">
      <div class="md:col-span-2">
        <Label for={name} class="mt-2 mb-2">{displayName}</Label>
        <Input type="text" id={name} name={$data.customFields[name]} bind:value={$data.customFields[name]} />
        {#if $errors.customFields?.[name]}
          <Helper class="mt-2" color="red">
            {$errors.customFields[name]}
          </Helper>
        {/if}
      </div>
    </div>
  {/each}
  <div class="mt-6 mb-4 flex items-center gap-2 border-b border-gray-200 pb-2 dark:border-gray-700">
    <Badge color="yellow" class="text-xs">
      <LockOutline class="mr-1 h-3 w-3" />
      {$translations.private}
    </Badge>
    <span class="text-sm text-gray-500 dark:text-gray-400">
      {$translations.fieldVisibilityAuthenticatedOnly}
    </span>
  </div>
  <div class="grid gap-4 md:grid-cols-3">
    <div class="w-full">
      <Label for="contactName1" class="mt-2 mb-2">{$translations.contact1NameTitle}</Label>
      <Input type="text" id="contactName1" name="contactName1" />
      {#if $errors.contactName1}
        <Helper class="mt-2" color="red">
          {$errors.contactName1}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="contactPhone1" class="mt-2 mb-2">{$translations.contact1PhoneTitle}</Label>
      <Input type="text" id="contactPhone1" name="contactPhone1" />
      {#if $errors.contactPhone1}
        <Helper class="mt-2" color="red">
          {$errors.contactPhone1}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="contactEmail1" class="mt-2 mb-2">{$translations.contact1EmailTitle}</Label>
      <Input type="text" id="contactEmail1" name="contactEmail1" />
      {#if $errors.contactEmail1}
        <Helper class="mt-2" color="red">
          {$errors.contactEmail1}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-3">
    <div class="w-full">
      <Label for="contactName2" class="mt-2 mb-2">{$translations.contact2NameTitle}</Label>
      <Input type="text" id="contactName2" name="contactName2" />
      {#if $errors.contactName2}
        <Helper class="mt-2" color="red">
          {$errors.contactName2}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="contactPhone2" class="mt-2 mb-2">{$translations.contact2PhoneTitle}</Label>
      <Input type="text" id="contactPhone2" name="contactPhone2" />
      {#if $errors.contactPhone2}
        <Helper class="mt-2" color="red">
          {$errors.contactPhone2}
        </Helper>
      {/if}
    </div>
    <div class="w-full">
      <Label for="contactEmail2" class="mt-2 mb-2">{$translations.contact2EmailTitle}</Label>
      <Input type="text" id="contactEmail2" name="contactEmail2" />
      {#if $errors.contactEmail2}
        <Helper class="mt-2" color="red">
          {$errors.contactEmail2}
        </Helper>
      {/if}
    </div>
  </div>
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="adminNotes" class="mt-2 mb-2">{$translations.adminNotes}</Label>
      <Textarea id="adminNotes" name="adminNotes" rows={2} class="w-full bg-gray-50 dark:bg-gray-700" />
      {#if $errors.adminNotes}
        <Helper class="mt-2" color="red">
          {$errors.adminNotes}
        </Helper>
      {/if}
    </div>
  </div>
{/snippet}

{#snippet changesTabContent()}
  {#if changesLoading}
    <div class="text-center"><Spinner /></div>
  {:else if changesLoadedForMeetingId && changes.length > 0}
    <div class="space-y-3">
      {#each changes as { dateString, details, userName }}
        <div class="rounded-lg bg-gray-100 p-3 shadow-sm dark:bg-gray-800">
          <div class="mb-0 flex items-center justify-between">
            <h6 class="text-lg font-semibold text-gray-900 dark:text-white">
              {dateString}
              {$translations.by}
              {userName}
            </h6>
          </div>
          {#if details && details.length > 0}
            <ul class="mt-1 space-y-1">
              {#each details as detail}
                <li class="text-sm text-gray-600 dark:text-gray-400">
                  {detail.trim()}
                </li>
              {/each}
            </ul>
          {/if}
        </div>
      {/each}
    </div>
  {:else if changesLoadedForMeetingId && changes.length === 0}
    <div class="py-8 text-center text-gray-500 dark:text-gray-400">{$translations.noChangesFound}</div>
  {/if}
{/snippet}

<form use:form>
  <BasicTabs
    {tabs}
    {errorTabs}
    tabsSnippets={[basicTabContent, locationTabContent, otherTabContent, changesTabContent]}
    onTabChange={(index) => {
      if (selectedMeeting && index === CHANGES_TAB_INDEX && changesLoadedForMeetingId !== selectedMeeting.id) {
        getChanges(selectedMeeting.id);
      }
    }}
  />
  <Hr class="my-8" />
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      {#if geocodingError}
        <Helper class="mt-2 mb-4 pb-2 text-lg" color="red">
          {geocodingError}
        </Helper>
      {/if}
      {#if hasBasicErrors($errors) || hasLocationErrors($errors) || hasOtherErrors($errors)}
        <Helper class="mt-2 mb-4 pb-2 text-lg" color="red">
          {$translations.meetingErrorsSomewhere + ' ' + errorTabs.join(', ')}
        </Helper>
      {/if}
      {#if selectedMeeting}
        <div class="mb-4">
          <Checkbox name="saveAsCopy" checked={saveAsCopy} onchange={(e) => (saveAsCopy = e.currentTarget.checked)}>
            {$translations.saveAsCopyCheckbox}
          </Checkbox>
        </div>
      {/if}
      <Button type="submit" class="w-full" disabled={!$isDirty && !saveAsCopy} onclick={disableButtonHack}>
        {#if selectedMeeting && saveAsCopy}
          {$translations.saveAsNewMeeting}
        {:else if selectedMeeting}
          {$translations.applyChangesTitle}
        {:else}
          {$translations.addMeeting}
        {/if}
      </Button>
    </div>
  </div>
</form>
{#if meetingToDelete}
  <MeetingDeleteModal bind:showDeleteModal {meetingToDelete} {onDeleted} />
{/if}

<style>
  :global(.hide-close-button button[aria-label='Close']) {
    display: none !important;
  }

  /* Target disabled format menu items (headers) */
  :global(#formatIds div[class*='opacity-50']) {
    font-weight: bold !important;
    font-size: 0.925rem !important;
    background-color: rgb(243, 244, 246) !important; /* gray-100 */
    color: rgb(31, 41, 55) !important; /* gray-800 */
  }

  @media (prefers-color-scheme: dark) {
    :global(#formatIds div[class*='opacity-50']) {
      background-color: rgb(17, 24, 39) !important; /* gray-900 */
      color: rgb(209, 213, 219) !important; /* gray-300 */
    }
  }

  .map-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: #e0e0e0;
    color: #666;
    text-align: center;
    padding: 20px;
  }
</style>
