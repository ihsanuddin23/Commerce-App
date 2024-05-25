<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::orderBy('id', 'asc');

        if ($request->get('keyword')) {
            $brands = $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $brands = $brands->paginate(10);
        return view('admin.brands.list', compact('brands'));
    }
    public function create()
    {
        return view('admin.brands.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'slug' => 'required|unique:brands'
            ],
            [
                'name.required' => 'Harap isi nama terlebih dahulu',
                'slug.required' => 'Harap isi slug terlebih dahulu',
                'slug.unique' => 'Slug sudah ada, harap gunakan slug yang berbeda',
            ]
        );

        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success', 'Brand berhasil ditambahkan');

            return response()->json([
                'status' => true,
                'message' => 'Brand berhasil ditambahkan'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id, Request $request)
    {
        $brand = Brand::find($id);

        if (empty($brand)) {
            $request->session()->flash('error', 'Brand tidak ditemukan');
            return redirect()->route('brands.index');
        }

        $data['brand'] = $brand;

        return view('admin.brands.edit', $data);
    }
    public function update($id, Request $request)
    {
        $brand = Brand::find($id);

        if (empty($brand)) {
            $request->session()->flash('error', 'Brand tidak ditemukan');
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'slug' => 'required|unique:brands,slug,' . $brand->id . ',id',
            ],
            [
                'name.required' => 'Harap isi nama terlebih dahulu',
                'slug.required' => 'Harap isi slug terlebih dahulu',
                'slug.unique' => 'Slug sudah ada, harap gunakan slug yang berbeda',
            ]
        );

        if ($validator->passes()) {
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success', 'Brand berhasil di perbarui');

            return response()->json([
                'status' => true,
                'message' => 'Brand berhasil di perbarui'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy($id, Request $request)
    {
        $brand = Brand::find($id);

        if (empty($brand)) {
            $request->session()->flash('error', 'Brand tidak tersedia');
            return response([
                'status' => true,
                'notFound' => true
            ]);
        }

        $brand->delete();

        $request->session()->flash('success', 'brand berhasil dihapus');

        return response([
            'status' => true,
            'message' => 'brand berhasil dihapus.'
        ]);
    }
}
