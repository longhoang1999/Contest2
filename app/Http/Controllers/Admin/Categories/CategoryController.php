<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin/Categories/categories', compact('categories'));
    }

    // Lấy dữ liệu một danh mục (cho sửa)
    public function show($id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json($category);
        }

        return response()->json(['message' => 'Danh mục không tồn tại'], 404);
    }

    // Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('category.index')->with('error', 'Danh mục không tồn tại');
        }

        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        // Cập nhật danh mục
        $category->update([
            'name' => $request->name,
            'note' => $request->note,
        ]);

        return redirect()->route('category.index')->with('success', 'Danh mục đã được cập nhật thành công!');
    }
}