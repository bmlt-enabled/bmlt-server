import { ResponseContext, RequestContext, HttpFile, HttpInfo } from '../http/http';
import { Configuration, PromiseConfigurationOptions, wrapOptions } from '../configuration'
import { PromiseMiddleware, Middleware, PromiseMiddlewareWrapper } from '../middleware';

import { AuthenticationError } from '../models/AuthenticationError';
import { AuthorizationError } from '../models/AuthorizationError';
import { ConflictError } from '../models/ConflictError';
import { ErrorTest } from '../models/ErrorTest';
import { Format } from '../models/Format';
import { FormatBase } from '../models/FormatBase';
import { FormatCreate } from '../models/FormatCreate';
import { FormatPartialUpdate } from '../models/FormatPartialUpdate';
import { FormatTranslation } from '../models/FormatTranslation';
import { FormatUpdate } from '../models/FormatUpdate';
import { Meeting } from '../models/Meeting';
import { MeetingBase } from '../models/MeetingBase';
import { MeetingChangeResource } from '../models/MeetingChangeResource';
import { MeetingCreate } from '../models/MeetingCreate';
import { MeetingPartialUpdate } from '../models/MeetingPartialUpdate';
import { MeetingUpdate } from '../models/MeetingUpdate';
import { NotFoundError } from '../models/NotFoundError';
import { RootServer } from '../models/RootServer';
import { RootServerBase } from '../models/RootServerBase';
import { RootServerBaseStatistics } from '../models/RootServerBaseStatistics';
import { RootServerBaseStatisticsMeetings } from '../models/RootServerBaseStatisticsMeetings';
import { RootServerBaseStatisticsServiceBodies } from '../models/RootServerBaseStatisticsServiceBodies';
import { ServerError } from '../models/ServerError';
import { ServiceBody } from '../models/ServiceBody';
import { ServiceBodyBase } from '../models/ServiceBodyBase';
import { ServiceBodyCreate } from '../models/ServiceBodyCreate';
import { ServiceBodyPartialUpdate } from '../models/ServiceBodyPartialUpdate';
import { ServiceBodyUpdate } from '../models/ServiceBodyUpdate';
import { Token } from '../models/Token';
import { TokenCredentials } from '../models/TokenCredentials';
import { User } from '../models/User';
import { UserBase } from '../models/UserBase';
import { UserCreate } from '../models/UserCreate';
import { UserPartialUpdate } from '../models/UserPartialUpdate';
import { UserUpdate } from '../models/UserUpdate';
import { ValidationError } from '../models/ValidationError';
import { ObservableRootServerApi } from './ObservableAPI';

import { RootServerApiRequestFactory, RootServerApiResponseProcessor} from "../apis/RootServerApi";
export class PromiseRootServerApi {
    private api: ObservableRootServerApi

    public constructor(
        configuration: Configuration,
        requestFactory?: RootServerApiRequestFactory,
        responseProcessor?: RootServerApiResponseProcessor
    ) {
        this.api = new ObservableRootServerApi(configuration, requestFactory, responseProcessor);
    }

    /**
     * Revoke token and logout.
     * Revokes a token
     */
    public authLogoutWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.authLogoutWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Revoke token and logout.
     * Revokes a token
     */
    public authLogout(_options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.authLogout(observableOptions);
        return result.toPromise();
    }

