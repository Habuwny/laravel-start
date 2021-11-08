<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Models\MultiPics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
Route::get('/email/verify', function () {
  return view('auth.verify-email');
})
  ->middleware('auth')
  ->name('verification.notice');

Route::get('/', function () {
  $brands = DB::table('brands')->get();
  return view('home', compact('brands'));
});
//Category Controller Route

Route::get('/category/all', [CategoryController::class, 'AllCat'])->name(
  'all.category'
);
Route::post('/category/add', [CategoryController::class, 'AddCat'])->name(
  'store.category'
);
Route::get('/category/edit/{id}', [CategoryController::class, 'EditCat']);
Route::get('/category/soft-delete/{id}', [
  CategoryController::class,
  'SoftDeleteCat',
]);
Route::get('/category/restore/{id}', [CategoryController::class, 'RestoreCat']);
Route::post('/category/update/{id}', [CategoryController::class, 'UpdateCat']);
Route::get('category/delete/{id}', [CategoryController::class, 'DeleteCat']);

//Brand Controller Route
Route::get('/brand/all', [BrandController::class, 'AllBrand'])->name(
  'all.brand'
);
Route::post('/brand/add', [BrandController::class, 'AddBrand'])->name(
  'store.brand'
);
Route::get('brand/edit/{id}', [BrandController::class, 'EditBrand']);

Route::post('brand/update/{id}', [BrandController::class, 'UpdateBrand']);

Route::get('brand/delete/{id}', [BrandController::class, 'DeleteBrand']);

// Multi Images

Route::get('/multi/image', [BrandController::class, 'MultiPics'])->name(
  'multi.image'
);
Route::post('multi/add', [BrandController::class, 'StoreImages'])->name(
  'store.image'
);

// Admin All Routes

Route::get('/brand/all', [BrandController::class, 'AllBrand'])->name(
  'all.brand'
);

////
Route::middleware(['auth:sanctum', 'verified'])
  ->get('/dashboard', function () {
    //    $users = User::all();
    $users = DB::table('users')->get();

    return view('admin.index');
  })
  ->name('dashboard');

Route::get('/user/logout', [BrandController::class, 'logout'])->name(
  'user.logout'
);
