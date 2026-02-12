import type { LocaleObject } from 'yup';

export const ptYupLocale: LocaleObject = {
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
export const ptTranslations = {
  accountSettingsTitle: 'Ajustes da conta',
  accountTitle: 'Conta',
  accountTypeTitle: 'Formato de conta',
  addCountiesOrSubProvincesWhereMeetingsAreHeld: 'Add counties or sub-provinces where meetings are held', // TODO: Translate
  addCountySubProvince: 'Add county/sub-province (e.g., Berkshire, Dukes)',
  addFormat: 'Adicionar formato',
  addMeeting: 'Adicionar reunião',
  addServiceBody: 'Adicionar corpo de serviço',
  addStateProvince: 'Add state/province (e.g., NY, MA)',
  addStatesOrProvincesWhereMeetingsAreHeld: 'Add states or provinces where meetings are held',
  addUser: 'Adicionar usuário',
  administrationTitle: 'administração',
  administratorTitle: 'Administrador',
  adminNotes: 'Admin Notes', // TODO: translate
  adminTitle: 'Administrador',
  advancedSettings: 'Advanced Settings', // TODO: Translate
  anteMeridiem: 'AM',
  applyChangesTitle: 'Aplicar alterações',
  automaticallyCalculatedOnSave: 'Automatically calculated on save',
  boroughTitle: 'Bairro/Subseção da cidade',
  busLinesTitle: 'Linhas de ônibus',
  by: 'por',
  cancel: 'Cancelar',
  changeHistoryDepth: 'Change History Depth', // TODO: Translate
  chooseOption: 'Choose option ...', // TODO: translate
  chooseStartTime: 'Escolher hora de início',
  cityTownTitle: 'Cidade/Município',
  clearFormTitle: 'Limpar formulário',
  close: 'Fechar',
  closeWithoutSaving: 'Fechar sem salvar',
  commentsTitle: 'Comentários',
  confirmDeleteFormat: 'Tem certeza de que deseja apagar este formato?',
  confirmDeleteMeeting: 'Tem certeza de que deseja apagar esta reunião?',
  confirmDeleteServiceBody: 'Tem certeza de que deseja apagar este corpo de serviço?',
  confirmDeleteUser: 'Tem certeza de que deseja apagar este usuário?',
  confirmYesImSure: "Yes, I'm sure.",
  contact1EmailTitle: 'Email do Contato 1',
  contact1NameTitle: 'Nome do Contato 1',
  contact1PhoneTitle: 'Telefone do Contato 1',
  contact2EmailTitle: 'Email do Contato 2',
  contact2NameTitle: 'Nome do Contato 2',
  contact2PhoneTitle: 'Telefone do Contato 2',
  copyToClipboard: 'Copiar para a área de transferência',
  countySubProvinceTitle: 'Condado/Sub-província',
  dashboardTitle: 'Painel',
  day: 'Dia',
  day0: 'Domingo',
  day1: 'Segunda-feira',
  day2: 'Terça-feira',
  day3: 'Quarta-feira',
  day4: 'Quinta-feira',
  day5: 'Sexta-feira',
  day6: 'Sábado',
  dayTitle: 'Dia da semana',
  deactivatedTitle: 'Desativado',
  deactivatedUserTitle: 'Usuário desativado',
  defaultClosedStatus: 'Default Closed Status (for NAWS export)', // TODO: Translate
  defaultLanguage: 'Default Language',
  defaultMeetingDuration: 'Default Meeting Duration',
  defaultSortKey: 'Default Sort Key',
  delete: 'Apagar',
  deleteFormat: 'Apagar formato',
  deleteMeeting: 'Apagar reunião',
  deleteServiceBody: 'Apagar corpo de serviço',
  deleteUser: 'Apagar usuário',
  descriptionTitle: 'Descrição',
  details: 'Detalhes (número da linha ou ID da reunião)',
  distanceUnits: 'Distance Units', // TODO: Translate
  downloading: 'Baixando…',
  downloadLaravelLog: 'Baixando log do Laravel',
  downloadSpreadsheet: 'Download Spreadsheet', // TODO: translate
  downloadTranslationsForCurrentLanguage: 'Baixar traduções para o idioma selecionado no login',
  downloadTranslationsSpreadsheet: 'Baixar planilha de Traduções',
  durationTitle: 'Duração',
  editFormat: 'Editar Formato',
  editUser: 'Editar Usário',
  emailTitle: 'Email',
  enableAutoGeocoding: 'Enable Auto Geocoding', // TODO: Translate
  enableCountyAutoGeocoding: 'Enable County Auto Geocoding', // TODO: Translate
  enableLanguageSelector: 'Enable Language Selector', // TODO: Translate
  enableZipAutoGeocoding: 'Enable ZIP Auto Geocoding', // TODO: Translate
  error: 'Erro',
  errorDownloading: 'Erro de baixamento',
  errors: 'Erros',
  extraInfoTitle: 'Informação extra',
  fieldVisibilityAuthenticatedOnly: 'These fields are only visible to authenticated users', // TODO: translate
  fileProcessedSuccessfully: '✓ Arquivo processado com sucesso',
  filter: 'Filtro',
  formatDeleteConflictError: 'Erro: O formato não pôde ser apagado porque ainda está associado a reuniões.',
  formatId: 'ID do formato de conta',
  formatLangNames: 'Format Language Names (Advanced)', // TODO: Translate
  formatLangNamesHelperText:
    'Specify custom language names for format translations not included in the server. This is an advanced feature—consider requesting official translations instead. Example: ga → Gaelic', // TODO: Translate
  formatsTitle: 'Formatos',
  formatTypeCode_ALERT: 'O formato deve ser especialmente proeminente (Requisito de tempo em recuperação, etc.)',
  formatTypeCode_COMMON_NEEDS_OR_RESTRICTION: 'Necessidades comuns e restrições (Homens (interesse masculino), LGTBQ, Sem Crianças, etc.)',
  formatTypeCode_LANGUAGE: 'Idioma',
  formatTypeCode_LOCATION: 'Código de Localização (Acessível a cadeirantes, Estacionamento limitado, etc.)',
  formatTypeCode_MEETING_FORMAT: 'Formato da Reunião (Palestrante, Estudo de literatura, etc.)',
  formatTypeCode_NONE: 'Nenhum',
  formatTypeCode_OPEN_OR_CLOSED: 'Participação por não-adictos (Aberto, Fechado)',
  formatTypeTitle: 'Tipo de formato',
  formatValidationError: 'Este formato é protegido e não pode ser apagado.',
  generalSettings: 'General Settings', // TODO: Translate
  geocodingFailed: 'Geocoding failed',
  googleGeocodingFailed: 'Google geocoding failed',
  googleKeyProblemDescription: 'The Google Maps API key is invalid or not properly configured. Please update your API key settings.',
  googleKeyProblemTitle: 'Google Maps API Key Problem',
  googleMapsApiKey: 'Google Maps API Key', // TODO: Translate
  helplineTitle: 'Linha de ajuda',
  hideDetails: 'Ocultar detalhes',
  homeTitle: 'Início',
  hoursTitle: 'Horas',
  hybrid: 'Hybrid', // TODO: translate
  idTitle: 'ID',
  includeServiceBodyEmailInSemanticOutput: 'Include Service Body Email in Semantic Output', // TODO: Translate
  inPerson: 'In-Person', // TODO: translate
  invalidUsernameOrPassword: 'Nome de usuário ou senha inválidos.',
  keyAlreadyInUse: 'key already in use for another format',
  keyIsRequired: 'chave é obrigatória',
  keyTitle: 'Chave',
  kilometers: 'Kilometers', // TODO: Translate
  languageSelectTitle: 'Selecionar idioma',
  lastLoginTitle: 'Last Login',
  latitudeTitle: 'Latitude',
  loadFile: 'Carregar arquivo',
  loading: 'carregando ...',
  location: 'Location', // TODO: translate
  locationMapTitle: 'Location Map', // TOFIX: translate
  locationStreetErrorMessage: 'Reuniões híbridas ou presenciais devem ter um endereço de rua.',
  locationTextTitle: 'Texto de localização',
  loginTitle: 'Login',
  loginVerb: 'Fazer login',
  logout: 'Sair',
  longitudeTitle: 'Longitude',
  malformedRows: 'Linhas malformadas',
  manageServerSettings: 'Manage Server Settings', // TODO: translate
  mapCenterLocation: 'Map Center Location', // TODO: Translate
  mapsAndGeocoding: 'Maps & Geocoding', // TODO: Translate
  meeting: 'reunião',
  meetingCountiesSubProvinces: 'Meeting Counties/Sub-Provinces', // TODO: Translate
  meetingErrorsSomewhere: 'Abas com um ou mais erros',
  meetingId: 'Meeting ID', // TODO: translate
  meetingIsPublishedTitle: 'A reunião está publicada.',
  meetingListEditorsTitle: 'Editores da lista de reuniões',
  meetings: 'reuniões',
  meetingSettings: 'Meeting Settings', // TODO: Translate
  meetingsPerPage: 'Reuniões por página',
  meetingStatesProvinces: 'Meeting States/Provinces',
  meetingsTitle: 'Reuniões',
  meetingUnpublishedNote: 'Nota: Despublicar uma reunião indica um fechamento temporário. Se esta reunião foi fechada permanentemente, por favor, apagá-la.',
  miles: 'Miles', // TODO: Translate
  minutesTitle: 'Minutos',
  more: 'more', // TODO: translate
  myBmltServer: 'My BMLT Server', // TODO: Translate
  nameTitle: 'Nome',
  nationTitle: 'Nação',
  nawsFormat_BEG: 'Recém-chegado',
  nawsFormat_BT: 'Texto Básico',
  nawsFormat_CAN: 'Luz de velas',
  nawsFormat_CH: 'Fechada em feriados',
  nawsFormat_CLOSED: 'Fechada',
  nawsFormat_CPT: '12 Conceitos',
  nawsFormat_CW: 'Crianças bem-vindas',
  nawsFormat_DISC: 'Discussão/Participação',
  nawsFormat_GL: 'Gay/Lésbica',
  nawsFormat_GP: 'Princípios Orientadoes',
  nawsFormat_HYBR: 'Virtual e presencial',
  nawsFormat_IP: 'Estudo de Ips',
  nawsFormat_IW: 'Estudo de Funciona - Como e Por quê',
  nawsFormat_JFT: 'Leitura do Só Por Hoje',
  nawsFormat_LANG: 'Idioma alternativo',
  nawsFormat_LC: 'Viver Limpo',
  nawsFormat_LIT: 'Estudo da literatura',
  nawsFormat_M: 'Homens',
  nawsFormat_MED: 'Meditação',
  nawsFormat_NC: 'Sem crianças',
  nawsFormat_NONE: 'Nenhum',
  nawsFormat_NS: 'Não fumante',
  nawsFormat_OPEN: 'Aberta',
  nawsFormat_QA: 'Perguntas e respostas',
  nawsFormat_RA: 'Acesso restrito',
  nawsFormat_SD: 'Palestrante/Discussão',
  nawsFormat_SMOK: 'Fumante',
  nawsFormat_SPAD: 'Um Princípio Espiritual por Dia',
  nawsFormat_SPK: 'Palestrante',
  nawsFormat_STEP: 'Passo',
  nawsFormat_SWG: 'Estudo do livro Guia de Passos',
  nawsFormat_TC: 'Instalação Temporariamente Fechada',
  nawsFormat_TOP: 'Tópico',
  nawsFormat_TRAD: 'Tradição',
  nawsFormat_VAR: 'O formato varia',
  nawsFormat_VM: 'Virtual',
  nawsFormat_W: 'Mulheres',
  nawsFormat_WCHR: 'Acesso total a cadeirantes',
  nawsFormat_Y: 'Jovens',
  nawsFormatTitle: 'Formato Serviços Mundiais de NA',
  neighborhoodTitle: 'Vizinhança',
  noChangesFound: 'No changes found',
  noFormatTranslationsError: 'Pelo menos uma tradução é necessária.',
  noLogsFound: 'Nenhum registro encontrado',
  nominatimGeocodingFailed: 'Nominatim geocoding failed: no results found',
  none: 'Nenhum',
  noServiceBodiesAssigned: 'You are not assigned to any service bodies. Please contact your administrator.', // TODO: translate
  noServiceBodiesTitle: 'Nenhum corpo de serviço encontrado que este usuário possa editar.',
  notedAsDeleted: 'Anotado como apagado',
  notFound: 'Não encontrado',
  noticeMessageDisplayedToUsers: 'Notice message displayed to users', // TODO: Translate
  noTranslationAvailable: 'Nenhuma tradição disponível',
  noUpdateNeeded: 'Nenhuma atualização necessária',
  noUsersTitle: 'Nenhum outro usuário encontrado que este usuário possa editar',
  noWhitespaceInKey: 'Espaço em branco não permitido na chave',
  numberOfMeetingsForAutoSearch: 'Number of Meetings for Auto Search', // TODO: Translate
  numberOfMeetingsForAutoSearchHelperText: 'Number of meetings to use when automatically calculating search radius',
  observerTitle: 'Observador do corpo de serviço',
  occurredAt: 'Ocorrido em',
  ownedByTitle: 'Pertencente a',
  paginationOf: 'de',
  paginationShowing: 'Mostrando',
  parentIdTitle: 'Pai do corpo de serviço',
  passwordTitle: 'Senha',
  phoneMeetingTitle: 'Número de telefone para reunião telefónica',
  postMeridiem: 'PM',
  private: 'Private', // TODO: translate
  processingFile: 'Processando arquivo...',
  published: 'Publicado',
  regionBias: 'Region Bias', // TODO: Translate
  saveAsCopy: 'Salvar como cópia',
  saveAsCopyCheckbox: 'Salvar esta reunião como cópia (Cria uma nova reunião)',
  saveAsNewMeeting: 'Salvar como nova reunião',
  saveSettings: 'Save Settings', // TODO: Translate
  saving: 'Salvando...',
  search: 'Buscar',
  searchByDay: 'Buscar por dia',
  searchByName: 'Buscar por nome',
  searchByServiceBody: 'Buscar por corpo de serviço',
  searchMeetings: 'Buscar reuniões…',
  selectAllDays: 'Selecionar todos os dias',
  selectAllServiceBodies: 'Selecionar todos os corpos de serviço',
  serverAdministratorTitle: 'Administrador principal do servidor',
  serverNotice: 'Server Notice', // TODO: Translate
  serverSettings: 'Server Settings', // TODO: translate
  serverTitle: 'Servidor BMLT',
  serverTitleLabel: 'Server Title', // TODO: Translate
  serviceBodiesNoParent: 'Sem Pai (Nível Superior)',
  serviceBodiesTitle: 'Corpos de serviço',
  serviceBodiesWithEditableMeetings: 'Este usuário pode editar reuniões nos seguintes corpos de serviço:',
  serviceBodyAdminTitle: 'Administrador do corpo de serviço',
  serviceBodyDeleteConflictError: 'Erro: O corpo de serviço não pôde ser apagado porque ainda está associado a reuniões ou é pai de outros corpos de serviço',
  serviceBodyDeleteForceWarning: 'This will permanently delete the service body and all of its meetings. This action cannot be undone.', // TODO: translate
  serviceBodyDeleteWithMeetings: 'This service body has meetings. You must force delete to remove both the service body and its meetings.', // TODO: translate
  serviceBodyForceDelete: 'Force delete (also deletes all meetings)', // TODO: translate
  serviceBodyHasMeetings: 'meetings will be deleted', // TODO: translate
  serviceBodyId: 'Service Body ID', // TODO: translate
  serviceBodyInvalid: 'Por favor, selecione um corpo de serviço válido',
  serviceBodyTitle: 'Corpo de serviço',
  serviceBodyTypeTitle: 'Tipo de corpo de serviço',
  showAllTranslations: 'Mostrar todas as traduções',
  showDetails: 'Mostrar detalhes',
  startTimeTitle: 'Hora de início',
  stateTitle: 'Estado/Provincia',
  streetTitle: 'Rúa',
  summary: 'Resumo',
  supportedFileFormats: 'Formatos compatíveis: Excel (.xlsx) e CSV (.csv)',
  tabsBasic: 'Básico',
  tabsChanges: 'Alterações',
  tabsLocation: 'Localização',
  tabsOther: 'Outro',
  technicalDetails: 'Detalhes técnicos',
  time: 'Hora',
  timeAfternoon: 'Tarde',
  timeEvening: 'Noite',
  timeMorning: 'Manhã',
  timeZoneGeocodeError: 'Incapaz de determinar o fuso horário a partir das coordenadas. Por favor, selecione um fuso horário manualmente',
  timeZoneInvalid: 'Fuso horário inválido',
  timeZoneSelectPlaceholder: 'Escolha a opção (ou deixe em branco para detectar automaticamente a partir da localização)',
  timeZoneTitle: 'Fuso Horário',
  totalRows: 'Total de linhas (excluindo cabeçalho)',
  trainLinesTitle: 'Linhas de metrô',
  unpublished: 'Não publicado',
  unselectAllDays: 'Desmarcar todos os dias',
  unselectAllServiceBodies: 'Desmarcar todos os corpos de serviço',
  updated: 'Atualizado',
  updateWorldCommitteeCodes: 'Atualizar Códigos do Comitê Mundial',
  userDeleteConflictError: 'O usuário não pôde ser apagado porque ainda está associado a pelo menos um corpo de serviço ou é pai de outro usuário.',
  userId: 'User ID', // TODO: translate
  userIsDeactivated: 'Usuário está desativado',
  usernameTitle: 'Nome de usuário',
  usersTitle: 'Usuários',
  userTitle: 'Usuário',
  userTypeTitle: 'Tipo de usuário',
  venueTypeTitle: 'Tipo de local',
  virtual: 'Virtual', // TODO: translate
  virtualMeetingAdditionalInfoTitle: 'Informações adicionais da reunião virtual',
  virtualMeetingTitle: 'Link da reunião virtual',
  websiteUrlTitle: 'URL do site',
  welcome: 'Bem-vindo',
  worldIdTitle: 'Código do comitê mundial',
  youHaveUnsavedChanges: 'Você tem alterações não salvas. Você realmente deseja fechar?',
  yourGoogleMapsApiKey: 'Your Google Maps API Key',
  zipCodeTitle: 'CEP/Código Postal',
  zoomLevel: 'Zoom Level' // TODO: Translate
};
