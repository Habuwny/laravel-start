<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
  public function AllCat()
  {
    //        $categories = DB::table('categories')
    //            ->join('users', 'categories.user_id', 'users.id')
    //            ->select('categories.*', 'users.name')
    //            ->latest()
    //            ->paginate(5);
    $categories = Category::latest()->paginate(4);
    $trashCats = Category::onlyTrashed()
      ->latest()
      ->paginate(3);

    return view('admin.category.index', compact('categories', 'trashCats'));
  }

  public function AddCat(Request $request)
  {
    $validated = $request->validate(
      [
        'category_name' => 'required|unique:categories|max:255',
      ],
      [
        'category_name.required' => 'Please provide category name.',
        'category_name.max' => 'Category must be less than 255 characters.',
      ]
    );
    Category::insert([
      'category_name' => $request->category_name,
      'user_id' => Auth::user()->id,
      'created_at' => Carbon::now(),
    ]);

    //        $category = new Category;
    //        $category->category_name = $request->category_name;
    //        $category->user_id = Auth::user()->id;
    //        $category->save();
    return Redirect()
      ->back()
      ->with('success', 'Category inserted successfully.');
  }

  public function EditCat($id)
  {
    //    $category = Category::find($id);
    $category = DB::table('categories')
      ->where('id', $id)
      ->first();

    return view('admin.category.edit', compact('category'));
  }
  public function UpdateCat(Request $request, $id)
  {
    //    $category = Category::find($id)->update([
    //      'category_name' => $request->category_name,
    //      'user_id' => Auth::user()->id,
    //    ]);
    $data = [
      'category_name' => $request->category_name,
      'user_id' => Auth::user()->id,
    ];
    DB::table('categories')
      ->where('id', $id)
      ->update($data);

    return Redirect()
      ->route('all.category')
      ->with('success', 'Category updated successfully.');
  }

  public function SoftDeleteCat($id)
  {
    Category::find($id)->delete();

    return Redirect()
      ->back()
      ->with('success', 'Category trashed successfully.');
  }
  public function RestoreCat($id)
  {
    Category::withTrashed()
      ->find($id)
      ->restore();

    return Redirect()
      ->back()
      ->with('success', 'Category restored successfully.');
  }
  public function DeleteCat($id)
  {
    Category::onlyTrashed()
      ->find($id)
      ->forceDelete();

    return Redirect()
      ->back()
      ->with('success', 'Category deleted successfully.');
  }
}
