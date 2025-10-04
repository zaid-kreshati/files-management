<?php

namespace App\Http\Controllers\web;

use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\GroupService;
use App\Services\InvitationService;
use App\Services\FileService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Str;
use App\Models\RefreshToken;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use JsonResponseTrait;
    protected $authService, $groupService, $invitationService;

    public function __construct(AuthService $authService, GroupService $groupService, InvitationService $invitationService)
    {
        $this->authService = $authService;
        $this->groupService = $groupService;
        $this->invitationService = $invitationService;
    }

    public function registerForm()
    {
        return view('register');
    }

    public function registerAdmin(RegisterRequest $request)
    {
        $this->authService->register(array_merge($request->validated(), ['role' => 'admin']));
        $groups = null;
        $status = "groups";
        $invitationRequests = null;
        return view('home', ['groups' => $groups, 'status' => $status, 'invitationRequests' => $invitationRequests]);
    }


    public function registerClient(RegisterRequest $request)
    {
        $this->authService->register(array_merge($request->validated(), ['role' => 'user']));
        $groups = null;
        $status = "groups";
        $invitationRequests = null;
        return view('home', ['groups' => $groups, 'status' => $status, 'invitationRequests' => $invitationRequests]);
    }




    public function logout(Request $request)
    {
        Auth::logout();
        $refreshToken = $request->cookie('refresh_token');

        // if ($refreshToken) {
        //     RefreshToken::where('token', $refreshToken)->delete();
        // }
        return view('login')->with('success', 'Logged out successfully');
    }

    public function login(LoginRequest $request)
    {
        $authData = $this->authService->login($request->validated());
        if ($authData) {
            $groups = null;
            $status = "groups";
            $userId = Auth::user()->id;
            $invitationRequests = $this->invitationService->getUserInvitations($userId);
            return response()
                ->view('home', ['groups' => $groups,'status' => $status,'invitationRequests' => $invitationRequests,
                    'accessToken' => $authData['accessToken']
                ])->cookie(
                    'refresh_token', // Cookie name
                    $authData['refreshToken'], // Cookie value
                    60 * 24 * 7, // Expiration time in minutes (7 days)
                    null, // Path (default is '/')
                    null, // Domain (default is current domain)
                    true, // Secure (set to true for HTTPS only)
                    true  // HttpOnly (prevents JavaScript access to the cookie)
                );
        } else {
            return view('login')->with('error', 'Invalid credentials');
        }
    }

    public function home()
    {
        $groups = null;
        $status = "groups";
        $invitationRequests = null;
        return view('home', ['groups' => $groups, 'status' => $status, 'invitationRequests' => $invitationRequests]);
    }





    public function refreshToken(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        $storedToken = RefreshToken::where('token', $refreshToken)->first();


        if (!$storedToken || $storedToken->expires_at < now()) {
            return $this->errorResponse('Refresh token is invalid or expired', 401);
        }

        $user = $storedToken->user;

        $newAccessToken = $user->createToken('access-token')->plainTextToken;
        $newRefreshToken = Str::random(60);

        $storedToken->update(['token' => $newRefreshToken, 'expires_at' => now()->addDays(7)]);

        $response = [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ];
        return $this->successResponse($response, 'Token refreshed successfully', 200)
            ->cookie('refresh_token', $newRefreshToken, 60 * 24 * 7, null, null, true, true);
    }
}


