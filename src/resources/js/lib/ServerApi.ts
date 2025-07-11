import {
  Configuration,
  ResponseError,
  RootServerApi,
  type AuthenticationError,
  type AuthorizationError,
  type ConflictError,
  type Format,
  type FormatCreate,
  type FormatPartialUpdate,
  type FormatUpdate,
  type GetMeetingsRequest,
  type Meeting,
  type MeetingChangeResource,
  type MeetingCreate,
  type MeetingPartialUpdate,
  type MeetingUpdate,
  type NotFoundError,
  type ServiceBody,
  type ServiceBodyCreate,
  type ServiceBodyPartialUpdate,
  type ServiceBodyUpdate,
  type Token,
  type User,
  type UserCreate,
  type UserPartialUpdate,
  type UserUpdate,
  type ValidationError
} from 'bmlt-server-client';

class ApiClient extends RootServerApi {
  private authorizationHeader: string | null = null;
  private _token: Token | null = null;

  constructor(token: Token | null = null) {
    super();
    this.token = token;
    this.configuration = new Configuration({
      basePath: settings.apiBaseUrl,
      accessToken: () => this.authorizationHeader ?? ''
    });
  }

  set token(token: Token | null) {
    this._token = token;
    this.accessToken = token?.accessToken ?? null;
  }

  get token(): Token | null {
    return this._token;
  }

  set accessToken(accessToken: string | null) {
    if (!accessToken) {
      this.authorizationHeader = '';
    } else {
      this.authorizationHeader = `Bearer ${accessToken}`;
    }
  }

  get isLoggedIn(): boolean {
    return Boolean(this.authorizationHeader);
  }

  get isTokenExpired(): boolean {
    if (!this._token || !this._token.expiresAt) {
      return true;
    }
    const currentTime = Math.floor(Date.now() / 1000);
    return this._token.expiresAt < currentTime;
  }

  async getLaravelLog(): Promise<Blob> {
    return this.getLaravelLogRaw().then((response) => response.value());
  }
}

type AuthenticationErrorHandler = (error: AuthenticationError) => void;
type AuthorizationErrorHandler = (error: AuthorizationError) => void;
type NotFoundErrorHandler = (error: NotFoundError) => void;
type ConflictErrorHandler = (error: ConflictError) => void;
type ValidationErrorHandler = (error: ValidationError) => void;
type ServerErrorHandler = (error: ResponseError) => void;
type NetworkErrorHandler = () => void;
type GenericErrorHandler = (error: Error) => void;
type ErrorHandlers = {
  handleAuthenticationError?: AuthenticationErrorHandler;
  handleAuthorizationError?: AuthorizationErrorHandler;
  handleNotFoundError?: NotFoundErrorHandler;
  handleConflictError?: ConflictErrorHandler;
  handleValidationError?: ValidationErrorHandler;
  handleServerError?: ServerErrorHandler;
  handleNetworkError?: NetworkErrorHandler;
  handleError?: GenericErrorHandler;
};

class ApiClientWrapper {
  static instance = new ApiClientWrapper();

  public api: ApiClient;
  private defaultAuthenticationErrorHandler: AuthenticationErrorHandler | null = null;
  private defaultAuthorizationErrorHandler: AuthorizationErrorHandler | null = null;
  private defaultNotFoundErrorHandler: NotFoundErrorHandler | null = null;
  private defaultConflictErrorHandler: ConflictErrorHandler | null = null;
  private defaultValidationErrorHandler: ValidationErrorHandler | null = null;
  private defaultServerErrorHandler: ServerErrorHandler | null = null;
  private defaultNetworkErrorHandler: NetworkErrorHandler | null = null;
  private defaultErrorHandler: GenericErrorHandler | null = null;

  constructor(token: Token | null = null) {
    if (!token) {
      const tokenJson = localStorage.getItem('token');
      if (tokenJson) {
        token = JSON.parse(tokenJson) as Token;
      }
    }

    this.api = new ApiClient(token);
  }

