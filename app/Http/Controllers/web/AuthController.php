<?php

namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\GroupService;
use App\Services\InvitationService;

class AuthController extends Controller
{
    protected $authService,$groupService,$invitationService;

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
        $groups = null;
        $status="groups";
        $invitationRequests = null;
        return view('home', ['groups' => $groups,'status'=>$status,'invitationRequests'=>$invitationRequests]);
    }

    public function registerAdmin(RegisterRequest $request)
    {
        $this->authService->register(array_merge($request->validated(), ['role' => 'admin']));
        $groups = null;
        $status="groups";
        $invitationRequests = null;
        return view('home', ['groups' => $groups,'status'=>$status,'invitationRequests'=>$invitationRequests]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return view('login')->with('success', 'Logged out successfully');
    }

    public function login(LoginRequest $request)
    {
        $authData = $this->authService->login($request->validated());
        if ($authData) {
            $groups = null;
            $status="groups";
            $userId=Auth::user()->id;
            $invitationRequests = $this->invitationService->getUserInvitations($userId);
            return view('home', ['groups' => $groups,'status'=>$status,'invitationRequests'=>$invitationRequests]);
        }
        else {
            return view('login')->with('error', 'Invalid credentials');
        }
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
