<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\AuthController;
use App\Http\Controllers\web\GroupController;
use App\Http\Controllers\web\FileController;
use App\Http\Controllers\web\InvitationController;
use App\Http\Controllers\web\ChecksFilesController;
use App\Http\Controllers\web\checkOutController;




Route::get('/', function () {
    return view('login');
});
Route::get('/index', function () {
    return view('index');
});


Route::controller(AuthController::class)->group(function () {
    Route::get('/register/form', 'registerForm')->name('register.form');
    Route::post('/register/user', 'registerClient')->name('register.client');
    Route::post('/register/admin', 'registerAdmin')->name('register.admin');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/home', 'home')->name('home')->middleware('auth:sanctum');
    Route::post('/loginApi', 'loginApi')->name('loginApi');
});

Route::controller(GroupController::class)->prefix('groups')->name('groups')->group(function () {
    Route::post('/store', 'store')->name('store');
    Route::delete('{groupId}/delete', 'destroy')->name('destroy');
    Route::get('/show/{id}', 'show')->name('show');
    Route::get('/all', 'index')->name('index');
    Route::put('{groupId}/edit', 'update')->name('update');
    Route::post('/search', 'searchGroups')->name('search');
    Route::get('/all', 'getAllGroups')->name('all');
    Route::post('/allwithpagination', 'getAllGroupsWithPagination')->name('allwithpagination');
    Route::get('/check-owner/{groupId}',  'checkOwner')->name('checkOwner');

});

Route::controller(FileController::class)->prefix('files')->group(function(){

     // Upload Files
 Route::post('/{groupId}/upload',  'uploadFile');
 // get file for approved
 Route::get('/{groupId}/files/pending',  'getFilesForApproval');
 Route::post('/{fileId}/respond' , 'approveFile');
 // Get all Files in specific group
 Route::get('/{groupId}/all' , 'getApprovedFiles')->name('group.file');
 // Delete file
 Route::delete('/{fileId}/delete', 'deleteFile')->name('delete.file');
 // Show file
 Route::get('/{fileId}/open', 'openFile')->name('open.file')->middleware('auth:sanctum');
 // Open backup
 Route::get('/open-backup/{backupId}', 'openBackup')->name('open.backup');
 // Download file
 Route::get('/download/{fileId}', 'downloadFile')->name('download.file');
 // Restore backup
 Route::post('/restore-backup', 'restoreBackup')->name('restore.backup');



});

Route::controller(InvitationController::class)->prefix('group')->group(function(){

     // Invitation to group
     Route::post('/inviteuser', 'sendInvitation');
     //Route::get('/{groupId}/invite', 'sendBulkInvitations')->name('inviteAll');

     // Invitations Response

    Route::post('/invitations/{invitationId}/respond',  'respondToInvitation')->middleware('auth:sanctum');
    Route::get('/invitations',  'getUserInvitations')->middleware('auth:sanctum');

});

// Check in file
Route::controller(ChecksFilesController::class)->group(function(){
Route::post('/files/check-in' ,  'checkInFiles')->middleware('auth:sanctum');
Route::post('files/check-out/{groupId}/file' ,  'replaceFile')->middleware('auth:sanctum');
});

Route::controller(checkOutController::class)->group(function(){
    Route::post('files/check-out/{groupId}/file' ,  'checkOut')->middleware('auth:sanctum');
});