  initializeDefaultErrorHandlers(defaultErrorHandlers: ErrorHandlers): void {
    this.defaultAuthenticationErrorHandler = defaultErrorHandlers.handleAuthenticationError ?? this.defaultAuthenticationErrorHandler;
    this.defaultAuthorizationErrorHandler = defaultErrorHandlers.handleAuthorizationError ?? this.defaultAuthorizationErrorHandler;
    this.defaultNotFoundErrorHandler = defaultErrorHandlers.handleNotFoundError ?? this.defaultNotFoundErrorHandler;
    this.defaultValidationErrorHandler = defaultErrorHandlers.handleValidationError ?? this.defaultValidationErrorHandler;
    this.defaultNetworkErrorHandler = defaultErrorHandlers.handleNetworkError ?? this.defaultNetworkErrorHandler;
    this.defaultServerErrorHandler = defaultErrorHandlers.handleServerError ?? this.defaultServerErrorHandler;
    this.defaultErrorHandler = defaultErrorHandlers.handleError ?? this.defaultErrorHandler;
  }

  set token(token: Token | null) {
    this.api.token = token;
  }

  get token(): Token | null {
    return this.api.token;
  }

  get isLoggedIn(): boolean {
    return this.api.isLoggedIn;
  }

  get isTokenExpired(): boolean {
    return this.api.isTokenExpired;
  }

  async login(username: string, password: string): Promise<Token> {
    const tokenCredentials = { username, password };
    const authTokenRequest = { tokenCredentials };
    return this.api.authToken(authTokenRequest);
  }

  async refreshToken(): Promise<Token> {
    return this.api.authRefresh();
  }

  async logout(): Promise<void> {
    return this.api.authLogout();
  }

  async getMeetings({ meetingIds = '', days = '', serviceBodyIds = '', searchString = '' }: GetMeetingsRequest = {}): Promise<Meeting[]> {
    const params = {
      ...(searchString && { searchString }),
      ...(days && { days }),
      ...(meetingIds && { meetingIds }),
      ...(serviceBodyIds && { serviceBodyIds })
    };
    return this.api.getMeetings(params);
  }

  async getMeeting(id: number): Promise<Meeting> {
    const params = { meetingId: id };
    return this.api.getMeeting(params);
  }

  async getMeetingChanges(id: number): Promise<Array<MeetingChangeResource>> {
    const params = { meetingId: id };
    return this.api.getMeetingChanges(params);
  }

  async createMeeting(meeting: MeetingCreate): Promise<Meeting> {
    const params = { meetingCreate: meeting };
    return this.api.createMeeting(params);
  }

  async updateMeeting(id: number, meeting: MeetingUpdate): Promise<void> {
    const params = { meetingId: id, meetingUpdate: meeting };
    return this.api.updateMeeting(params);
  }

  async partialUpdateMeeting(id: number, meeting: MeetingPartialUpdate, skipVenueTypeLocationValidation?: boolean): Promise<void> {
    const params = {
      meetingId: id,
      meetingPartialUpdate: meeting,
      ...(skipVenueTypeLocationValidation && { skipVenueTypeLocationValidation })
    };
    return this.api.patchMeeting(params);
  }

  async deleteMeeting(id: number): Promise<void> {
    const params = { meetingId: id };
    return this.api.deleteMeeting(params);
  }

  async getServiceBodies(): Promise<ServiceBody[]> {
    return this.api.getServiceBodies();
  }

  async getServiceBody(id: number): Promise<ServiceBody> {
    const params = { serviceBodyId: id };
    return this.api.getServiceBody(params);
  }

  async createServiceBody(serviceBody: ServiceBodyCreate): Promise<ServiceBody> {
    const params = { serviceBodyCreate: serviceBody };
    return this.api.createServiceBody(params);
  }

  async updateServiceBody(id: number, serviceBody: ServiceBodyUpdate): Promise<void> {
    const params = { serviceBodyId: id, serviceBodyUpdate: serviceBody };
    return this.api.updateServiceBody(params);
  }

  async partialUpdateServiceBody(id: number, serviceBody: ServiceBodyPartialUpdate): Promise<void> {
    const params = { serviceBodyId: id, serviceBodyPartialUpdate: serviceBody };
    return this.api.patchServiceBody(params);
  }

  async deleteServiceBody(id: number): Promise<void> {
    const params = { serviceBodyId: id };
    return this.api.deleteServiceBody(params);
  }

