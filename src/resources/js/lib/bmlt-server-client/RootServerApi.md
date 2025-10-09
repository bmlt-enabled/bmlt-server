# .RootServerApi

All URIs are relative to *http://localhost:8000/main_server*

Method | HTTP request | Description
------------- | ------------- | -------------
[**authLogout**](RootServerApi.md#authLogout) | **POST** /api/v1/auth/logout | Revokes a token
[**authRefresh**](RootServerApi.md#authRefresh) | **POST** /api/v1/auth/refresh | Revokes and issues a new token
[**authToken**](RootServerApi.md#authToken) | **POST** /api/v1/auth/token | Creates a token
[**createErrorTest**](RootServerApi.md#createErrorTest) | **POST** /api/v1/errortest | Tests some errors
[**createFormat**](RootServerApi.md#createFormat) | **POST** /api/v1/formats | Creates a format
[**createMeeting**](RootServerApi.md#createMeeting) | **POST** /api/v1/meetings | Creates a meeting
[**createServiceBody**](RootServerApi.md#createServiceBody) | **POST** /api/v1/servicebodies | Creates a service body
[**createUser**](RootServerApi.md#createUser) | **POST** /api/v1/users | Creates a user
[**deleteFormat**](RootServerApi.md#deleteFormat) | **DELETE** /api/v1/formats/{formatId} | Deletes a format
[**deleteMeeting**](RootServerApi.md#deleteMeeting) | **DELETE** /api/v1/meetings/{meetingId} | Deletes a meeting
[**deleteServiceBody**](RootServerApi.md#deleteServiceBody) | **DELETE** /api/v1/servicebodies/{serviceBodyId} | Deletes a service body
[**deleteUser**](RootServerApi.md#deleteUser) | **DELETE** /api/v1/users/{userId} | Deletes a user
[**getFormat**](RootServerApi.md#getFormat) | **GET** /api/v1/formats/{formatId} | Retrieves a format
[**getFormats**](RootServerApi.md#getFormats) | **GET** /api/v1/formats | Retrieves formats
[**getLaravelLog**](RootServerApi.md#getLaravelLog) | **GET** /api/v1/logs/laravel | Retrieves laravel log
[**getMeeting**](RootServerApi.md#getMeeting) | **GET** /api/v1/meetings/{meetingId} | Retrieves a meeting
[**getMeetingChanges**](RootServerApi.md#getMeetingChanges) | **GET** /api/v1/meetings/{meetingId}/changes | Retrieve changes for a meeting
[**getMeetings**](RootServerApi.md#getMeetings) | **GET** /api/v1/meetings | Retrieves meetings
[**getRootServer**](RootServerApi.md#getRootServer) | **GET** /api/v1/rootservers/{rootServerId} | Retrieves a root server
[**getRootServers**](RootServerApi.md#getRootServers) | **GET** /api/v1/rootservers | Retrieves root servers
[**getServiceBodies**](RootServerApi.md#getServiceBodies) | **GET** /api/v1/servicebodies | Retrieves service bodies
[**getServiceBody**](RootServerApi.md#getServiceBody) | **GET** /api/v1/servicebodies/{serviceBodyId} | Retrieves a service body
[**getUser**](RootServerApi.md#getUser) | **GET** /api/v1/users/{userId} | Retrieves a single user
[**getUsers**](RootServerApi.md#getUsers) | **GET** /api/v1/users | Retrieves users
[**partialUpdateUser**](RootServerApi.md#partialUpdateUser) | **PATCH** /api/v1/users/{userId} | Patches a user
[**patchFormat**](RootServerApi.md#patchFormat) | **PATCH** /api/v1/formats/{formatId} | Patches a format
[**patchMeeting**](RootServerApi.md#patchMeeting) | **PATCH** /api/v1/meetings/{meetingId} | Patches a meeting
[**patchServiceBody**](RootServerApi.md#patchServiceBody) | **PATCH** /api/v1/servicebodies/{serviceBodyId} | Patches a service body
[**updateFormat**](RootServerApi.md#updateFormat) | **PUT** /api/v1/formats/{formatId} | Updates a format
[**updateMeeting**](RootServerApi.md#updateMeeting) | **PUT** /api/v1/meetings/{meetingId} | Updates a meeting
[**updateServiceBody**](RootServerApi.md#updateServiceBody) | **PUT** /api/v1/servicebodies/{serviceBodyId} | Updates a Service Body
[**updateUser**](RootServerApi.md#updateUser) | **PUT** /api/v1/users/{userId} | Update single user


# **authLogout**
> void authLogout()

Revoke token and logout.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.authLogout(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when token was logged out. |  -  |
**401** | Returns when request is unauthenticated. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **authRefresh**
> Token authRefresh()

Refresh token.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.authRefresh(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**Token**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when refresh is successful. |  -  |
**401** | Returns when request is unauthenticated. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **authToken**
> Token authToken(tokenCredentials)

Exchange credentials for a new token

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiAuthTokenRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiAuthTokenRequest = {
    // User credentials
  tokenCredentials: {
    username: "MyUsername",
    password: "PassWord12345",
  },
};

const data = await apiInstance.authToken(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tokenCredentials** | **TokenCredentials**| User credentials |


### Return type

**Token**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when POST is successful. |  -  |
**401** | Returns when credentials are incorrect. |  -  |
**403** | Returns when unauthorized. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **createErrorTest**
> ErrorTest createErrorTest(errorTest)

Tests some errors.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiCreateErrorTestRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiCreateErrorTestRequest = {
    // Pass in error test object.
  errorTest: {
    arbitraryString: "string",
    arbitraryInt: 123,
    forceServerError: true,
  },
};

const data = await apiInstance.createErrorTest(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **errorTest** | **ErrorTest**| Pass in error test object. |


### Return type

**ErrorTest**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Returns when POST is successful. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**422** | Validation error. |  -  |
**500** | Server error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **createFormat**
> Format createFormat(formatCreate)

Creates a format.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiCreateFormatRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiCreateFormatRequest = {
    // Pass in format object
  formatCreate: null,
};

const data = await apiInstance.createFormat(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **formatCreate** | **FormatCreate**| Pass in format object |


### Return type

**Format**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Returns when POST is successful. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no format exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **createMeeting**
> Meeting createMeeting(meetingCreate)

Creates a meeting.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiCreateMeetingRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiCreateMeetingRequest = {
    // Pass in meeting object
  meetingCreate: null,
};

const data = await apiInstance.createMeeting(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingCreate** | **MeetingCreate**| Pass in meeting object |


### Return type

**Meeting**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Returns when POST is successful. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no meeting body exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **createServiceBody**
> ServiceBody createServiceBody(serviceBodyCreate)

Creates a service body.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiCreateServiceBodyRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiCreateServiceBodyRequest = {
    // Pass in service body object
  serviceBodyCreate: null,
};

const data = await apiInstance.createServiceBody(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceBodyCreate** | **ServiceBodyCreate**| Pass in service body object |


### Return type

**ServiceBody**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Returns when POST is successful. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no service body exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **createUser**
> User createUser(userCreate)

Creates a user.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiCreateUserRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiCreateUserRequest = {
    // Pass in user object
  userCreate: ,
};

const data = await apiInstance.createUser(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **userCreate** | **UserCreate**| Pass in user object |


### Return type

**User**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Returns when POST is successful. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no user exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **deleteFormat**
> void deleteFormat()

Deletes a format by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiDeleteFormatRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiDeleteFormatRequest = {
    // ID of format
  formatId: 1,
};

const data = await apiInstance.deleteFormat(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **formatId** | [**number**] | ID of format | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when not authenticated |  -  |
**403** | Returns when unauthorized |  -  |
**404** | Returns when no format exists. |  -  |
**409** | Returns when format has meetings assigned. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **deleteMeeting**
> void deleteMeeting()

Deletes a meeting by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiDeleteMeetingRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiDeleteMeetingRequest = {
    // ID of meeting
  meetingId: 1,
};

const data = await apiInstance.deleteMeeting(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingId** | [**number**] | ID of meeting | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no meeting exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **deleteServiceBody**
> void deleteServiceBody()

Deletes a service body by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiDeleteServiceBodyRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiDeleteServiceBodyRequest = {
    // ID of service body
  serviceBodyId: 1,
};

const data = await apiInstance.deleteServiceBody(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceBodyId** | [**number**] | ID of service body | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no service body exists. |  -  |
**409** | Returns when service body has children. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **deleteUser**
> void deleteUser()

Deletes a user by id

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiDeleteUserRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiDeleteUserRequest = {
    // ID of user
  userId: 1,
};

const data = await apiInstance.deleteUser(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **userId** | [**number**] | ID of user | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when not authenticated |  -  |
**403** | Returns when unauthorized |  -  |
**404** | Returns when no user exists. |  -  |
**409** | Returns when user is still referenced by service bodies. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getFormat**
> Format getFormat()

Retrieve a format

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetFormatRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetFormatRequest = {
    // ID of format
  formatId: 1,
};

const data = await apiInstance.getFormat(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **formatId** | [**number**] | ID of format | defaults to undefined


### Return type

**Format**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when not authenticated. |  -  |
**404** | Returns when no format exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getFormats**
> Array<Format> getFormats()

Retrieve formats

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.getFormats(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**Array<Format>**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when not authenticated |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getLaravelLog**
> HttpFile getLaravelLog()

Retrieve the laravel log if it exists.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.getLaravelLog(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**HttpFile**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/gzip, application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no laravel log file exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getMeeting**
> Meeting getMeeting()

Retrieve a meeting.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetMeetingRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetMeetingRequest = {
    // ID of meeting
  meetingId: 1,
};

const data = await apiInstance.getMeeting(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingId** | [**number**] | ID of meeting | defaults to undefined


### Return type

**Meeting**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**404** | Returns when no meeting exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getMeetingChanges**
> Array<MeetingChangeResource> getMeetingChanges()

Retrieve all changes made to a specific meeting.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetMeetingChangesRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetMeetingChangesRequest = {
    // ID of the meeting
  meetingId: 1,
};

const data = await apiInstance.getMeetingChanges(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingId** | [**number**] | ID of the meeting | defaults to undefined


### Return type

**Array<MeetingChangeResource>**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | List of changes for the meeting. |  -  |
**401** | Unauthorized. |  -  |
**403** | Returns when unauthorized |  -  |
**404** | Meeting not found. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getMeetings**
> Array<Meeting> getMeetings()

Retrieve meetings for authenticated user.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetMeetingsRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetMeetingsRequest = {
    // comma delimited meeting ids (optional)
  meetingIds: "1,2",
    // comma delimited day ids between 0-6 (optional)
  days: "0,1",
    // comma delimited service body ids (optional)
  serviceBodyIds: "3,4",
    // string (optional)
  searchString: "Just for Today",
};

const data = await apiInstance.getMeetings(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingIds** | [**string**] | comma delimited meeting ids | (optional) defaults to undefined
 **days** | [**string**] | comma delimited day ids between 0-6 | (optional) defaults to undefined
 **serviceBodyIds** | [**string**] | comma delimited service body ids | (optional) defaults to undefined
 **searchString** | [**string**] | string | (optional) defaults to undefined


### Return type

**Array<Meeting>**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | List of meetings. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getRootServer**
> RootServer getRootServer()

Retrieve a single root server id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetRootServerRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetRootServerRequest = {
    // ID of root server
  rootServerId: 1,
};

const data = await apiInstance.getRootServer(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **rootServerId** | [**number**] | ID of root server | defaults to undefined


### Return type

**RootServer**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Successful response. |  -  |
**404** | Returns when no root server exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getRootServers**
> Array<RootServer> getRootServers()

Retrieve root servers.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.getRootServers(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**Array<RootServer>**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Successful response. |  -  |
**404** | Returns when aggregator mode is disabled. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getServiceBodies**
> Array<ServiceBody> getServiceBodies()

Retrieve service bodies for authenticated user.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.getServiceBodies(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**Array<ServiceBody>**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when not authenticated. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getServiceBody**
> ServiceBody getServiceBody()

Retrieve a single service body by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetServiceBodyRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetServiceBodyRequest = {
    // ID of service body
  serviceBodyId: 1,
};

const data = await apiInstance.getServiceBody(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceBodyId** | [**number**] | ID of service body | defaults to undefined


### Return type

**ServiceBody**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**404** | Returns when no service body exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getUser**
> User getUser()

Retrieve single user.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiGetUserRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiGetUserRequest = {
    // ID of user
  userId: 1,
};

const data = await apiInstance.getUser(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **userId** | [**number**] | ID of user | defaults to undefined


### Return type

**User**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when not authenticated. |  -  |
**404** | Returns when no user exists. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **getUsers**
> Array<User> getUsers()

Retrieve users for authenticated user.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request = {};

const data = await apiInstance.getUsers(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters
This endpoint does not need any parameter.


### Return type

**Array<User>**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Returns when user is authenticated. |  -  |
**401** | Returns when not authenticated |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **partialUpdateUser**
> void partialUpdateUser(userPartialUpdate)

Patches a user by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiPartialUpdateUserRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiPartialUpdateUserRequest = {
    // ID of user
  userId: 1,
    // Pass in fields you want to update.
  userPartialUpdate: ,
};

const data = await apiInstance.partialUpdateUser(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **userPartialUpdate** | **UserPartialUpdate**| Pass in fields you want to update. |
 **userId** | [**number**] | ID of user | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when not authenticated |  -  |
**403** | Returns when unauthorized |  -  |
**404** | Returns when no user exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **patchFormat**
> void patchFormat(formatPartialUpdate)

Patches a single format by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiPatchFormatRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiPatchFormatRequest = {
    // ID of format
  formatId: 1,
    // Pass in fields you want to update.
  formatPartialUpdate: null,
};

const data = await apiInstance.patchFormat(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **formatPartialUpdate** | **FormatPartialUpdate**| Pass in fields you want to update. |
 **formatId** | [**number**] | ID of format | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when not authenticated |  -  |
**403** | Returns when unauthorized |  -  |
**404** | Returns when no format exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **patchMeeting**
> void patchMeeting(meetingPartialUpdate)

Patches a meeting by id

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiPatchMeetingRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiPatchMeetingRequest = {
    // ID of meeting
  meetingId: 1,
    // Pass in fields you want to update.
  meetingPartialUpdate: null,
    // specify true to skip venue type location validation (optional)
  skipVenueTypeLocationValidation: true,
};

const data = await apiInstance.patchMeeting(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingPartialUpdate** | **MeetingPartialUpdate**| Pass in fields you want to update. |
 **meetingId** | [**number**] | ID of meeting | defaults to undefined
 **skipVenueTypeLocationValidation** | [**boolean**] | specify true to skip venue type location validation | (optional) defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no meeting exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **patchServiceBody**
> void patchServiceBody(serviceBodyPartialUpdate)

Patches a single service body by id.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiPatchServiceBodyRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiPatchServiceBodyRequest = {
    // ID of service body
  serviceBodyId: 1,
    // Pass in fields you want to update.
  serviceBodyPartialUpdate: null,
};

const data = await apiInstance.patchServiceBody(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceBodyPartialUpdate** | **ServiceBodyPartialUpdate**| Pass in fields you want to update. |
 **serviceBodyId** | [**number**] | ID of service body | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no service body exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **updateFormat**
> void updateFormat(formatUpdate)

Updates a format.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiUpdateFormatRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiUpdateFormatRequest = {
    // ID of format
  formatId: 1,
    // Pass in format object
  formatUpdate: null,
};

const data = await apiInstance.updateFormat(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **formatUpdate** | **FormatUpdate**| Pass in format object |
 **formatId** | [**number**] | ID of format | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no format exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **updateMeeting**
> void updateMeeting(meetingUpdate)

Updates a meeting.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiUpdateMeetingRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiUpdateMeetingRequest = {
    // ID of meeting
  meetingId: 1,
    // Pass in meeting object
  meetingUpdate: null,
};

const data = await apiInstance.updateMeeting(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **meetingUpdate** | **MeetingUpdate**| Pass in meeting object |
 **meetingId** | [**number**] | ID of meeting | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no meeting exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **updateServiceBody**
> void updateServiceBody(serviceBodyUpdate)

Updates a single service body.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiUpdateServiceBodyRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiUpdateServiceBodyRequest = {
    // ID of service body
  serviceBodyId: 1,
    // Pass in service body object
  serviceBodyUpdate: null,
};

const data = await apiInstance.updateServiceBody(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **serviceBodyUpdate** | **ServiceBodyUpdate**| Pass in service body object |
 **serviceBodyId** | [**number**] | ID of service body | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no service body exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)

# **updateUser**
> void updateUser(userUpdate)

Updates a user.

### Example


```typescript
import { createConfiguration, RootServerApi } from '';
import type { RootServerApiUpdateUserRequest } from '';

const configuration = createConfiguration();
const apiInstance = new RootServerApi(configuration);

const request: RootServerApiUpdateUserRequest = {
    // ID of user
  userId: 1,
    // Pass in user object
  userUpdate: ,
};

const data = await apiInstance.updateUser(request);
console.log('API called successfully. Returned data:', data);
```


### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **userUpdate** | **UserUpdate**| Pass in user object |
 **userId** | [**number**] | ID of user | defaults to undefined


### Return type

**void**

### Authorization

[bmltToken](README.md#bmltToken)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Success. |  -  |
**401** | Returns when user is not authenticated. |  -  |
**403** | Returns when user is unauthorized to perform action. |  -  |
**404** | Returns when no user exists. |  -  |
**422** | Validation error. |  -  |

[[Back to top]](#) [[Back to API list]](README.md#documentation-for-api-endpoints) [[Back to Model list]](README.md#documentation-for-models) [[Back to README]](README.md)


