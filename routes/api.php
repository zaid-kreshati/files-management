<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController_api;
use App\Http\Middleware\ValidateToken;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\checkOutController;
use App\Http\Controllers\api\ChecksFilesController;
use App\Http\Controllers\api\InvitationController;
use App\Http\Controllers\api\FileController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth
Route::controller(AuthController::class)->group(function () {
    Route::post('register/user', 'registerClient');
    Route::post('register/admin', 'registerAdmin');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

// Group
Route::controller(GroupController::class)->group(function () {
    Route::post('groups/create', 'store');
    Route::delete('groups/delete/{id}', 'destroy');
    Route::get('groups/show/{id}', 'show');
    Route::get('groups/all', 'index');
    Route::get('groups/{groupId}/members', 'getGroupMembers');
});

// Invitaiton to group
Route::controller(InvitationController::class)->group(function () {
    Route::post('groups/{groupId}/invite/{user_id}', 'sendInvitation');
    Route::post('groups/{groupId}/invite', 'sendBulkInvitations');
    Route::post('/invitations/{invitation}/respond', 'respondToInvitation');
    Route::get('/invitations', 'getUserInvitations');
});

//Files
Route::controller(FileController::class)->group(function () {
    Route::post('/{groupId}/files/upload', 'uploadFile');
    Route::get('/{groupId}/files/pending', 'getFilesForApproval');
    Route::post('/file/{fileId}/', 'approveFile');
    Route::get('/{groupId}/files/all', 'getApprovedFiles');
});

// Check in file
Route::controller(ChecksFilesController::class)->group(function () {
    Route::post('/files/checkIn', 'checkInFiles');
    Route::post('files/checkout/{groupId}/file', 'replaceFile');
});

// Check out file
Route::controller(checkOutController::class)->group(function () {
    Route::post('files/checkout/{groupId}/file', 'replaceFile');
});