    /**
     * Refresh token.
     * Revokes and issues a new token
     */
    public authRefreshWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<Token>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.authRefreshWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Refresh token.
     * Revokes and issues a new token
     */
    public authRefresh(_options?: PromiseConfigurationOptions): Promise<Token> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.authRefresh(observableOptions);
        return result.toPromise();
    }

    /**
     * Exchange credentials for a new token
     * Creates a token
     * @param tokenCredentials User credentials
     */
    public authTokenWithHttpInfo(tokenCredentials: TokenCredentials, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Token>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.authTokenWithHttpInfo(tokenCredentials, observableOptions);
        return result.toPromise();
    }

    /**
     * Exchange credentials for a new token
     * Creates a token
     * @param tokenCredentials User credentials
     */
    public authToken(tokenCredentials: TokenCredentials, _options?: PromiseConfigurationOptions): Promise<Token> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.authToken(tokenCredentials, observableOptions);
        return result.toPromise();
    }

    /**
     * Tests some errors.
     * Tests some errors
     * @param errorTest Pass in error test object.
     */
    public createErrorTestWithHttpInfo(errorTest: ErrorTest, _options?: PromiseConfigurationOptions): Promise<HttpInfo<ErrorTest>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createErrorTestWithHttpInfo(errorTest, observableOptions);
        return result.toPromise();
    }

    /**
     * Tests some errors.
     * Tests some errors
     * @param errorTest Pass in error test object.
     */
    public createErrorTest(errorTest: ErrorTest, _options?: PromiseConfigurationOptions): Promise<ErrorTest> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createErrorTest(errorTest, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a format.
     * Creates a format
     * @param formatCreate Pass in format object
     */
    public createFormatWithHttpInfo(formatCreate: FormatCreate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Format>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createFormatWithHttpInfo(formatCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a format.
     * Creates a format
     * @param formatCreate Pass in format object
     */
    public createFormat(formatCreate: FormatCreate, _options?: PromiseConfigurationOptions): Promise<Format> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createFormat(formatCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a meeting.
     * Creates a meeting
     * @param meetingCreate Pass in meeting object
     */
    public createMeetingWithHttpInfo(meetingCreate: MeetingCreate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Meeting>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createMeetingWithHttpInfo(meetingCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a meeting.
     * Creates a meeting
     * @param meetingCreate Pass in meeting object
     */
    public createMeeting(meetingCreate: MeetingCreate, _options?: PromiseConfigurationOptions): Promise<Meeting> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createMeeting(meetingCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a service body.
     * Creates a service body
     * @param serviceBodyCreate Pass in service body object
     */
    public createServiceBodyWithHttpInfo(serviceBodyCreate: ServiceBodyCreate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<ServiceBody>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createServiceBodyWithHttpInfo(serviceBodyCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a service body.
     * Creates a service body
     * @param serviceBodyCreate Pass in service body object
     */
    public createServiceBody(serviceBodyCreate: ServiceBodyCreate, _options?: PromiseConfigurationOptions): Promise<ServiceBody> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createServiceBody(serviceBodyCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a user.
     * Creates a user
     * @param userCreate Pass in user object
     */
    public createUserWithHttpInfo(userCreate: UserCreate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<User>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createUserWithHttpInfo(userCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Creates a user.
     * Creates a user
     * @param userCreate Pass in user object
     */
    public createUser(userCreate: UserCreate, _options?: PromiseConfigurationOptions): Promise<User> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.createUser(userCreate, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a format by id.
     * Deletes a format
     * @param formatId ID of format
     */
    public deleteFormatWithHttpInfo(formatId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteFormatWithHttpInfo(formatId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a format by id.
     * Deletes a format
     * @param formatId ID of format
     */
    public deleteFormat(formatId: number, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteFormat(formatId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a meeting by id.
     * Deletes a meeting
     * @param meetingId ID of meeting
     */
    public deleteMeetingWithHttpInfo(meetingId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteMeetingWithHttpInfo(meetingId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a meeting by id.
     * Deletes a meeting
     * @param meetingId ID of meeting
     */
    public deleteMeeting(meetingId: number, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteMeeting(meetingId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a service body by id.
     * Deletes a service body
     * @param serviceBodyId ID of service body
     */
    public deleteServiceBodyWithHttpInfo(serviceBodyId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteServiceBodyWithHttpInfo(serviceBodyId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a service body by id.
     * Deletes a service body
     * @param serviceBodyId ID of service body
     */
    public deleteServiceBody(serviceBodyId: number, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteServiceBody(serviceBodyId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a user by id
     * Deletes a user
     * @param userId ID of user
     */
    public deleteUserWithHttpInfo(userId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteUserWithHttpInfo(userId, observableOptions);
        return result.toPromise();
    }

    /**
     * Deletes a user by id
     * Deletes a user
     * @param userId ID of user
     */
    public deleteUser(userId: number, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.deleteUser(userId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a format
     * Retrieves a format
     * @param formatId ID of format
     */
    public getFormatWithHttpInfo(formatId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Format>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getFormatWithHttpInfo(formatId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a format
     * Retrieves a format
     * @param formatId ID of format
     */
    public getFormat(formatId: number, _options?: PromiseConfigurationOptions): Promise<Format> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getFormat(formatId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve formats
     * Retrieves formats
     */
    public getFormatsWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<Array<Format>>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getFormatsWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve formats
     * Retrieves formats
     */
    public getFormats(_options?: PromiseConfigurationOptions): Promise<Array<Format>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getFormats(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve the laravel log if it exists.
     * Retrieves laravel log
     */
    public getLaravelLogWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<HttpFile>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getLaravelLogWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve the laravel log if it exists.
     * Retrieves laravel log
     */
    public getLaravelLog(_options?: PromiseConfigurationOptions): Promise<HttpFile> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getLaravelLog(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a meeting.
     * Retrieves a meeting
     * @param meetingId ID of meeting
     */
    public getMeetingWithHttpInfo(meetingId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Meeting>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getMeetingWithHttpInfo(meetingId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a meeting.
     * Retrieves a meeting
     * @param meetingId ID of meeting
     */
    public getMeeting(meetingId: number, _options?: PromiseConfigurationOptions): Promise<Meeting> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getMeeting(meetingId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve all changes made to a specific meeting.
     * Retrieve changes for a meeting
     * @param meetingId ID of the meeting
     */
    public getMeetingChangesWithHttpInfo(meetingId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Array<MeetingChangeResource>>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getMeetingChangesWithHttpInfo(meetingId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve all changes made to a specific meeting.
     * Retrieve changes for a meeting
     * @param meetingId ID of the meeting
     */
    public getMeetingChanges(meetingId: number, _options?: PromiseConfigurationOptions): Promise<Array<MeetingChangeResource>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getMeetingChanges(meetingId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve meetings for authenticated user.
     * Retrieves meetings
     * @param [meetingIds] comma delimited meeting ids
     * @param [days] comma delimited day ids between 0-6
     * @param [serviceBodyIds] comma delimited service body ids
     * @param [searchString] string
     */
    public getMeetingsWithHttpInfo(meetingIds?: string, days?: string, serviceBodyIds?: string, searchString?: string, _options?: PromiseConfigurationOptions): Promise<HttpInfo<Array<Meeting>>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getMeetingsWithHttpInfo(meetingIds, days, serviceBodyIds, searchString, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve meetings for authenticated user.
     * Retrieves meetings
     * @param [meetingIds] comma delimited meeting ids
     * @param [days] comma delimited day ids between 0-6
     * @param [serviceBodyIds] comma delimited service body ids
     * @param [searchString] string
     */
    public getMeetings(meetingIds?: string, days?: string, serviceBodyIds?: string, searchString?: string, _options?: PromiseConfigurationOptions): Promise<Array<Meeting>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getMeetings(meetingIds, days, serviceBodyIds, searchString, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a single root server id.
     * Retrieves a root server
     * @param rootServerId ID of root server
     */
    public getRootServerWithHttpInfo(rootServerId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<RootServer>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getRootServerWithHttpInfo(rootServerId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a single root server id.
     * Retrieves a root server
     * @param rootServerId ID of root server
     */
    public getRootServer(rootServerId: number, _options?: PromiseConfigurationOptions): Promise<RootServer> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getRootServer(rootServerId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve root servers.
     * Retrieves root servers
     */
    public getRootServersWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<Array<RootServer>>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getRootServersWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve root servers.
     * Retrieves root servers
     */
    public getRootServers(_options?: PromiseConfigurationOptions): Promise<Array<RootServer>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getRootServers(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve service bodies for authenticated user.
     * Retrieves service bodies
     */
    public getServiceBodiesWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<Array<ServiceBody>>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getServiceBodiesWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve service bodies for authenticated user.
     * Retrieves service bodies
     */
    public getServiceBodies(_options?: PromiseConfigurationOptions): Promise<Array<ServiceBody>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getServiceBodies(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a single service body by id.
     * Retrieves a service body
     * @param serviceBodyId ID of service body
     */
    public getServiceBodyWithHttpInfo(serviceBodyId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<ServiceBody>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getServiceBodyWithHttpInfo(serviceBodyId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve a single service body by id.
     * Retrieves a service body
     * @param serviceBodyId ID of service body
     */
    public getServiceBody(serviceBodyId: number, _options?: PromiseConfigurationOptions): Promise<ServiceBody> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getServiceBody(serviceBodyId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve single user.
     * Retrieves a single user
     * @param userId ID of user
     */
    public getUserWithHttpInfo(userId: number, _options?: PromiseConfigurationOptions): Promise<HttpInfo<User>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getUserWithHttpInfo(userId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve single user.
     * Retrieves a single user
     * @param userId ID of user
     */
    public getUser(userId: number, _options?: PromiseConfigurationOptions): Promise<User> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getUser(userId, observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve users for authenticated user.
     * Retrieves users
     */
    public getUsersWithHttpInfo(_options?: PromiseConfigurationOptions): Promise<HttpInfo<Array<User>>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getUsersWithHttpInfo(observableOptions);
        return result.toPromise();
    }

    /**
     * Retrieve users for authenticated user.
     * Retrieves users
     */
    public getUsers(_options?: PromiseConfigurationOptions): Promise<Array<User>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.getUsers(observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a user by id.
     * Patches a user
     * @param userId ID of user
     * @param userPartialUpdate Pass in fields you want to update.
     */
    public partialUpdateUserWithHttpInfo(userId: number, userPartialUpdate: UserPartialUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.partialUpdateUserWithHttpInfo(userId, userPartialUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a user by id.
     * Patches a user
     * @param userId ID of user
     * @param userPartialUpdate Pass in fields you want to update.
     */
    public partialUpdateUser(userId: number, userPartialUpdate: UserPartialUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.partialUpdateUser(userId, userPartialUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a single format by id.
     * Patches a format
     * @param formatId ID of format
     * @param formatPartialUpdate Pass in fields you want to update.
     */
    public patchFormatWithHttpInfo(formatId: number, formatPartialUpdate: FormatPartialUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.patchFormatWithHttpInfo(formatId, formatPartialUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a single format by id.
     * Patches a format
     * @param formatId ID of format
     * @param formatPartialUpdate Pass in fields you want to update.
     */
    public patchFormat(formatId: number, formatPartialUpdate: FormatPartialUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.patchFormat(formatId, formatPartialUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a meeting by id
     * Patches a meeting
     * @param meetingId ID of meeting
     * @param meetingPartialUpdate Pass in fields you want to update.
     * @param [skipVenueTypeLocationValidation] specify true to skip venue type location validation
     */
    public patchMeetingWithHttpInfo(meetingId: number, meetingPartialUpdate: MeetingPartialUpdate, skipVenueTypeLocationValidation?: boolean, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.patchMeetingWithHttpInfo(meetingId, meetingPartialUpdate, skipVenueTypeLocationValidation, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a meeting by id
     * Patches a meeting
     * @param meetingId ID of meeting
     * @param meetingPartialUpdate Pass in fields you want to update.
     * @param [skipVenueTypeLocationValidation] specify true to skip venue type location validation
     */
    public patchMeeting(meetingId: number, meetingPartialUpdate: MeetingPartialUpdate, skipVenueTypeLocationValidation?: boolean, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.patchMeeting(meetingId, meetingPartialUpdate, skipVenueTypeLocationValidation, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a single service body by id.
     * Patches a service body
     * @param serviceBodyId ID of service body
     * @param serviceBodyPartialUpdate Pass in fields you want to update.
     */
    public patchServiceBodyWithHttpInfo(serviceBodyId: number, serviceBodyPartialUpdate: ServiceBodyPartialUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.patchServiceBodyWithHttpInfo(serviceBodyId, serviceBodyPartialUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Patches a single service body by id.
     * Patches a service body
     * @param serviceBodyId ID of service body
     * @param serviceBodyPartialUpdate Pass in fields you want to update.
     */
    public patchServiceBody(serviceBodyId: number, serviceBodyPartialUpdate: ServiceBodyPartialUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.patchServiceBody(serviceBodyId, serviceBodyPartialUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a format.
     * Updates a format
     * @param formatId ID of format
     * @param formatUpdate Pass in format object
     */
    public updateFormatWithHttpInfo(formatId: number, formatUpdate: FormatUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateFormatWithHttpInfo(formatId, formatUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a format.
     * Updates a format
     * @param formatId ID of format
     * @param formatUpdate Pass in format object
     */
    public updateFormat(formatId: number, formatUpdate: FormatUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateFormat(formatId, formatUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a meeting.
     * Updates a meeting
     * @param meetingId ID of meeting
     * @param meetingUpdate Pass in meeting object
     */
    public updateMeetingWithHttpInfo(meetingId: number, meetingUpdate: MeetingUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateMeetingWithHttpInfo(meetingId, meetingUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a meeting.
     * Updates a meeting
     * @param meetingId ID of meeting
     * @param meetingUpdate Pass in meeting object
     */
    public updateMeeting(meetingId: number, meetingUpdate: MeetingUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateMeeting(meetingId, meetingUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a single service body.
     * Updates a Service Body
     * @param serviceBodyId ID of service body
     * @param serviceBodyUpdate Pass in service body object
     */
    public updateServiceBodyWithHttpInfo(serviceBodyId: number, serviceBodyUpdate: ServiceBodyUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateServiceBodyWithHttpInfo(serviceBodyId, serviceBodyUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a single service body.
     * Updates a Service Body
     * @param serviceBodyId ID of service body
     * @param serviceBodyUpdate Pass in service body object
     */
    public updateServiceBody(serviceBodyId: number, serviceBodyUpdate: ServiceBodyUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateServiceBody(serviceBodyId, serviceBodyUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a user.
     * Update single user
     * @param userId ID of user
     * @param userUpdate Pass in user object
     */
    public updateUserWithHttpInfo(userId: number, userUpdate: UserUpdate, _options?: PromiseConfigurationOptions): Promise<HttpInfo<void>> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateUserWithHttpInfo(userId, userUpdate, observableOptions);
        return result.toPromise();
    }

    /**
     * Updates a user.
     * Update single user
     * @param userId ID of user
     * @param userUpdate Pass in user object
     */
    public updateUser(userId: number, userUpdate: UserUpdate, _options?: PromiseConfigurationOptions): Promise<void> {
        const observableOptions = wrapOptions(_options);
        const result = this.api.updateUser(userId, userUpdate, observableOptions);
        return result.toPromise();
    }


}



