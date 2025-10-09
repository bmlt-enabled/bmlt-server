import { ResponseContext, RequestContext, HttpFile, HttpInfo } from '../http/http';
import { Configuration, ConfigurationOptions } from '../configuration'
import type { Middleware } from '../middleware';

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

import { ObservableRootServerApi } from "./ObservableAPI";
import { RootServerApiRequestFactory, RootServerApiResponseProcessor} from "../apis/RootServerApi";

export interface RootServerApiAuthLogoutRequest {
}

export interface RootServerApiAuthRefreshRequest {
}

export interface RootServerApiAuthTokenRequest {
    /**
     * User credentials
     * @type TokenCredentials
     * @memberof RootServerApiauthToken
     */
    tokenCredentials: TokenCredentials
}

export interface RootServerApiCreateErrorTestRequest {
    /**
     * Pass in error test object.
     * @type ErrorTest
     * @memberof RootServerApicreateErrorTest
     */
    errorTest: ErrorTest
}

export interface RootServerApiCreateFormatRequest {
    /**
     * Pass in format object
     * @type FormatCreate
     * @memberof RootServerApicreateFormat
     */
    formatCreate: FormatCreate
}

export interface RootServerApiCreateMeetingRequest {
    /**
     * Pass in meeting object
     * @type MeetingCreate
     * @memberof RootServerApicreateMeeting
     */
    meetingCreate: MeetingCreate
}

export interface RootServerApiCreateServiceBodyRequest {
    /**
     * Pass in service body object
     * @type ServiceBodyCreate
     * @memberof RootServerApicreateServiceBody
     */
    serviceBodyCreate: ServiceBodyCreate
}

export interface RootServerApiCreateUserRequest {
    /**
     * Pass in user object
     * @type UserCreate
     * @memberof RootServerApicreateUser
     */
    userCreate: UserCreate
}

export interface RootServerApiDeleteFormatRequest {
    /**
     * ID of format
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApideleteFormat
     */
    formatId: number
}

export interface RootServerApiDeleteMeetingRequest {
    /**
     * ID of meeting
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApideleteMeeting
     */
    meetingId: number
}

export interface RootServerApiDeleteServiceBodyRequest {
    /**
     * ID of service body
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApideleteServiceBody
     */
    serviceBodyId: number
}

export interface RootServerApiDeleteUserRequest {
    /**
     * ID of user
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApideleteUser
     */
    userId: number
}

export interface RootServerApiGetFormatRequest {
    /**
     * ID of format
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApigetFormat
     */
    formatId: number
}

export interface RootServerApiGetFormatsRequest {
}

export interface RootServerApiGetLaravelLogRequest {
}

export interface RootServerApiGetMeetingRequest {
    /**
     * ID of meeting
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApigetMeeting
     */
    meetingId: number
}

export interface RootServerApiGetMeetingChangesRequest {
    /**
     * ID of the meeting
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApigetMeetingChanges
     */
    meetingId: number
}

export interface RootServerApiGetMeetingsRequest {
    /**
     * comma delimited meeting ids
     * Defaults to: undefined
     * @type string
     * @memberof RootServerApigetMeetings
     */
    meetingIds?: string
    /**
     * comma delimited day ids between 0-6
     * Defaults to: undefined
     * @type string
     * @memberof RootServerApigetMeetings
     */
    days?: string
    /**
     * comma delimited service body ids
     * Defaults to: undefined
     * @type string
     * @memberof RootServerApigetMeetings
     */
    serviceBodyIds?: string
    /**
     * string
     * Defaults to: undefined
     * @type string
     * @memberof RootServerApigetMeetings
     */
    searchString?: string
}

export interface RootServerApiGetRootServerRequest {
    /**
     * ID of root server
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApigetRootServer
     */
    rootServerId: number
}

export interface RootServerApiGetRootServersRequest {
}

