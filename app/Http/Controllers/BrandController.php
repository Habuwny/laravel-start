<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\MultiPics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
  public function AllBrand()
  {
    $brands = Brand::latest()->paginate(5);
    return view('admin.brand.index', compact('brands'));
  }
  public function AddBrand(Request $request)
  {
    $validated = $request->validate(
      [
        'brand_name' => 'required|unique:brands|min:4',
        'brand_image' => 'required|mimes:jpeg,png,jpg',
      ],
      [
        'brand_name.required' => 'Please provide brand name.',
        'brand_name.min' => 'Brand name must be more than 4 characters.',
        'brand_image.required' => 'Please provide brand image.',
      ]
    );

    $brand_image = $request->file('brand_image');
    //
    //    $name_gen = hexdec(uniqid());
    //    $img_ext = strtolower($brand_image->getClientOriginalExtension());
    //    $img_name =
    //      strtolower($request->brand_name) . '.' . $name_gen . '.' . $img_ext;
    //    $up_location = 'images/brand/';
    //    $last_img = $up_location . $img_name;
    //    $brand_image->move($up_location, $img_name);
    $name_gen =
      strtolower($request->brand_name) .
      '.' .
      hexdec(uniqid()) .
      '.' .
      $brand_image->getClientOriginalExtension();

    Image::make($brand_image)
      ->resize(300, 200)
      ->save('images/brand/' . $name_gen);
    $img_path = 'images/brand/' . $name_gen;

    Brand::insert([
      'brand_name' => $request->brand_name,
      'brand_image' => $img_path,
      'created_at' => Carbon::now(),
    ]);

    return Redirect()
      ->back()
      ->with('success', 'Brand inserted successfully.');
  }

  public function EditBrand($id)
  {
    $brand = Brand::find($id);

    return view('admin.brand.edit', compact('brand'));
  }
  public function UpdateBrand(Request $request, $id)
  {
    $validated = $request->validate(
      [
        'brand_name' => 'required|min:4',
      ],
      [
        'brand_name.required' => 'Please provide brand name.',
        'brand_name.min' => 'Brand name must be more than 4 characters.',
      ]
    );

    $old_image = $request->old_image;
    $brand_image = $request->file('brand_image');

    if ($brand_image) {
      $name_gen = hexdec(uniqid());
      $img_ext = strtolower($brand_image->getClientOriginalExtension());
      $img_name =
        strtolower($request->brand_name) . '.' . $name_gen . '.' . $img_ext;
      $up_location = 'images/brand/';
      $last_img = $up_location . $img_name;
      $brand_image->move($up_location, $img_name);

      unlink($old_image);

      Brand::find($id)->update([
        'brand_name' => $request->brand_name,
        'brand_image' => $last_img,
        'created_at' => Carbon::now(),
      ]);

      return Redirect()
        ->back()
        ->with('success', 'Brand updated successfully.');
    } else {
      Brand::find($id)->update([
        'brand_name' => $request->brand_name,
        'created_at' => Carbon::now(),
      ]);

      return Redirect()
        ->back()
        ->with('success', 'Brand updated successfully.');
    }
  }
  public function DeleteBrand($id)
  {
    $img = Brand::find($id);
    $old_img = $img->brand_image;
    unlink($old_img);

    Brand::find($id)->delete();

    return Redirect()
      ->back()
      ->with('success', 'Brand deleted successfully.');
  }

  // Multi Images

  public function MultiPics()
  {
    $images = MultiPics::all();
    return view('admin.multiPics.index', compact('images'));
  }

  public function StoreImages(Request $request)
  {
    $images = $request->file('image');

    foreach ($images as $image) {
      $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

      Image::make($image)
        ->resize(300, 200)
        ->save('images/multi/' . $name_gen);
      $img_path = 'images/multi/' . $name_gen;

      MultiPics::insert([
        'image' => $img_path,
        'created_at' => Carbon::now(),
      ]);
    }

    return Redirect()
      ->back()
      ->with('success', 'Images inserted successfully.');
  }

  public function logout()
  {
    Auth::logout();

    return Redirect::route('login')->with(
      'success',
      'Your are logged out now.'
    );
  }
}
