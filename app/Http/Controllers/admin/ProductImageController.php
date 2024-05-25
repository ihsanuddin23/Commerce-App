<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imageName;
        $productImage->save();

        // Large
        $destPath = public_path() . '/uploads/product/large/' . $imageName;
        $image = Image::make($sourcePath);
        $image->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($destPath);

        // Small
        $destPath = public_path() . '/uploads/product/small/' . $imageName;
        $image = Image::make($sourcePath);
        $image->fit(300, 300);
        $image->save($destPath);

        return response()->json([
            'sttaus' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('uploads/product/small/' . $productImage->image),
            'message' => 'Foto berhasil di simpan'
        ]);
    }

    public function destroy(Request $request)
    {
        $productImage = ProductImage::find($request->id);

        if (empty($productImage)) {
            return response()->json([
                'sttaus' => false,
                'message' => 'Foto tidak ditemukan'
            ]);
        }

        // Delete image from folder
        File::delete(public_path('uploads/product/large/' . $productImage->image));
        File::delete(public_path('uploads/product/small/' . $productImage->image));

        $productImage->delete();

        return response()->json([
            'sttaus' => true,
            'message' => 'Foto berhasil di hapus'
        ]);
    }
}