export interface RootServerApiGetServiceBodiesRequest {
}

export interface RootServerApiGetServiceBodyRequest {
    /**
     * ID of service body
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApigetServiceBody
     */
    serviceBodyId: number
}

export interface RootServerApiGetUserRequest {
    /**
     * ID of user
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApigetUser
     */
    userId: number
}

export interface RootServerApiGetUsersRequest {
}

export interface RootServerApiPartialUpdateUserRequest {
    /**
     * ID of user
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApipartialUpdateUser
     */
    userId: number
    /**
     * Pass in fields you want to update.
     * @type UserPartialUpdate
     * @memberof RootServerApipartialUpdateUser
     */
    userPartialUpdate: UserPartialUpdate
}

export interface RootServerApiPatchFormatRequest {
    /**
     * ID of format
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApipatchFormat
     */
    formatId: number
    /**
     * Pass in fields you want to update.
     * @type FormatPartialUpdate
     * @memberof RootServerApipatchFormat
     */
    formatPartialUpdate: FormatPartialUpdate
}

export interface RootServerApiPatchMeetingRequest {
    /**
     * ID of meeting
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApipatchMeeting
     */
    meetingId: number
    /**
     * Pass in fields you want to update.
     * @type MeetingPartialUpdate
     * @memberof RootServerApipatchMeeting
     */
    meetingPartialUpdate: MeetingPartialUpdate
    /**
     * specify true to skip venue type location validation
     * Defaults to: undefined
     * @type boolean
     * @memberof RootServerApipatchMeeting
     */
    skipVenueTypeLocationValidation?: boolean
}

export interface RootServerApiPatchServiceBodyRequest {
    /**
     * ID of service body
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApipatchServiceBody
     */
    serviceBodyId: number
    /**
     * Pass in fields you want to update.
     * @type ServiceBodyPartialUpdate
     * @memberof RootServerApipatchServiceBody
     */
    serviceBodyPartialUpdate: ServiceBodyPartialUpdate
}

export interface RootServerApiUpdateFormatRequest {
    /**
     * ID of format
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApiupdateFormat
     */
    formatId: number
    /**
     * Pass in format object
     * @type FormatUpdate
     * @memberof RootServerApiupdateFormat
     */
    formatUpdate: FormatUpdate
}

export interface RootServerApiUpdateMeetingRequest {
    /**
     * ID of meeting
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApiupdateMeeting
     */
    meetingId: number
    /**
     * Pass in meeting object
     * @type MeetingUpdate
     * @memberof RootServerApiupdateMeeting
     */
    meetingUpdate: MeetingUpdate
}

export interface RootServerApiUpdateServiceBodyRequest {
    /**
     * ID of service body
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApiupdateServiceBody
     */
    serviceBodyId: number
    /**
     * Pass in service body object
     * @type ServiceBodyUpdate
     * @memberof RootServerApiupdateServiceBody
     */
    serviceBodyUpdate: ServiceBodyUpdate
}

export interface RootServerApiUpdateUserRequest {
    /**
     * ID of user
     * Defaults to: undefined
     * @type number
     * @memberof RootServerApiupdateUser
     */
    userId: number
    /**
     * Pass in user object
     * @type UserUpdate
     * @memberof RootServerApiupdateUser
     */
    userUpdate: UserUpdate
}

export class ObjectRootServerApi {
    private api: ObservableRootServerApi

    public constructor(configuration: Configuration, requestFactory?: RootServerApiRequestFactory, responseProcessor?: RootServerApiResponseProcessor) {
        this.api = new ObservableRootServerApi(configuration, requestFactory, responseProcessor);
    }

    /**
     * Revoke token and logout.
     * Revokes a token
     * @param param the request object
     */
    public authLogoutWithHttpInfo(param: RootServerApiAuthLogoutRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.authLogoutWithHttpInfo( options).toPromise();
    }

