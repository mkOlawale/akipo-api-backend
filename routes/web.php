<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\globalcontroller;
use App\Http\Controllers\testingController;
use App\Http\Middleware\AdminCheck;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::post('app/create_tags', [globalcontroller::class, 'addtags']);
Route::post('app/edit_tags', [globalcontroller::class, 'edittags']);
Route::post('app/delete_tags', [globalcontroller::class, 'deletetags']);
Route::get('app/get_tags', [globalcontroller::class, 'gettags']);


Route::post('app/create_category', [globalcontroller::class, 'addCategory']);
Route::post('app/edit_category', [globalcontroller::class, 'editCategory']);
Route::post('app/upload', [globalcontroller::class, 'Upload']);
Route::get('app/get_category', [globalcontroller::class, 'getCategory']);


Route::post('app/create_user', [globalcontroller::class, 'createUser']);
Route::post('app/edit_users', [globalcontroller::class, 'editUser']);
Route::get('app/get_users', [globalcontroller::class, 'getUser']);
Route::post('app/admin_login', [globalcontroller::class, 'adminLogin']);

Route::post('app/create_blog', [globalcontroller::class, 'createBlog']);
Route::post('app/delete_blog', [globalcontroller::class, 'deleteBlog']);
Route::get('app/blogsdata', [globalcontroller::class, 'blogdata']);
Route::post('app/update_blog/{id}', [globalcontroller::class, 'updateBlog']);
Route::get('app/blog_single/{id}', [globalcontroller::class, 'updateSingleBlog']);

Route::post('createBlog', [globalcontroller::class, 'uploadEditorImage']);


Route::any('{slug}', [globalcontroller::class, 'index'])->where('slug', '([A-z\d-]+)?');

Route::get('/', [globalcontroller::class, 'index']);
Route::get('/logout', [globalcontroller::class, 'logout']);

// Route::any('{slug}', function(){
//     return view('welcome');
// });
Route::get('/payment_page', [testingController::class, 'test'])->name('Payment_request');