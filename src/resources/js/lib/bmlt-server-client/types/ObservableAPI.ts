import { ResponseContext, RequestContext, HttpFile, HttpInfo } from '../http/http';
import { Configuration, ConfigurationOptions, mergeConfiguration } from '../configuration'
import type { Middleware } from '../middleware';
import { Observable, of, from } from '../rxjsStub';
import {mergeMap, map} from  '../rxjsStub';
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

import { RootServerApiRequestFactory, RootServerApiResponseProcessor} from "../apis/RootServerApi";
export class ObservableRootServerApi {
    private requestFactory: RootServerApiRequestFactory;
    private responseProcessor: RootServerApiResponseProcessor;
    private configuration: Configuration;

    public constructor(
        configuration: Configuration,
        requestFactory?: RootServerApiRequestFactory,
        responseProcessor?: RootServerApiResponseProcessor
    ) {
        this.configuration = configuration;
        this.requestFactory = requestFactory || new RootServerApiRequestFactory(configuration);
        this.responseProcessor = responseProcessor || new RootServerApiResponseProcessor();
    }

    /**
     * Revoke token and logout.
     * Revokes a token
     */
    public authLogoutWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.authLogout(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.authLogoutWithHttpInfo(rsp)));
            }));
    }

    /**
     * Revoke token and logout.
     * Revokes a token
     */
    public authLogout(_options?: ConfigurationOptions): Observable<void> {
        return this.authLogoutWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Refresh token.
     * Revokes and issues a new token
     */
    public authRefreshWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<Token>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.authRefresh(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.authRefreshWithHttpInfo(rsp)));
            }));
    }

    /**
     * Refresh token.
     * Revokes and issues a new token
     */
    public authRefresh(_options?: ConfigurationOptions): Observable<Token> {
        return this.authRefreshWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<Token>) => apiResponse.data));
    }

    /**
     * Exchange credentials for a new token
     * Creates a token
     * @param tokenCredentials User credentials
     */
    public authTokenWithHttpInfo(tokenCredentials: TokenCredentials, _options?: ConfigurationOptions): Observable<HttpInfo<Token>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.authToken(tokenCredentials, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.authTokenWithHttpInfo(rsp)));
            }));
    }

    /**
     * Exchange credentials for a new token
     * Creates a token
     * @param tokenCredentials User credentials
     */
    public authToken(tokenCredentials: TokenCredentials, _options?: ConfigurationOptions): Observable<Token> {
        return this.authTokenWithHttpInfo(tokenCredentials, _options).pipe(map((apiResponse: HttpInfo<Token>) => apiResponse.data));
    }

    /**
     * Tests some errors.
     * Tests some errors
     * @param errorTest Pass in error test object.
     */
    public createErrorTestWithHttpInfo(errorTest: ErrorTest, _options?: ConfigurationOptions): Observable<HttpInfo<ErrorTest>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.createErrorTest(errorTest, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.createErrorTestWithHttpInfo(rsp)));
            }));
    }

    /**
     * Tests some errors.
     * Tests some errors
     * @param errorTest Pass in error test object.
     */
    public createErrorTest(errorTest: ErrorTest, _options?: ConfigurationOptions): Observable<ErrorTest> {
        return this.createErrorTestWithHttpInfo(errorTest, _options).pipe(map((apiResponse: HttpInfo<ErrorTest>) => apiResponse.data));
    }

    /**
     * Creates a format.
     * Creates a format
     * @param formatCreate Pass in format object
     */
    public createFormatWithHttpInfo(formatCreate: FormatCreate, _options?: ConfigurationOptions): Observable<HttpInfo<Format>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.createFormat(formatCreate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.createFormatWithHttpInfo(rsp)));
            }));
    }

    /**
     * Creates a format.
     * Creates a format
     * @param formatCreate Pass in format object
     */
    public createFormat(formatCreate: FormatCreate, _options?: ConfigurationOptions): Observable<Format> {
        return this.createFormatWithHttpInfo(formatCreate, _options).pipe(map((apiResponse: HttpInfo<Format>) => apiResponse.data));
    }

    /**
     * Creates a meeting.
     * Creates a meeting
     * @param meetingCreate Pass in meeting object
     */
    public createMeetingWithHttpInfo(meetingCreate: MeetingCreate, _options?: ConfigurationOptions): Observable<HttpInfo<Meeting>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.createMeeting(meetingCreate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.createMeetingWithHttpInfo(rsp)));
            }));
    }

    /**
     * Creates a meeting.
     * Creates a meeting
     * @param meetingCreate Pass in meeting object
     */
    public createMeeting(meetingCreate: MeetingCreate, _options?: ConfigurationOptions): Observable<Meeting> {
        return this.createMeetingWithHttpInfo(meetingCreate, _options).pipe(map((apiResponse: HttpInfo<Meeting>) => apiResponse.data));
    }

    /**
     * Creates a service body.
     * Creates a service body
     * @param serviceBodyCreate Pass in service body object
     */
    public createServiceBodyWithHttpInfo(serviceBodyCreate: ServiceBodyCreate, _options?: ConfigurationOptions): Observable<HttpInfo<ServiceBody>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.createServiceBody(serviceBodyCreate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.createServiceBodyWithHttpInfo(rsp)));
            }));
    }

    /**
     * Creates a service body.
     * Creates a service body
     * @param serviceBodyCreate Pass in service body object
     */
    public createServiceBody(serviceBodyCreate: ServiceBodyCreate, _options?: ConfigurationOptions): Observable<ServiceBody> {
        return this.createServiceBodyWithHttpInfo(serviceBodyCreate, _options).pipe(map((apiResponse: HttpInfo<ServiceBody>) => apiResponse.data));
    }

    /**
     * Creates a user.
     * Creates a user
     * @param userCreate Pass in user object
     */
    public createUserWithHttpInfo(userCreate: UserCreate, _options?: ConfigurationOptions): Observable<HttpInfo<User>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.createUser(userCreate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.createUserWithHttpInfo(rsp)));
            }));
    }

    /**
     * Creates a user.
     * Creates a user
     * @param userCreate Pass in user object
     */
    public createUser(userCreate: UserCreate, _options?: ConfigurationOptions): Observable<User> {
        return this.createUserWithHttpInfo(userCreate, _options).pipe(map((apiResponse: HttpInfo<User>) => apiResponse.data));
    }

    /**
     * Deletes a format by id.
     * Deletes a format
     * @param formatId ID of format
     */
    public deleteFormatWithHttpInfo(formatId: number, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.deleteFormat(formatId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.deleteFormatWithHttpInfo(rsp)));
            }));
    }

    /**
     * Deletes a format by id.
     * Deletes a format
     * @param formatId ID of format
     */
    public deleteFormat(formatId: number, _options?: ConfigurationOptions): Observable<void> {
        return this.deleteFormatWithHttpInfo(formatId, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Deletes a meeting by id.
     * Deletes a meeting
     * @param meetingId ID of meeting
     */
    public deleteMeetingWithHttpInfo(meetingId: number, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.deleteMeeting(meetingId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.deleteMeetingWithHttpInfo(rsp)));
            }));
    }

    /**
     * Deletes a meeting by id.
     * Deletes a meeting
     * @param meetingId ID of meeting
     */
    public deleteMeeting(meetingId: number, _options?: ConfigurationOptions): Observable<void> {
        return this.deleteMeetingWithHttpInfo(meetingId, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Deletes a service body by id.
     * Deletes a service body
     * @param serviceBodyId ID of service body
     */
    public deleteServiceBodyWithHttpInfo(serviceBodyId: number, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.deleteServiceBody(serviceBodyId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.deleteServiceBodyWithHttpInfo(rsp)));
            }));
    }

    /**
     * Deletes a service body by id.
     * Deletes a service body
     * @param serviceBodyId ID of service body
     */
    public deleteServiceBody(serviceBodyId: number, _options?: ConfigurationOptions): Observable<void> {
        return this.deleteServiceBodyWithHttpInfo(serviceBodyId, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Deletes a user by id
     * Deletes a user
     * @param userId ID of user
     */
    public deleteUserWithHttpInfo(userId: number, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.deleteUser(userId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.deleteUserWithHttpInfo(rsp)));
            }));
    }

    /**
     * Deletes a user by id
     * Deletes a user
     * @param userId ID of user
     */
    public deleteUser(userId: number, _options?: ConfigurationOptions): Observable<void> {
        return this.deleteUserWithHttpInfo(userId, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Retrieve a format
     * Retrieves a format
     * @param formatId ID of format
     */
    public getFormatWithHttpInfo(formatId: number, _options?: ConfigurationOptions): Observable<HttpInfo<Format>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getFormat(formatId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getFormatWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve a format
     * Retrieves a format
     * @param formatId ID of format
     */
    public getFormat(formatId: number, _options?: ConfigurationOptions): Observable<Format> {
        return this.getFormatWithHttpInfo(formatId, _options).pipe(map((apiResponse: HttpInfo<Format>) => apiResponse.data));
    }

    /**
     * Retrieve formats
     * Retrieves formats
     */
    public getFormatsWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<Array<Format>>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getFormats(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getFormatsWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve formats
     * Retrieves formats
     */
    public getFormats(_options?: ConfigurationOptions): Observable<Array<Format>> {
        return this.getFormatsWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<Array<Format>>) => apiResponse.data));
    }

    /**
     * Retrieve the laravel log if it exists.
     * Retrieves laravel log
     */
    public getLaravelLogWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<HttpFile>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getLaravelLog(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getLaravelLogWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve the laravel log if it exists.
     * Retrieves laravel log
     */
    public getLaravelLog(_options?: ConfigurationOptions): Observable<HttpFile> {
        return this.getLaravelLogWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<HttpFile>) => apiResponse.data));
    }

    /**
     * Retrieve a meeting.
     * Retrieves a meeting
     * @param meetingId ID of meeting
     */
    public getMeetingWithHttpInfo(meetingId: number, _options?: ConfigurationOptions): Observable<HttpInfo<Meeting>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getMeeting(meetingId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getMeetingWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve a meeting.
     * Retrieves a meeting
     * @param meetingId ID of meeting
     */
    public getMeeting(meetingId: number, _options?: ConfigurationOptions): Observable<Meeting> {
        return this.getMeetingWithHttpInfo(meetingId, _options).pipe(map((apiResponse: HttpInfo<Meeting>) => apiResponse.data));
    }

    /**
     * Retrieve all changes made to a specific meeting.
     * Retrieve changes for a meeting
     * @param meetingId ID of the meeting
     */
    public getMeetingChangesWithHttpInfo(meetingId: number, _options?: ConfigurationOptions): Observable<HttpInfo<Array<MeetingChangeResource>>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getMeetingChanges(meetingId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getMeetingChangesWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve all changes made to a specific meeting.
     * Retrieve changes for a meeting
     * @param meetingId ID of the meeting
     */
    public getMeetingChanges(meetingId: number, _options?: ConfigurationOptions): Observable<Array<MeetingChangeResource>> {
        return this.getMeetingChangesWithHttpInfo(meetingId, _options).pipe(map((apiResponse: HttpInfo<Array<MeetingChangeResource>>) => apiResponse.data));
    }

    /**
     * Retrieve meetings for authenticated user.
     * Retrieves meetings
     * @param [meetingIds] comma delimited meeting ids
     * @param [days] comma delimited day ids between 0-6
     * @param [serviceBodyIds] comma delimited service body ids
     * @param [searchString] string
     */
    public getMeetingsWithHttpInfo(meetingIds?: string, days?: string, serviceBodyIds?: string, searchString?: string, _options?: ConfigurationOptions): Observable<HttpInfo<Array<Meeting>>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getMeetings(meetingIds, days, serviceBodyIds, searchString, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getMeetingsWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve meetings for authenticated user.
     * Retrieves meetings
     * @param [meetingIds] comma delimited meeting ids
     * @param [days] comma delimited day ids between 0-6
     * @param [serviceBodyIds] comma delimited service body ids
     * @param [searchString] string
     */
    public getMeetings(meetingIds?: string, days?: string, serviceBodyIds?: string, searchString?: string, _options?: ConfigurationOptions): Observable<Array<Meeting>> {
        return this.getMeetingsWithHttpInfo(meetingIds, days, serviceBodyIds, searchString, _options).pipe(map((apiResponse: HttpInfo<Array<Meeting>>) => apiResponse.data));
    }

    /**
     * Retrieve a single root server id.
     * Retrieves a root server
     * @param rootServerId ID of root server
     */
    public getRootServerWithHttpInfo(rootServerId: number, _options?: ConfigurationOptions): Observable<HttpInfo<RootServer>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getRootServer(rootServerId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getRootServerWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve a single root server id.
     * Retrieves a root server
     * @param rootServerId ID of root server
     */
    public getRootServer(rootServerId: number, _options?: ConfigurationOptions): Observable<RootServer> {
        return this.getRootServerWithHttpInfo(rootServerId, _options).pipe(map((apiResponse: HttpInfo<RootServer>) => apiResponse.data));
    }

    /**
     * Retrieve root servers.
     * Retrieves root servers
     */
    public getRootServersWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<Array<RootServer>>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getRootServers(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getRootServersWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve root servers.
     * Retrieves root servers
     */
    public getRootServers(_options?: ConfigurationOptions): Observable<Array<RootServer>> {
        return this.getRootServersWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<Array<RootServer>>) => apiResponse.data));
    }

    /**
     * Retrieve service bodies for authenticated user.
     * Retrieves service bodies
     */
    public getServiceBodiesWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<Array<ServiceBody>>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getServiceBodies(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getServiceBodiesWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve service bodies for authenticated user.
     * Retrieves service bodies
     */
    public getServiceBodies(_options?: ConfigurationOptions): Observable<Array<ServiceBody>> {
        return this.getServiceBodiesWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<Array<ServiceBody>>) => apiResponse.data));
    }

    /**
     * Retrieve a single service body by id.
     * Retrieves a service body
     * @param serviceBodyId ID of service body
     */
    public getServiceBodyWithHttpInfo(serviceBodyId: number, _options?: ConfigurationOptions): Observable<HttpInfo<ServiceBody>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getServiceBody(serviceBodyId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getServiceBodyWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve a single service body by id.
     * Retrieves a service body
     * @param serviceBodyId ID of service body
     */
    public getServiceBody(serviceBodyId: number, _options?: ConfigurationOptions): Observable<ServiceBody> {
        return this.getServiceBodyWithHttpInfo(serviceBodyId, _options).pipe(map((apiResponse: HttpInfo<ServiceBody>) => apiResponse.data));
    }

    /**
     * Retrieve single user.
     * Retrieves a single user
     * @param userId ID of user
     */
    public getUserWithHttpInfo(userId: number, _options?: ConfigurationOptions): Observable<HttpInfo<User>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getUser(userId, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getUserWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve single user.
     * Retrieves a single user
     * @param userId ID of user
     */
    public getUser(userId: number, _options?: ConfigurationOptions): Observable<User> {
        return this.getUserWithHttpInfo(userId, _options).pipe(map((apiResponse: HttpInfo<User>) => apiResponse.data));
    }

    /**
     * Retrieve users for authenticated user.
     * Retrieves users
     */
    public getUsersWithHttpInfo(_options?: ConfigurationOptions): Observable<HttpInfo<Array<User>>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.getUsers(_config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.getUsersWithHttpInfo(rsp)));
            }));
    }

    /**
     * Retrieve users for authenticated user.
     * Retrieves users
     */
    public getUsers(_options?: ConfigurationOptions): Observable<Array<User>> {
        return this.getUsersWithHttpInfo(_options).pipe(map((apiResponse: HttpInfo<Array<User>>) => apiResponse.data));
    }

    /**
     * Patches a user by id.
     * Patches a user
     * @param userId ID of user
     * @param userPartialUpdate Pass in fields you want to update.
     */
    public partialUpdateUserWithHttpInfo(userId: number, userPartialUpdate: UserPartialUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.partialUpdateUser(userId, userPartialUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.partialUpdateUserWithHttpInfo(rsp)));
            }));
    }

    /**
     * Patches a user by id.
     * Patches a user
     * @param userId ID of user
     * @param userPartialUpdate Pass in fields you want to update.
     */
    public partialUpdateUser(userId: number, userPartialUpdate: UserPartialUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.partialUpdateUserWithHttpInfo(userId, userPartialUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Patches a single format by id.
     * Patches a format
     * @param formatId ID of format
     * @param formatPartialUpdate Pass in fields you want to update.
     */
    public patchFormatWithHttpInfo(formatId: number, formatPartialUpdate: FormatPartialUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.patchFormat(formatId, formatPartialUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.patchFormatWithHttpInfo(rsp)));
            }));
    }

    /**
     * Patches a single format by id.
     * Patches a format
     * @param formatId ID of format
     * @param formatPartialUpdate Pass in fields you want to update.
     */
    public patchFormat(formatId: number, formatPartialUpdate: FormatPartialUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.patchFormatWithHttpInfo(formatId, formatPartialUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Patches a meeting by id
     * Patches a meeting
     * @param meetingId ID of meeting
     * @param meetingPartialUpdate Pass in fields you want to update.
     * @param [skipVenueTypeLocationValidation] specify true to skip venue type location validation
     */
    public patchMeetingWithHttpInfo(meetingId: number, meetingPartialUpdate: MeetingPartialUpdate, skipVenueTypeLocationValidation?: boolean, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.patchMeeting(meetingId, meetingPartialUpdate, skipVenueTypeLocationValidation, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.patchMeetingWithHttpInfo(rsp)));
            }));
    }

    /**
     * Patches a meeting by id
     * Patches a meeting
     * @param meetingId ID of meeting
     * @param meetingPartialUpdate Pass in fields you want to update.
     * @param [skipVenueTypeLocationValidation] specify true to skip venue type location validation
     */
    public patchMeeting(meetingId: number, meetingPartialUpdate: MeetingPartialUpdate, skipVenueTypeLocationValidation?: boolean, _options?: ConfigurationOptions): Observable<void> {
        return this.patchMeetingWithHttpInfo(meetingId, meetingPartialUpdate, skipVenueTypeLocationValidation, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Patches a single service body by id.
     * Patches a service body
     * @param serviceBodyId ID of service body
     * @param serviceBodyPartialUpdate Pass in fields you want to update.
     */
    public patchServiceBodyWithHttpInfo(serviceBodyId: number, serviceBodyPartialUpdate: ServiceBodyPartialUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.patchServiceBody(serviceBodyId, serviceBodyPartialUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.patchServiceBodyWithHttpInfo(rsp)));
            }));
    }

    /**
     * Patches a single service body by id.
     * Patches a service body
     * @param serviceBodyId ID of service body
     * @param serviceBodyPartialUpdate Pass in fields you want to update.
     */
    public patchServiceBody(serviceBodyId: number, serviceBodyPartialUpdate: ServiceBodyPartialUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.patchServiceBodyWithHttpInfo(serviceBodyId, serviceBodyPartialUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Updates a format.
     * Updates a format
     * @param formatId ID of format
     * @param formatUpdate Pass in format object
     */
    public updateFormatWithHttpInfo(formatId: number, formatUpdate: FormatUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.updateFormat(formatId, formatUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.updateFormatWithHttpInfo(rsp)));
            }));
    }

    /**
     * Updates a format.
     * Updates a format
     * @param formatId ID of format
     * @param formatUpdate Pass in format object
     */
    public updateFormat(formatId: number, formatUpdate: FormatUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.updateFormatWithHttpInfo(formatId, formatUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Updates a meeting.
     * Updates a meeting
     * @param meetingId ID of meeting
     * @param meetingUpdate Pass in meeting object
     */
    public updateMeetingWithHttpInfo(meetingId: number, meetingUpdate: MeetingUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.updateMeeting(meetingId, meetingUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.updateMeetingWithHttpInfo(rsp)));
            }));
    }

    /**
     * Updates a meeting.
     * Updates a meeting
     * @param meetingId ID of meeting
     * @param meetingUpdate Pass in meeting object
     */
    public updateMeeting(meetingId: number, meetingUpdate: MeetingUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.updateMeetingWithHttpInfo(meetingId, meetingUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Updates a single service body.
     * Updates a Service Body
     * @param serviceBodyId ID of service body
     * @param serviceBodyUpdate Pass in service body object
     */
    public updateServiceBodyWithHttpInfo(serviceBodyId: number, serviceBodyUpdate: ServiceBodyUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.updateServiceBody(serviceBodyId, serviceBodyUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.updateServiceBodyWithHttpInfo(rsp)));
            }));
    }

    /**
     * Updates a single service body.
     * Updates a Service Body
     * @param serviceBodyId ID of service body
     * @param serviceBodyUpdate Pass in service body object
     */
    public updateServiceBody(serviceBodyId: number, serviceBodyUpdate: ServiceBodyUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.updateServiceBodyWithHttpInfo(serviceBodyId, serviceBodyUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

    /**
     * Updates a user.
     * Update single user
     * @param userId ID of user
     * @param userUpdate Pass in user object
     */
    public updateUserWithHttpInfo(userId: number, userUpdate: UserUpdate, _options?: ConfigurationOptions): Observable<HttpInfo<void>> {
        const _config = mergeConfiguration(this.configuration, _options);

        const requestContextPromise = this.requestFactory.updateUser(userId, userUpdate, _config);
        // build promise chain
        let middlewarePreObservable = from<RequestContext>(requestContextPromise);
        for (const middleware of _config.middleware) {
            middlewarePreObservable = middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => middleware.pre(ctx)));
        }

        return middlewarePreObservable.pipe(mergeMap((ctx: RequestContext) => _config.httpApi.send(ctx))).
            pipe(mergeMap((response: ResponseContext) => {
                let middlewarePostObservable = of(response);
                for (const middleware of _config.middleware.reverse()) {
                    middlewarePostObservable = middlewarePostObservable.pipe(mergeMap((rsp: ResponseContext) => middleware.post(rsp)));
                }
                return middlewarePostObservable.pipe(map((rsp: ResponseContext) => this.responseProcessor.updateUserWithHttpInfo(rsp)));
            }));
    }

    /**
     * Updates a user.
     * Update single user
     * @param userId ID of user
     * @param userUpdate Pass in user object
     */
    public updateUser(userId: number, userUpdate: UserUpdate, _options?: ConfigurationOptions): Observable<void> {
        return this.updateUserWithHttpInfo(userId, userUpdate, _options).pipe(map((apiResponse: HttpInfo<void>) => apiResponse.data));
    }

}