    /**
     * Revoke token and logout.
     * Revokes a token
     * @param param the request object
     */
    public authLogout(param: RootServerApiAuthLogoutRequest = {}, options?: ConfigurationOptions): Promise<void> {
        return this.api.authLogout( options).toPromise();
    }

    /**
     * Refresh token.
     * Revokes and issues a new token
     * @param param the request object
     */
    public authRefreshWithHttpInfo(param: RootServerApiAuthRefreshRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<Token>> {
        return this.api.authRefreshWithHttpInfo( options).toPromise();
    }

    /**
     * Refresh token.
     * Revokes and issues a new token
     * @param param the request object
     */
    public authRefresh(param: RootServerApiAuthRefreshRequest = {}, options?: ConfigurationOptions): Promise<Token> {
        return this.api.authRefresh( options).toPromise();
    }

    /**
     * Exchange credentials for a new token
     * Creates a token
     * @param param the request object
     */
    public authTokenWithHttpInfo(param: RootServerApiAuthTokenRequest, options?: ConfigurationOptions): Promise<HttpInfo<Token>> {
        return this.api.authTokenWithHttpInfo(param.tokenCredentials,  options).toPromise();
    }

    /**
     * Exchange credentials for a new token
     * Creates a token
     * @param param the request object
     */
    public authToken(param: RootServerApiAuthTokenRequest, options?: ConfigurationOptions): Promise<Token> {
        return this.api.authToken(param.tokenCredentials,  options).toPromise();
    }

    /**
     * Tests some errors.
     * Tests some errors
     * @param param the request object
     */
    public createErrorTestWithHttpInfo(param: RootServerApiCreateErrorTestRequest, options?: ConfigurationOptions): Promise<HttpInfo<ErrorTest>> {
        return this.api.createErrorTestWithHttpInfo(param.errorTest,  options).toPromise();
    }

    /**
     * Tests some errors.
     * Tests some errors
     * @param param the request object
     */
    public createErrorTest(param: RootServerApiCreateErrorTestRequest, options?: ConfigurationOptions): Promise<ErrorTest> {
        return this.api.createErrorTest(param.errorTest,  options).toPromise();
    }

    /**
     * Creates a format.
     * Creates a format
     * @param param the request object
     */
    public createFormatWithHttpInfo(param: RootServerApiCreateFormatRequest, options?: ConfigurationOptions): Promise<HttpInfo<Format>> {
        return this.api.createFormatWithHttpInfo(param.formatCreate,  options).toPromise();
    }

    /**
     * Creates a format.
     * Creates a format
     * @param param the request object
     */
    public createFormat(param: RootServerApiCreateFormatRequest, options?: ConfigurationOptions): Promise<Format> {
        return this.api.createFormat(param.formatCreate,  options).toPromise();
    }

    /**
     * Creates a meeting.
     * Creates a meeting
     * @param param the request object
     */
    public createMeetingWithHttpInfo(param: RootServerApiCreateMeetingRequest, options?: ConfigurationOptions): Promise<HttpInfo<Meeting>> {
        return this.api.createMeetingWithHttpInfo(param.meetingCreate,  options).toPromise();
    }

    /**
     * Creates a meeting.
     * Creates a meeting
     * @param param the request object
     */
    public createMeeting(param: RootServerApiCreateMeetingRequest, options?: ConfigurationOptions): Promise<Meeting> {
        return this.api.createMeeting(param.meetingCreate,  options).toPromise();
    }

    /**
     * Creates a service body.
     * Creates a service body
     * @param param the request object
     */
    public createServiceBodyWithHttpInfo(param: RootServerApiCreateServiceBodyRequest, options?: ConfigurationOptions): Promise<HttpInfo<ServiceBody>> {
        return this.api.createServiceBodyWithHttpInfo(param.serviceBodyCreate,  options).toPromise();
    }

    /**
     * Creates a service body.
     * Creates a service body
     * @param param the request object
     */
    public createServiceBody(param: RootServerApiCreateServiceBodyRequest, options?: ConfigurationOptions): Promise<ServiceBody> {
        return this.api.createServiceBody(param.serviceBodyCreate,  options).toPromise();
    }

    /**
     * Creates a user.
     * Creates a user
     * @param param the request object
     */
    public createUserWithHttpInfo(param: RootServerApiCreateUserRequest, options?: ConfigurationOptions): Promise<HttpInfo<User>> {
        return this.api.createUserWithHttpInfo(param.userCreate,  options).toPromise();
    }

    /**
     * Creates a user.
     * Creates a user
     * @param param the request object
     */
    public createUser(param: RootServerApiCreateUserRequest, options?: ConfigurationOptions): Promise<User> {
        return this.api.createUser(param.userCreate,  options).toPromise();
    }

    /**
     * Deletes a format by id.
     * Deletes a format
     * @param param the request object
     */
    public deleteFormatWithHttpInfo(param: RootServerApiDeleteFormatRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.deleteFormatWithHttpInfo(param.formatId,  options).toPromise();
    }

    /**
     * Deletes a format by id.
     * Deletes a format
     * @param param the request object
     */
    public deleteFormat(param: RootServerApiDeleteFormatRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.deleteFormat(param.formatId,  options).toPromise();
    }

    /**
     * Deletes a meeting by id.
     * Deletes a meeting
     * @param param the request object
     */
    public deleteMeetingWithHttpInfo(param: RootServerApiDeleteMeetingRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.deleteMeetingWithHttpInfo(param.meetingId,  options).toPromise();
    }

    /**
     * Deletes a meeting by id.
     * Deletes a meeting
     * @param param the request object
     */
    public deleteMeeting(param: RootServerApiDeleteMeetingRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.deleteMeeting(param.meetingId,  options).toPromise();
    }

    /**
     * Deletes a service body by id.
     * Deletes a service body
     * @param param the request object
     */
    public deleteServiceBodyWithHttpInfo(param: RootServerApiDeleteServiceBodyRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.deleteServiceBodyWithHttpInfo(param.serviceBodyId,  options).toPromise();
    }

    /**
     * Deletes a service body by id.
     * Deletes a service body
     * @param param the request object
     */
    public deleteServiceBody(param: RootServerApiDeleteServiceBodyRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.deleteServiceBody(param.serviceBodyId,  options).toPromise();
    }

    /**
     * Deletes a user by id
     * Deletes a user
     * @param param the request object
     */
    public deleteUserWithHttpInfo(param: RootServerApiDeleteUserRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.deleteUserWithHttpInfo(param.userId,  options).toPromise();
    }

    /**
     * Deletes a user by id
     * Deletes a user
     * @param param the request object
     */
    public deleteUser(param: RootServerApiDeleteUserRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.deleteUser(param.userId,  options).toPromise();
    }

    /**
     * Retrieve a format
     * Retrieves a format
     * @param param the request object
     */
    public getFormatWithHttpInfo(param: RootServerApiGetFormatRequest, options?: ConfigurationOptions): Promise<HttpInfo<Format>> {
        return this.api.getFormatWithHttpInfo(param.formatId,  options).toPromise();
    }

    /**
     * Retrieve a format
     * Retrieves a format
     * @param param the request object
     */
    public getFormat(param: RootServerApiGetFormatRequest, options?: ConfigurationOptions): Promise<Format> {
        return this.api.getFormat(param.formatId,  options).toPromise();
    }

    /**
     * Retrieve formats
     * Retrieves formats
     * @param param the request object
     */
    public getFormatsWithHttpInfo(param: RootServerApiGetFormatsRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<Array<Format>>> {
        return this.api.getFormatsWithHttpInfo( options).toPromise();
    }

    /**
     * Retrieve formats
     * Retrieves formats
     * @param param the request object
     */
    public getFormats(param: RootServerApiGetFormatsRequest = {}, options?: ConfigurationOptions): Promise<Array<Format>> {
        return this.api.getFormats( options).toPromise();
    }

    /**
     * Retrieve the laravel log if it exists.
     * Retrieves laravel log
     * @param param the request object
     */
    public getLaravelLogWithHttpInfo(param: RootServerApiGetLaravelLogRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<HttpFile>> {
        return this.api.getLaravelLogWithHttpInfo( options).toPromise();
    }

    /**
     * Retrieve the laravel log if it exists.
     * Retrieves laravel log
     * @param param the request object
     */
    public getLaravelLog(param: RootServerApiGetLaravelLogRequest = {}, options?: ConfigurationOptions): Promise<HttpFile> {
        return this.api.getLaravelLog( options).toPromise();
    }

    /**
     * Retrieve a meeting.
     * Retrieves a meeting
     * @param param the request object
     */
    public getMeetingWithHttpInfo(param: RootServerApiGetMeetingRequest, options?: ConfigurationOptions): Promise<HttpInfo<Meeting>> {
        return this.api.getMeetingWithHttpInfo(param.meetingId,  options).toPromise();
    }

    /**
     * Retrieve a meeting.
     * Retrieves a meeting
     * @param param the request object
     */
    public getMeeting(param: RootServerApiGetMeetingRequest, options?: ConfigurationOptions): Promise<Meeting> {
        return this.api.getMeeting(param.meetingId,  options).toPromise();
    }

    /**
     * Retrieve all changes made to a specific meeting.
     * Retrieve changes for a meeting
     * @param param the request object
     */
    public getMeetingChangesWithHttpInfo(param: RootServerApiGetMeetingChangesRequest, options?: ConfigurationOptions): Promise<HttpInfo<Array<MeetingChangeResource>>> {
        return this.api.getMeetingChangesWithHttpInfo(param.meetingId,  options).toPromise();
    }

    /**
     * Retrieve all changes made to a specific meeting.
     * Retrieve changes for a meeting
     * @param param the request object
     */
    public getMeetingChanges(param: RootServerApiGetMeetingChangesRequest, options?: ConfigurationOptions): Promise<Array<MeetingChangeResource>> {
        return this.api.getMeetingChanges(param.meetingId,  options).toPromise();
    }

    /**
     * Retrieve meetings for authenticated user.
     * Retrieves meetings
     * @param param the request object
     */
    public getMeetingsWithHttpInfo(param: RootServerApiGetMeetingsRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<Array<Meeting>>> {
        return this.api.getMeetingsWithHttpInfo(param.meetingIds, param.days, param.serviceBodyIds, param.searchString,  options).toPromise();
    }

    /**
     * Retrieve meetings for authenticated user.
     * Retrieves meetings
     * @param param the request object
     */
    public getMeetings(param: RootServerApiGetMeetingsRequest = {}, options?: ConfigurationOptions): Promise<Array<Meeting>> {
        return this.api.getMeetings(param.meetingIds, param.days, param.serviceBodyIds, param.searchString,  options).toPromise();
    }

    /**
     * Retrieve a single root server id.
     * Retrieves a root server
     * @param param the request object
     */
    public getRootServerWithHttpInfo(param: RootServerApiGetRootServerRequest, options?: ConfigurationOptions): Promise<HttpInfo<RootServer>> {
        return this.api.getRootServerWithHttpInfo(param.rootServerId,  options).toPromise();
    }

    /**
     * Retrieve a single root server id.
     * Retrieves a root server
     * @param param the request object
     */
    public getRootServer(param: RootServerApiGetRootServerRequest, options?: ConfigurationOptions): Promise<RootServer> {
        return this.api.getRootServer(param.rootServerId,  options).toPromise();
    }

    /**
     * Retrieve root servers.
     * Retrieves root servers
     * @param param the request object
     */
    public getRootServersWithHttpInfo(param: RootServerApiGetRootServersRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<Array<RootServer>>> {
        return this.api.getRootServersWithHttpInfo( options).toPromise();
    }

    /**
     * Retrieve root servers.
     * Retrieves root servers
     * @param param the request object
     */
    public getRootServers(param: RootServerApiGetRootServersRequest = {}, options?: ConfigurationOptions): Promise<Array<RootServer>> {
        return this.api.getRootServers( options).toPromise();
    }

    /**
     * Retrieve service bodies for authenticated user.
     * Retrieves service bodies
     * @param param the request object
     */
    public getServiceBodiesWithHttpInfo(param: RootServerApiGetServiceBodiesRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<Array<ServiceBody>>> {
        return this.api.getServiceBodiesWithHttpInfo( options).toPromise();
    }

    /**
     * Retrieve service bodies for authenticated user.
     * Retrieves service bodies
     * @param param the request object
     */
    public getServiceBodies(param: RootServerApiGetServiceBodiesRequest = {}, options?: ConfigurationOptions): Promise<Array<ServiceBody>> {
        return this.api.getServiceBodies( options).toPromise();
    }

    /**
     * Retrieve a single service body by id.
     * Retrieves a service body
     * @param param the request object
     */
    public getServiceBodyWithHttpInfo(param: RootServerApiGetServiceBodyRequest, options?: ConfigurationOptions): Promise<HttpInfo<ServiceBody>> {
        return this.api.getServiceBodyWithHttpInfo(param.serviceBodyId,  options).toPromise();
    }

    /**
     * Retrieve a single service body by id.
     * Retrieves a service body
     * @param param the request object
     */
    public getServiceBody(param: RootServerApiGetServiceBodyRequest, options?: ConfigurationOptions): Promise<ServiceBody> {
        return this.api.getServiceBody(param.serviceBodyId,  options).toPromise();
    }

    /**
     * Retrieve single user.
     * Retrieves a single user
     * @param param the request object
     */
    public getUserWithHttpInfo(param: RootServerApiGetUserRequest, options?: ConfigurationOptions): Promise<HttpInfo<User>> {
        return this.api.getUserWithHttpInfo(param.userId,  options).toPromise();
    }

    /**
     * Retrieve single user.
     * Retrieves a single user
     * @param param the request object
     */
    public getUser(param: RootServerApiGetUserRequest, options?: ConfigurationOptions): Promise<User> {
        return this.api.getUser(param.userId,  options).toPromise();
    }

    /**
     * Retrieve users for authenticated user.
     * Retrieves users
     * @param param the request object
     */
    public getUsersWithHttpInfo(param: RootServerApiGetUsersRequest = {}, options?: ConfigurationOptions): Promise<HttpInfo<Array<User>>> {
        return this.api.getUsersWithHttpInfo( options).toPromise();
    }

    /**
     * Retrieve users for authenticated user.
     * Retrieves users
     * @param param the request object
     */
    public getUsers(param: RootServerApiGetUsersRequest = {}, options?: ConfigurationOptions): Promise<Array<User>> {
        return this.api.getUsers( options).toPromise();
    }

    /**
     * Patches a user by id.
     * Patches a user
     * @param param the request object
     */
    public partialUpdateUserWithHttpInfo(param: RootServerApiPartialUpdateUserRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.partialUpdateUserWithHttpInfo(param.userId, param.userPartialUpdate,  options).toPromise();
    }

    /**
     * Patches a user by id.
     * Patches a user
     * @param param the request object
     */
    public partialUpdateUser(param: RootServerApiPartialUpdateUserRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.partialUpdateUser(param.userId, param.userPartialUpdate,  options).toPromise();
    }

    /**
     * Patches a single format by id.
     * Patches a format
     * @param param the request object
     */
    public patchFormatWithHttpInfo(param: RootServerApiPatchFormatRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.patchFormatWithHttpInfo(param.formatId, param.formatPartialUpdate,  options).toPromise();
    }

    /**
     * Patches a single format by id.
     * Patches a format
     * @param param the request object
     */
    public patchFormat(param: RootServerApiPatchFormatRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.patchFormat(param.formatId, param.formatPartialUpdate,  options).toPromise();
    }

    /**
     * Patches a meeting by id
     * Patches a meeting
     * @param param the request object
     */
    public patchMeetingWithHttpInfo(param: RootServerApiPatchMeetingRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.patchMeetingWithHttpInfo(param.meetingId, param.meetingPartialUpdate, param.skipVenueTypeLocationValidation,  options).toPromise();
    }

    /**
     * Patches a meeting by id
     * Patches a meeting
     * @param param the request object
     */
    public patchMeeting(param: RootServerApiPatchMeetingRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.patchMeeting(param.meetingId, param.meetingPartialUpdate, param.skipVenueTypeLocationValidation,  options).toPromise();
    }

    /**
     * Patches a single service body by id.
     * Patches a service body
     * @param param the request object
     */
    public patchServiceBodyWithHttpInfo(param: RootServerApiPatchServiceBodyRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.patchServiceBodyWithHttpInfo(param.serviceBodyId, param.serviceBodyPartialUpdate,  options).toPromise();
    }

    /**
     * Patches a single service body by id.
     * Patches a service body
     * @param param the request object
     */
    public patchServiceBody(param: RootServerApiPatchServiceBodyRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.patchServiceBody(param.serviceBodyId, param.serviceBodyPartialUpdate,  options).toPromise();
    }

    /**
     * Updates a format.
     * Updates a format
     * @param param the request object
     */
    public updateFormatWithHttpInfo(param: RootServerApiUpdateFormatRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.updateFormatWithHttpInfo(param.formatId, param.formatUpdate,  options).toPromise();
    }

    /**
     * Updates a format.
     * Updates a format
     * @param param the request object
     */
    public updateFormat(param: RootServerApiUpdateFormatRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.updateFormat(param.formatId, param.formatUpdate,  options).toPromise();
    }

    /**
     * Updates a meeting.
     * Updates a meeting
     * @param param the request object
     */
    public updateMeetingWithHttpInfo(param: RootServerApiUpdateMeetingRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.updateMeetingWithHttpInfo(param.meetingId, param.meetingUpdate,  options).toPromise();
    }

    /**
     * Updates a meeting.
     * Updates a meeting
     * @param param the request object
     */
    public updateMeeting(param: RootServerApiUpdateMeetingRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.updateMeeting(param.meetingId, param.meetingUpdate,  options).toPromise();
    }

    /**
     * Updates a single service body.
     * Updates a Service Body
     * @param param the request object
     */
    public updateServiceBodyWithHttpInfo(param: RootServerApiUpdateServiceBodyRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.updateServiceBodyWithHttpInfo(param.serviceBodyId, param.serviceBodyUpdate,  options).toPromise();
    }

    /**
     * Updates a single service body.
     * Updates a Service Body
     * @param param the request object
     */
    public updateServiceBody(param: RootServerApiUpdateServiceBodyRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.updateServiceBody(param.serviceBodyId, param.serviceBodyUpdate,  options).toPromise();
    }

    /**
     * Updates a user.
     * Update single user
     * @param param the request object
     */
    public updateUserWithHttpInfo(param: RootServerApiUpdateUserRequest, options?: ConfigurationOptions): Promise<HttpInfo<void>> {
        return this.api.updateUserWithHttpInfo(param.userId, param.userUpdate,  options).toPromise();
    }

    /**
     * Updates a user.
     * Update single user
     * @param param the request object
     */
    public updateUser(param: RootServerApiUpdateUserRequest, options?: ConfigurationOptions): Promise<void> {
        return this.api.updateUser(param.userId, param.userUpdate,  options).toPromise();
    }

}
