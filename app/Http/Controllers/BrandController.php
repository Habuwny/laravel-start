<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
    $name_gen = hexdec(uniqid());
    $img_ext = strtolower($brand_image->getClientOriginalExtension());
    $img_name =
      strtolower($request->brand_name) . '.' . $name_gen . '.' . $img_ext;
    $up_location = 'images/brand/';
    $last_img = $up_location . $img_name;
    $brand_image->move($up_location, $img_name);

    Brand::insert([
      'brand_name' => $request->brand_name,
      'brand_image' => $last_img,
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
}
