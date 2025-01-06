<?php

namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  // Change this import
use Illuminate\Support\Facades\Auth;
use App\Services\GroupService;
use App\Services\InvitationService;
use Illuminate\Support\Collection;
use App\Models\User;




class AuthController extends Controller
{
    protected $authService;
    protected $groupService;
    protected $invitationService;


    public function __construct(AuthService $authService, GroupService $groupService,InvitationService $invitationService)
    {
        $this->authService = $authService;
        $this->groupService=$groupService;
        $this->invitationService=$invitationService;
    }

    public function registerForm()
    {
        return view('register');
    }

    public function registerClient(RegisterRequest $request)
    {
        $this->authService->register(array_merge($request->validated(), ['role' => 'user']));
        return view('login');
    }

    public function registerAdmin(RegisterRequest $request)
    {
        $user = $this->authService->register(array_merge($request->validated(), ['role' => 'admin']));
        return response()->json(['user' => $user], 201);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return view('login')->with('success', 'Logged out successfully');
    }



    public function login(LoginRequest $request)
    {
        $authData = $this->authService->login($request->validated());
        $groups = null;
        $status="groups";
        $userId=Auth::user()->id;
        $invitationRequests = $this->invitationService->getUserInvitations($userId);


        if ($authData) {
            return view('home', ['groups' => $groups,'status'=>$status,'invitationRequests'=>$invitationRequests]);
        }
        else {
            return view('login')->with('error', 'Invalid credentials');
        }
    }

    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return $token;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function home(Request $request)
    {
        $groups = null;
        $status="groups";
        $userId=Auth::user()->id;
        $invitationRequests = $this->invitationService->getUserInvitations($userId);
        return view('home', ['groups' => $groups,'status'=>$status,'invitationRequests'=>$invitationRequests]);
    }

}
