<?php

namespace App\Http\Controllers\Legacy;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CatchAllController;
use App\Interfaces\ServiceBodyRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LegacyAdminController extends Controller
{
    private int $REQUEST_TYPE_ADMIN_XML = 1;
    private int $REQUEST_TYPE_ADMIN_JSON = 2;
    private int $REQUEST_TYPE_WEB = 3;

    private ServiceBodyRepositoryInterface $serviceBodyRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(ServiceBodyRepositoryInterface $serviceBodyRepository, UserRepositoryInterface $userRepository)
    {
        $this->serviceBodyRepository = $serviceBodyRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request)
    {
        $adminAction = $request->input('admin_action');
        if ($adminAction == 'login') {
            return $this->handleLogin($request);
        } elseif ($adminAction == 'logout') {
            return $this->handleLogout($request);
        } elseif ($adminAction == 'get_permissions') {
            return $this->handleGetPermissions($request);
        } elseif ($adminAction == 'get_user_info') {
            return $this->handleGetUserInfo($request);
        }

        return CatchAllController::handle($request);
    }

    private function handleLogin(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $username = $request->input('c_comdef_admin_login');
        $password = $request->input('c_comdef_admin_password');
        $credentials = ['login_string' => $username, 'password' => $password];
        $success = Auth::attempt($credentials);
        if ($success) {
            if (Hash::needsRehash($request->user()->password_string)) {
                $this->userRepository->updatePassword($request->user()->id_bigint, $password);
            }

            $apiType = $this->getApiType($request);
            if ($apiType != $this->REQUEST_TYPE_WEB) {
                $user = Auth::user();
                if ($user) {
                    if ($user->user_level_tinyint == User::USER_LEVEL_ADMIN || $user->user_level_tinyint == User::USER_LEVEL_DEACTIVATED) {
                        Auth::logout();
                        $request->session()->invalidate();
                        $success = false;
                    }
                }
            }
        }
        return $success
            ? $this->loggedInResponse($request)
            : $this->badCredentialsResponse($request);
    }

    private function handleLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return $this->logoutResponse($request);
    }

    private function handleGetPermissions(Request $request)
    {
        $apiType = $this->getApiType($request);
        if ($apiType != $this->REQUEST_TYPE_ADMIN_JSON) {
            abort(404);
        }

        $user = $request->user();
        if (!$user) {
            return $this->badCredentialsResponse($request);
        }

        $serviceBodyIds = $this->serviceBodyRepository->getAssignedServiceBodyIds($user->id_bigint)->toArray();
        $serviceBodies = empty($serviceBodyIds) ? [] : $this->serviceBodyRepository->search(includeIds: $serviceBodyIds)
            ->map(fn ($sb) => [
                'id' => $sb->id_bigint,
                'name' => $sb->name_string,
                'permissions' => 3
            ])
            ->toArray();

        return response(['service_body' => $serviceBodies])->header('Content-Type', 'application/json');
    }

    private function handleGetUserInfo(Request $request)
    {
        $apiType = $this->getApiType($request);
        if ($apiType != $this->REQUEST_TYPE_ADMIN_JSON) {
            abort(404);
        }

        $user = $request->user();
        if (!$user) {
            return $this->badCredentialsResponse($request);
        }

        $response = [
            'current_user' => [
                'id' => $user->id_bigint,
                'name' => $user->name_string,
                'type' => $user->user_level_tinyint
            ]
        ];
        return response($response)->header('Content-Type', 'application/json');
    }

    private function loggedInResponse($request)
    {
        $apiType = $this->getApiType($request);
        if ($apiType == $this->REQUEST_TYPE_ADMIN_XML) {
            return response('OK');
        } elseif ($apiType == $this->REQUEST_TYPE_ADMIN_JSON) {
            return response('OK');
        } else {
            $redirectUrl = $request->input('attemptedurl', '/');
            if (str_contains($redirectUrl, 'bad_login_form')) {
                $redirectUrl = '/';
            }
            $response = redirect($redirectUrl);
            $languagePref = $request->input('lang_enum');
            if ($languagePref) {
                $response = $response->withCookie(cookie('bmlt_admin_lang_pref', $languagePref, 60 * 24 * 365));
            }
            return $response;
        }
    }

    private function badCredentialsResponse($request)
    {
        $apiType = $this->getApiType($request);
        if ($apiType == $this->REQUEST_TYPE_ADMIN_XML) {
            return response('<h1>NOT AUTHORIZED</h1>');
        } elseif ($apiType == $this->REQUEST_TYPE_ADMIN_JSON) {
            return response('NOT AUTHORIZED');
        } else {
            $response = redirect('/?bad_login_form=1');
            $languagePref = $request->input('lang_enum');
            if ($languagePref) {
                $response = $response->withCookie(cookie('bmlt_admin_lang_pref', $languagePref, 60 * 24 * 365));
            }
            return $response;
        }
    }

    private function logoutResponse($request)
    {
        $apiType = $this->getApiType($request);
        if ($apiType == $this->REQUEST_TYPE_ADMIN_XML) {
            return response('BYE');
        } elseif ($apiType == $this->REQUEST_TYPE_ADMIN_JSON) {
            return response('BYE');
        } else {
            return redirect('/');
        }
    }

    private function getApiType(Request $request): int
    {
        $pathInfo = LegacyPathInfo::parse($request);
        if (str_ends_with($pathInfo->path, '/server_admin/xml.php')) {
            return $this->REQUEST_TYPE_ADMIN_XML;
        } elseif (str_ends_with($pathInfo->path, '/server_admin/json.php')) {
            return $this->REQUEST_TYPE_ADMIN_JSON;
        } else {
            return $this->REQUEST_TYPE_WEB;
        }
    }
}
