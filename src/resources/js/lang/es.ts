import type { LocaleObject } from 'yup';

export const esYupLocale: LocaleObject = {
  array: {
    length: '${path} must have ${length} items',
    max: '${path} field must have less than or equal to ${max} items',
    min: '${path} field must have at least ${min} items'
  },
  boolean: {
    isValue: '${path} field must be ${value}'
  },
  date: {
    max: '${path} field must be at earlier than ${max}',
    min: '${path} field must be later than ${min}'
  },
  mixed: {
    default: '${path} is invalid',
    defined: '${path} must be defined',
    notNull: '${path} cannot be null',
    notOneOf: '${path} must not be one of the following values: ${values}',
    notType: '${path} must be a valid type',
    oneOf: '${path} must be one of the following values: ${values}',
    required: '${path} is a required field'
  },
  number: {
    integer: '${path} must be an integer',
    lessThan: '${path} must be less than ${less}',
    max: '${path} must be less than or equal to ${max}',
    min: '${path} must be greater than or equal to ${min}',
    moreThan: '${path} must be greater than ${more}',
    negative: '${path} must be a negative number',
    positive: '${path} must be a positive number'
  },
  object: {
    exact: '${path} object contains unknown properties: ${properties}',
    noUnknown: '${path} field has unspecified keys: ${unknown}'
  },
  string: {
    datetime: '${path} must be a valid ISO date-time',
    datetime_offset: '${path} must be a valid ISO date-time with UTC "Z" timezone',
    datetime_precision: '${path} must be a valid ISO date-time with a sub-second precision of exactly ${precision} digits',
    email: '${path} must be a valid email',
    length: '${path} must be exactly ${length} characters',
    lowercase: '${path} must be a lowercase string',
    matches: '${path} must match the following: "${regex}"',
    max: '${path} must be at most ${max} characters',
    min: '${path} must be at least ${min} characters',
    trim: '${path} must be a trimmed string',
    uppercase: '${path} must be a upper case string',
    url: '${path} must be a valid URL',
    uuid: '${path} must be a valid UUID'
  },
  tuple: {
    notType: '${path} must be a valid tuple type'
  }
};

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
export const esTranslations = {
  accountSettingsTitle: 'Ajustes de la cuenta',
  accountTitle: 'Cuenta',
  accountTypeTitle: 'Tipo de cuenta',
  addCountiesOrSubProvincesWhereMeetingsAreHeld: 'Add counties or sub-provinces where meetings are held', // TODO: Translate
  addCountySubProvince: 'Add county/sub-province (e.g., Berkshire, Dukes)',
  addFormat: 'Añadir formato',
  addMeeting: 'Añadir grupo',
  addServiceBody: 'Añadir organismo de servicio',
  addStateProvince: 'Add state/province (e.g., NY, MA)',
  addStatesOrProvincesWhereMeetingsAreHeld: 'Add states or provinces where meetings are held',
  addUser: 'Añadir usuario',
  administrationTitle: 'Administración',
  administratorTitle: 'Administrador',
  adminNotes: 'Admin Notes', // TODO: translate
  adminTitle: 'Administrador',
  advancedSettings: 'Advanced Settings', // TODO: Translate
  anteMeridiem: 'AM',
  applyChangesTitle: 'Aplicar cambios',
  automaticallyCalculatedOnSave: 'Automatically calculated on save',
  boroughTitle: 'Barrio/subdivisión de la ciudad',
  busLinesTitle: 'Líneas de autobuses',
  by: 'por',
  cancel: 'Cancelar',
  changeHistoryDepth: 'Change History Depth', // TODO: Translate
  chooseStartTime: 'Escoger hora de comienzo',
  cityTownTitle: 'Ciudad/Pueblo',
  clearFormTitle: 'Borrar formulario',
  close: 'Cerrar',
  closeWithoutSaving: 'Cerrar sin guardar',
  commentsTitle: 'Comentarios',
  confirmDeleteFormat: '¿Está Ud. seguro que quiere borrar este formato?',
  confirmDeleteMeeting: '¿Está Ud. seguro que quiere borrar este grupo?',
  confirmDeleteServiceBody: '¿Está Ud. seguro que quiere borrar este organismo de servicio?',
  confirmDeleteUser: '¿Está Ud. seguro que quiere borrar este usuario?',
  confirmYesImSure: "Yes, I'm sure.",
  contact1EmailTitle: 'Contacto 1 Correo',
  contact1NameTitle: 'Contacto 1 Nombre',
  contact1PhoneTitle: 'Contacto 1 Teléfono',
  contact2EmailTitle: 'Contacto 2 Correo',
  contact2NameTitle: 'Contacto 2 Nombre',
  contact2PhoneTitle: 'Contacto 2 Teléfono',
  copyToClipboard: 'Copiar al portapapeles',
  countySubProvinceTitle: 'Condado/subprovincia',
  dashboardTitle: 'Pantalla principal',
  day: 'Día',
  day0: 'Domingo',
  day1: 'Lunes',
  day2: 'Martes',
  day3: 'Miércoles',
  day4: 'Jueves',
  day5: 'Viernes',
  day6: 'Sábado',
  dayTitle: 'Día de la semana',
  deactivatedTitle: 'Desactivado',
  deactivatedUserTitle: 'Usuario desactivado',
  defaultClosedStatus: 'Default Closed Status (for NAWS export)', // TODO: Translate
  defaultLanguage: 'Default Language',
  defaultMeetingDuration: 'Default Meeting Duration',
  defaultSortKey: 'Default Sort Key',
  delete: 'Borrar',
  deleteFormat: 'Borrar formato',
  deleteMeeting: 'Borrar grupo',
  deleteServiceBody: 'Borrar organismo de servicio',
  deleteUser: 'Borrar usuario',
  descriptionTitle: 'Descripción',
  details: 'Detalles (fila o número de identificación del grupo)',
  distanceUnits: 'Distance Units', // TODO: Translate
  downloading: 'Descargando…',
  downloadLaravelLog: 'Descargar el registro de Laravel',
  downloadSpreadsheet: 'Download Spreadsheet', // TODO: translate
  downloadTranslationsForCurrentLanguage: 'Descargar traducciones para el idioma seleccionado al iniciar la sesión',
  downloadTranslationsSpreadsheet: 'Descargar hoja de cálculo con traducciones',
  durationTitle: 'Duración',
  editFormat: 'Editar formato',
  editUser: 'Editar usuario',
  emailTitle: 'Correo',
  enableAutoGeocoding: 'Enable Auto Geocoding', // TODO: Translate
  enableCountyAutoGeocoding: 'Enable County Auto Geocoding', // TODO: Translate
  enableLanguageSelector: 'Enable Language Selector', // TODO: Translate
  enableZipAutoGeocoding: 'Enable ZIP Auto Geocoding', // TODO: Translate
  error: 'Error',
  errorDownloading: 'Error al descargar',
  errors: 'Errores',
  extraInfoTitle: 'Información extra',
  fieldVisibilityAuthenticatedOnly: 'These fields are only visible to authenticated users', // TODO: translate
  fileProcessedSuccessfully: '✓ Archivo procesado correctamente',
  filter: 'Filtro',
  formatDeleteConflictError: 'Error: El formato no pudo borrarse porque está asociado con algún grupo',
  formatId: 'Número de identificación del formato',
  formatLangNames: 'Format Language Names (Advanced)', // TODO: Translate
  formatLangNamesHelperText:
    'Specify custom language names for format translations not included in the server. This is an advanced feature—consider requesting official translations instead. Example: ga → Gaelic', // TODO: Translate
  formatsTitle: 'Formatos',
  formatTypeCode_ALERT: 'El formato debe ser especialmente destacado (requisito de tiempo en recuperación, etc.).',
  formatTypeCode_COMMON_NEEDS_OR_RESTRICTION: 'Necesidades y restricciones comunes (reuniones de hombres, LGTBQ, prohibición de niños, etc.)',
  formatTypeCode_LANGUAGE: 'Idioma',
  formatTypeCode_LOCATION: 'Código de ubicación (accesible para sillas de ruedas, estacionamiento limitado, etc.)',
  formatTypeCode_MEETING_FORMAT: 'Formato de la reunión (orador, estudio de literatura, etc.)',
  formatTypeCode_NONE: 'Ninguno',
  formatTypeCode_OPEN_OR_CLOSED: 'Asistencia de los no-adictos (Grupo abierto, cerrado)',
  formatTypeTitle: 'Tipo de formato',
  formatValidationError: 'Error: Este formato está protegido y no puede borrarse.',
  generalSettings: 'General Settings', // TODO: Translate
  geocodingFailed: 'Geocoding failed',
  googleGeocodingFailed: 'Google geocoding failed',
  googleKeyProblemDescription: 'The Google Maps API key is invalid or not properly configured. Please update your API key settings.',
  googleKeyProblemTitle: 'Google Maps API Key Problem',
  googleMapsApiKey: 'Google Maps API Key', // TODO: Translate
  helplineTitle: 'Línea de ayuda',
  hideDetails: 'Ocultar detalles',
  homeTitle: 'Hogar',
  hoursTitle: 'Horas',
  hybrid: 'Hybrid', // TODO: translate
  idTitle: 'Identificación',
  includeServiceBodyEmailInSemanticOutput: 'Include Service Body Email in Semantic Output', // TODO: Translate
  inPerson: 'In-Person', // TODO: translate
  invalidUsernameOrPassword: 'Nombre de usuario o contraseña inválidos',
  keyAlreadyInUse: 'key already in use for another format',
  keyIsRequired: 'Coloque una clave',
  keyTitle: 'Clave',
  kilometers: 'Kilometers', // TODO: Translate
  languageSelectTitle: 'Escoger idioma',
  lastLoginTitle: 'Last Login',
  latitudeTitle: 'Latitud',
  loadFile: 'Cargar archivo',
  loading: 'cargando…',
  location: 'Location', // TODO: translate
  locationMapTitle: 'Location Map', // TOFIX: translate
  locationStreetErrorMessage: 'Para los gupos híbridos o presenciales, hace falta una dirección',
  locationTextTitle: 'Texto de ubicación',
  loginTitle: 'Login',
  loginVerb: 'Iniciar sesión',
  logout: 'Cerrar sesión',
  longitudeTitle: 'Longitud',
  malformedRows: 'Filas malformadas',
  manageServerSettings: 'Manage Server Settings', // TODO: translate
  mapCenterLocation: 'Map Center Location', // TODO: Translate
  mapsAndGeocoding: 'Maps & Geocoding', // TODO: Translate
  meeting: 'grupo',
  meetingCountiesSubProvinces: 'Meeting Counties/Sub-Provinces', // TODO: Translate
  meetingErrorsSomewhere: 'Pestañas con uno o más errores:',
  meetingId: 'Meeting ID', // TODO: translate
  meetingIsPublishedTitle: 'El grupo está publicado',
  meetingListEditorsTitle: 'Editores de la lista de grupos',
  meetings: 'grupos',
  meetingSettings: 'Meeting Settings', // TODO: Translate
  meetingsPerPage: 'Grupos por página',
  meetingStatesProvinces: 'Meeting States/Provinces',
  meetingsTitle: 'Grupos',
  meetingUnpublishedNote: 'Nota: Cancelar la publicación de este grupo indica un cierre temporal. Si este grupo se ha cerrado de forma permanente, elimínela.',
  miles: 'Miles', // TODO: Translate
  minutesTitle: 'Minutos',
  more: 'more', // TODO: translate
  myBmltServer: 'My BMLT Server', // TODO: Translate
  nameTitle: 'Nombre',
  nationTitle: 'Nación',
  nawsFormat_BEG: 'Recién llegado',
  nawsFormat_BT: 'Texto básico',
  nawsFormat_CAN: 'Reunión a luz de velas',
  nawsFormat_CH: 'Cerrado durante los días festivos',
  nawsFormat_CLOSED: 'Cerrado',
  nawsFormat_CPT: '12 Conceptos',
  nawsFormat_CW: 'Los niños son bienvenidos',
  nawsFormat_DISC: 'Discusión/participación',
  nawsFormat_GL: 'Gay/Lesbiano',
  nawsFormat_GP: 'Los principios que nos guían',
  nawsFormat_HYBR: 'Virtual y presencial',
  nawsFormat_IP: 'Estudio de los folletos informativos',
  nawsFormat_IW: 'Estudio de Funciona: cómo y por qué',
  nawsFormat_JFT: 'Estudio del Sólo por hoy',
  nawsFormat_LANG: 'Idioma alternativo',
  nawsFormat_LC: 'Vivir limpios',
  nawsFormat_LIT: 'Estudio de literatura',
  nawsFormat_M: 'Hombres',
  nawsFormat_MED: 'Meditación',
  nawsFormat_NC: 'No se admite a niños',
  nawsFormat_NONE: 'Ninguno',
  nawsFormat_NS: 'No fumar',
  nawsFormat_OPEN: 'Abierto',
  nawsFormat_QA: 'Preguntas y respuestas',
  nawsFormat_RA: 'Acceso restringido',
  nawsFormat_SD: 'Orador/discusión',
  nawsFormat_SMOK: 'Fumar',
  nawsFormat_SPAD: 'Un principio espiritual al día',
  nawsFormat_SPK: 'Orador',
  nawsFormat_STEP: 'Paso',
  nawsFormat_SWG: 'Estudio de guía para trabajar los pasos',
  nawsFormat_TC: 'Instalación temporalmente cerrada',
  nawsFormat_TOP: 'Tema',
  nawsFormat_TRAD: 'Tradición',
  nawsFormat_VAR: 'El formato puede variar',
  nawsFormat_VM: 'Virtual',
  nawsFormat_W: 'Mujeres',
  nawsFormat_WCHR: 'Accesible para sillas de ruedas',
  nawsFormat_Y: 'Jóvenes',
  nawsFormatTitle: 'Formato Servicios Mundiales de NA',
  neighborhoodTitle: 'Barrio/vecindario',
  noChangesFound: 'No changes found',
  noFormatTranslationsError: 'Se requiere por lo menos una traducción',
  noLogsFound: 'Los registros no se encuentran',
  nominatimGeocodingFailed: 'Nominatim geocoding failed: no results found',
  none: 'Ninguno',
  noServiceBodiesAssigned: 'You are not assigned to any service bodies. Please contact your administrator.', // TODO: translate
  noServiceBodiesTitle: 'No se encuentran organismos de servicio que el usuario pueda editar',
  notedAsDeleted: 'Anotado como borrado',
  notFound: 'No encontrado',
  noticeMessageDisplayedToUsers: 'Notice message displayed to users', // TODO: Translate
  noTranslationAvailable: 'No hay traducción disponible',
  noUpdateNeeded: 'No se requiere actualización',
  noUsersTitle: 'No se encuentra a otros usuarios que el presente usuario pueda editar',
  noWhitespaceInKey: 'No se permiten espacios en blanco en la clave',
  numberOfMeetingsForAutoSearch: 'Number of Meetings for Auto Search', // TODO: Translate
  numberOfMeetingsForAutoSearchHelperText: 'Number of meetings to use when automatically calculating search radius',
  observerTitle: 'Observador del organismo de servicio',
  occurredAt: 'Ocurrió en',
  ownedByTitle: 'Propiedad de',
  paginationOf: 'de',
  paginationShowing: 'Mostrando',
  parentIdTitle: 'Padre del organismo de servicio',
  passwordTitle: 'Contraseña',
  phoneMeetingTitle: 'Número de teléfono para la reunión telefónica',
  postMeridiem: 'PM',
  private: 'Private', // TODO: translate
  processingFile: 'Procesando archivo…',
  published: 'Publicado',
  regionBias: 'Region Bias', // TODO: Translate
  saveAsCopy: 'Guardar como copia',
  saveAsCopyCheckbox: 'Guardar esta reunión como copia (crea una nueva reunión)',
  saveAsNewMeeting: 'Guardar como nueva reunión',
  saveSettings: 'Save Settings', // TODO: Translate
  saving: 'Guardando...',
  search: 'Buscar',
  searchByDay: 'Buscar por día',
  searchByName: 'Buscar por nombre',
  searchByServiceBody: 'Buscar por organismo de servicio',
  searchMeetings: 'Buscar reuniones…',
  selectAllDays: 'Seleccionar todos los días',
  selectAllServiceBodies: 'Seleccionar todas las áreas de servicio',
  serverAdministratorTitle: 'Administrador principal del servidor',
  serverNotice: 'Server Notice', // TODO: Translate
  serverSettings: 'Server Settings', // TODO: translate
  serverTitle: 'Servidor de BMLT',
  serverTitleLabel: 'Server Title', // TODO: Translate
  serviceBodiesNoParent: 'Sin padre (nivel superior)',
  serviceBodiesTitle: 'Áreas de servicio',
  serviceBodiesWithEditableMeetings: 'Este usuario puede editar reuniones en los siguientes organismos de servicio',
  serviceBodyAdminTitle: 'Administrador del organismo de servicio',
  serviceBodyDeleteConflictError: 'Error: No se pudo borrar el organismo de servicio porque sigue asociado a otros grupos o es padre de otros organismos de servicio',
  serviceBodyDeleteForceWarning: 'This will permanently delete the service body and all of its meetings. This action cannot be undone.', // TODO: translate
  serviceBodyDeleteWithMeetings: 'This service body has meetings. You must force delete to remove both the service body and its meetings.', // TODO: translate
  serviceBodyForceDelete: 'Force delete (also deletes all meetings)', // TODO: translate
  serviceBodyHasMeetings: 'meetings will be deleted', // TODO: translate
  serviceBodyId: 'Service Body ID', // TODO: translate
  serviceBodyInvalid: 'Seleccione un organismo de servicio válido',
  serviceBodyTitle: 'Organismo de servicio',
  serviceBodyTypeTitle: 'Tipo de organismo de servicio',
  showAllTranslations: 'Mostrar todas las traducciones',
  showDetails: 'Mostrar detalles',
  startTimeTitle: 'Hora de comienzo',
  stateTitle: 'Estado/provincia',
  streetTitle: 'Calle',
  summary: 'Resumen',
  supportedFileFormats: 'Formatos compatibles: Excel (.xlsx) and CSV (.csv)',
  tabsBasic: 'Básico',
  tabsChanges: 'Cambios',
  tabsLocation: 'Ubicación',
  tabsOther: 'Otro',
  technicalDetails: 'Detalles técnicos',
  time: 'Hora',
  timeAfternoon: 'Por la tarde',
  timeEvening: 'Por la noche',
  timeMorning: 'Por la mañana',
  timeZoneGeocodeError: 'No se pudo determinar la zona horaria a partir de las coordenadas. Seleccione una zona horaria manualmente.',
  timeZoneInvalid: 'Zona horaria inválida',
  timeZoneSelectPlaceholder: 'Seleccione una opción (o déjelo en blanco para que se detecte automáticamente desde la ubicación).',
  timeZoneTitle: 'Zona horaria',
  totalRows: 'Total de filas (excluyendo el encabezado)',
  trainLinesTitle: 'Líneas de tren/metro',
  unpublished: 'No publicado',
  unselectAllDays: 'Deseleccionar todos los días',
  unselectAllServiceBodies: 'Deseleccionar todos los organismos de servicio',
  updated: 'Actualizado',
  updateWorldCommitteeCodes: 'Actualizar los códigos del Comité Mundial',
  userDeleteConflictError: 'Error: No se pudo borrar al usuario porque sigue asociado con al menos un organismo de servicio o es padre de otro usuario.',
  userId: 'User ID', // TODO: translate
  userIsDeactivated: 'El usuario está desactivado.',
  usernameTitle: 'Nombre de usuario',
  usersTitle: 'Usuarios',
  userTitle: 'Usuario',
  userTypeTitle: 'Tipo de usuario',
  venueTypeTitle: 'Tipo de recinto',
  virtual: 'Virtual', // TODO: translate
  virtualMeetingAdditionalInfoTitle: 'Grupo virtual información adicional',
  virtualMeetingTitle: 'Enlace reunión virtual',
  websiteUrlTitle: 'Dirección URL del sitio web',
  welcome: 'Bienvenido/a',
  worldIdTitle: 'Código del comité mundial',
  youHaveUnsavedChanges: 'Tiene cambios no guardados. ¿De verdad quiere cerrar?',
  yourGoogleMapsApiKey: 'Your Google Maps API Key',
  zipCodeTitle: 'Código postal',
  zoomLevel: 'Zoom Level' // TODO: Translate
};