  async getFormats(): Promise<Format[]> {
    return this.api.getFormats();
  }

  async getFormat(id: number): Promise<Format> {
    const params = { formatId: id };
    return this.api.getFormat(params);
  }

  async createFormat(format: FormatCreate): Promise<Format> {
    const params = { formatCreate: format };
    return this.api.createFormat(params);
  }

  async updateFormat(id: number, format: FormatUpdate): Promise<void> {
    const params = { formatId: id, formatUpdate: format };
    return this.api.updateFormat(params);
  }

  async partialUpdateFormat(id: number, format: FormatPartialUpdate): Promise<void> {
    const params = { formatId: id, formatPartialUpdate: format };
    return this.api.patchFormat(params);
  }

  async deleteFormat(id: number): Promise<void> {
    const params = { formatId: id };
    return this.api.deleteFormat(params);
  }

  async getUsers(): Promise<User[]> {
    return this.api.getUsers();
  }

  async getUser(id: number): Promise<User> {
    const params = { userId: id };
    return this.api.getUser(params);
  }

  async createUser(user: UserCreate): Promise<User> {
    const params = { userCreate: user };
    return this.api.createUser(params);
  }

  async updateUser(id: number, user: UserUpdate): Promise<void> {
    const params = { userId: id, userUpdate: user };
    return this.api.updateUser(params);
  }

  async partialUpdateUser(id: number, user: UserPartialUpdate): Promise<void> {
    const params = { userId: id, userPartialUpdate: user };
    return this.api.partialUpdateUser(params);
  }

  async deleteUser(id: number): Promise<void> {
    const params = { userId: id };
    return this.api.deleteUser(params);
  }

  async getLaravelLog(): Promise<Blob> {
    return this.api.getLaravelLog();
  }

  async handleErrors(error: Error, overrideErrorHandlers?: ErrorHandlers): Promise<void> {
    const handleAuthenticationError = overrideErrorHandlers?.handleAuthenticationError ?? this.defaultAuthenticationErrorHandler;
    const handleAuthorizationError = overrideErrorHandlers?.handleAuthorizationError ?? this.defaultAuthorizationErrorHandler;
    const handleNotFoundError = overrideErrorHandlers?.handleNotFoundError ?? this.defaultNotFoundErrorHandler;
    const handleConflictError = overrideErrorHandlers?.handleConflictError ?? this.defaultConflictErrorHandler;
    const handleValidationError = overrideErrorHandlers?.handleValidationError ?? this.defaultValidationErrorHandler;
    const handleNetworkError = overrideErrorHandlers?.handleNetworkError ?? this.defaultNetworkErrorHandler;
    const handleServerError = overrideErrorHandlers?.handleServerError ?? this.defaultServerErrorHandler;
    const handleError = overrideErrorHandlers?.handleError ?? this.defaultErrorHandler;

    // handle network errors first
    if (error.message === 'Failed to fetch' || error.name === 'FetchError') {
      if (handleNetworkError) {
        return handleNetworkError();
      }

      if (handleError) {
        return handleError(error);
      }

      return console.log('TODO show error dialog', error.message);
    }

    // handle api errors
    const responseError = error as ResponseError;
    const body = await responseError.response.json();

    if (handleAuthenticationError && responseError.response.status === 401) {
      // message
      return handleAuthenticationError(body as AuthenticationError);
    }

    if (handleAuthorizationError && responseError.response.status === 403) {
      // message
      return handleAuthorizationError(body as AuthorizationError);
    }

    if (handleNotFoundError && responseError.response.status === 404) {
      return handleNotFoundError(body as NotFoundError);
    }

    if (handleConflictError && responseError.response.status === 409) {
      return handleConflictError(body as ConflictError);
    }

    if (handleValidationError && responseError.response.status === 422) {
      // message, errors
      // console.log('body', body);
      return handleValidationError(body as ValidationError);
    }

    if (handleServerError && responseError.response.status > 499) {
      return handleServerError(body);
    }

    if (handleError) {
      return handleError(body);
    }

    return console.log('TODO unhandled error, show error dialog', body);
  }
}

export default ApiClientWrapper.instance;
