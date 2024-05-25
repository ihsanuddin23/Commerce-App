<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->orderBy('sub_categories.id', 'asc')
            ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
            $subCategories = $subCategories->orWhere('categories.name', 'like', '%' . $request->get('keyword') . '%');
        }
        $subCategories = $subCategories->paginate(10);


        return view('admin.sub_category.list', compact('subCategories'));
    }
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;

        return view('admin.sub_category.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'slug' => 'required|unique:sub_categories',
                'category' => 'required',
                'status' => 'required'
            ],
            [
                'name.required' => 'Harap isi nama terlebih dahulu',
                'slug.required' => 'Harap isi slug terlebih dahulu',
                'category.required' => 'Harap isi kategori terlebih dahulu',
                'slug.unique' => 'Slug sudah ada, harap gunakan slug yang berbeda',
            ]
        );

        if ($validator->passes()) {

            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();

            $request->session()->flash('success', 'Sub kategori berhasil ditambahkan');

            return response([
                'status' => true,
                'message' => 'Sub kategori berhasil ditambahkan'
            ]);
        } else {
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id, Request $request)
    {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            $request->session()->flash('error', 'Sub kategori tidak ditemukan');
            return redirect()->route('sub-categories.index');
        }

        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;

        return view('admin.sub_category.edit', $data);
    }

    public function update($id, Request $request)
    {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            $request->session()->flash('error', 'Sub kategori tidak ditemukan');
            return response([
                'status' => false,
                'notFound' => true
            ]);
            // return redirect()->route('sub-categories.index');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                // 'slug' => 'required|unique:sub_categories',
                'slug' => 'required|unique:sub_categories,slug,' . $subCategory->id . ',id',
                'category' => 'required',
                'status' => 'required'
            ],
            [
                'name.required' => 'Harap isi nama terlebih dahulu',
                'slug.required' => 'Harap isi slug terlebih dahulu',
                'category.required' => 'Harap isi kategori terlebih dahulu',
                'slug.unique' => 'Slug sudah ada, harap gunakan slug yang berbeda',
            ]
        );

        if ($validator->passes()) {

            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();

            $request->session()->flash('success', 'Sub kategori berhasil diperbarui');

            return response([
                'status' => true,
                'message' => 'Sub kategori berhasil diperbarui'
            ]);
        } else {
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            $request->session()->flash('error', 'Sub kategori tidak ditemukan');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }

        $subCategory->delete();

        $request->session()->flash('success', 'Sub kategori berhasil dihapus');

        return response([
            'status' => true,
            'message' => 'Sub kategori berhasil dihapus'
        ]);
    }
}
