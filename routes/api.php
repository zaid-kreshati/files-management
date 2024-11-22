<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController_api;
use App\Http\Middleware\ValidateToken;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\checkOutController;
use App\Http\Controllers\ChecksFilesController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\FileController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register/user', [AuthController::class, 'registerClient']);
Route::post('register/admin', [AuthController::class, 'registerAdmin']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'groups'], function () {
        Route::post('/create', [GroupController::class, 'store'])->name('groups.store');
        Route::delete('/delete/{id}', [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::get('/show/{id}', [GroupController::class, 'show'])->name('groups.show');
        Route::get('/all', [GroupController::class, 'index'])->name('groups.index');

        // Invitaiton to group
        Route::post('/{groupId}/invite/{user_id}', [InvitationController::class, 'sendInvitation']);
        Route::post('/{groupId}/invite', [InvitationController::class, 'sendBulkInvitations']);

        // Get Memebers 
        Route::get('/{groupId}/members', [GroupController::class, 'getGroupMembers']);

        
        // Upload Files 
        Route::post('/{groupId}/files/upload', [FileController::class, 'uploadFile']);
        // get file for approved
        Route::get('/{groupId}/files/pending', [FileController::class, 'getFilesForApproval']);
        Route::post('/file/{fileId}/' , [FileController::class , 'approveFile']);
        // Get all Files in specific group
        Route::get('/{groupId}/files/all' , [FileController::class , 'getApprovedFiles']);

    });
});

// Invitaions Response
Route::post('/invitations/{invitation}/respond', [InvitationController::class, 'respondToInvitation'])->middleware('auth:sanctum');
Route::get('/invitations', [InvitationController::class, 'getUserInvitations'])->middleware('auth:sanctum');

// Check in file
Route::post('/files/checkIn' , [ChecksFilesController::class , 'checkInFiles'])->middleware('auth:sanctum');
Route::post('files/checkout/{groupId}/file' , [checkOutController::class , 'replaceFile'])->middleware('auth:sanctum');