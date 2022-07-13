<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\AssigneeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\NotifyController;

//use Illuminate\Auth\AuthenticationException;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
 Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::get('/getLeadById/{id}', [LeadController::class,'getLeadById']);
   // Route::get('/getleadAllChannel', [LeadController::class,'getleadAllChannel']);
    Route::post('/getleadAllChannel', [LeadController::class,'getleadAllChannel']);

    Route::get('/getleadbychannel/{id}', [LeadController::class,'getleadbychannel']);
    Route::post('/postlead', [LeadController::class,'PostLead']); 
    Route::get('/getUserList', [LeadController::class,'getUserList']);
    Route::post('/addComment', [LeadController::class,'addComment']);
    Route::post('/getLeadByPost', [LeadController::class,'getLeadByPost']);
    Route::get('/getComments/{id}', [LeadController::class,'getComments']);
    Route::post('/editUser', [LeadController::class,'editUser']);
    Route::get('/loginUserDetail', [UserController::class,'loginUserDetail']);
    Route::post('/getUserdetail', [LeadController::class,'getUserdetail']);
    Route::post('/editLead', [AssigneeController::class,'editLead']);
    Route::post('/updateLead', [AssigneeController::class,'updateLead']);
    Route::post('/assignLead', [AssigneeController::class,'assignLead']);
    Route::get('/UserDetailById/{id}', [UserController::class,'UserDetailById']);
    Route::get('/getnotification', [NotifyController::class,'getnotification']);
    Route::get('/assign_notification', [NotifyController::class,'assign_notification']);
    Route::post('/close_notification', [NotifyController::class,'close_notification']);
    Route::post('/PasswordReset', [UserController::class,'PasswordReset']);
    Route::post('/UserDelete', [UserController::class,'UserDelete']);


   // Route::post('/getUserList', [LeadController::class,'getUserList']);
   
});
Route::post('/login', [UserController::class,'login'])->name('login');
Route::post('/register', [UserController::class,'register']);
Route::post('/ContactUs', [ContactUsController::class,'ContactUs']);
Route::post('/sendmail', [EmailController::class,'sendmail']);

