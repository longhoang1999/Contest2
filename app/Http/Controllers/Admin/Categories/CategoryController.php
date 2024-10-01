<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin/Categories/categories');
    }

    public function dataListCategory()
    {
        $categories = Category::all();
        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn(
                'action',
                function ($category) {
                    $action = "
                        <div class='btn-group'>
                             <button class='btn btn-info btn-sm showBtn' data-id='" . $category->id . "'>" . trans('category.btn.detail') . "</button>
                            <button class='btn btn-sm btn-warning editBtn' data-id='" . $category->id . "'>" . trans('category.btn.edit') . "</button>
                            <button class='btn btn-danger btn-sm deleteBtn' data-id='" . $category->id . "'>" . trans('category.btn.delete') . "</button>
                        </div>
                    ";
                    return $action;
                }
            )
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'note' => $request->note,
        ]);

        return response()->json(['success' => 'Danh mục đã được thêm thành công!']);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::findOrFail($id);
        $category->name = $request->input('name');
        $category->note = $request->input('note');
        $category->save();


        return response()->json(['success' => 'Danh mục đã được cập nhật thành công']);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Danh mục đã được xóa thành công!']);
    }

}