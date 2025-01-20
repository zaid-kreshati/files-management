<?php

namespace App\Http\Controllers\web;

use Illuminate\Contracts\View\View;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GroupService;
use App\Services\InvitationService;

class AuthController extends Controller
{
    protected InvitationService $invitationService;
    protected GroupService $groupService;
    protected AuthService $authService;

    public function __construct(AuthService $authService, GroupService $groupService, InvitationService $invitationService)
    {
        $this->authService = $authService;
        $this->groupService = $groupService;
        $this->invitationService = $invitationService;
    }

    public function registerForm(): View
    {
        return view('register');
    }

    public function registerAdmin(RegisterRequest $request): View
    {
        return $this->registerUser($request->validated(), 'admin');
    }

    public function registerClient(RegisterRequest $request): View
    {
        return $this->registerUser($request->validated(), 'user');
    }

    /**
     * Common method for registering users.
     */
    private function registerUser(array $validatedData, string $role): View
    {
        $this->authService->register(array_merge($validatedData, ['role' => $role]));
        return view('login');
    }

    public function logout(): View
    {
        Auth::logout();
        return view('login')->with('success', 'Logged out successfully');
    }

    public function login(LoginRequest $request): View
    {
        $authData = $this->authService->login($request->validated());
        if ($authData) {
            $viewData = $this->prepareHomeViewData();
            return view('home', $viewData);
        }
        return view('login')->with('error', 'Invalid credentials');
    }

    public function home(): View
    {
        $viewData = $this->prepareHomeViewData();
        return view('home', $viewData);
    }

    /**
     * Prepare data for the 'home' view.
     */
    private function prepareHomeViewData(): array
    {
        $userId = Auth::id();
        return [
            'groups' => null,
            'status' => 'groups',
            'invitationRequests' => $this->invitationService->getUserInvitations($userId),
        ];
    }


}
