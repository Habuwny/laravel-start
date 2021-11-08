<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
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

Route::get('/', function () {
  return view('welcome');
});
Route::get('home', function () {
  return 'this is home';
});
Route::get('about', function () {
  return view('about');
})->middleware('check');
Route::get('contact', [ContactController::class, 'index'])->name('con');

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

Route::middleware(['auth:sanctum', 'verified'])
  ->get('/dashboard', function () {
    //    $users = User::all();
    $users = DB::table('users')->get();

    return view('dashboard', compact('users'));
  })
  ->name('dashboard');
