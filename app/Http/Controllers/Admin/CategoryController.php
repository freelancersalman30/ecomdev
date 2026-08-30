<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['subCategories', 'products'])->orderBy('display_order')->get();
        $subCategories = SubCategory::with(['category'])->withCount(['childCategories', 'products'])->get();
        $childCategories = ChildCategory::with(['subCategory.category'])->withCount('products')->get();

        return view('admin.categories.index', compact('categories', 'subCategories', 'childCategories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? 'cpu',
            'description' => $request->description,
            'is_featured' => $request->has('is_featured'),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Sub-Category created successfully!');
    }

    public function storeChildCategory(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
        ]);

        ChildCategory::create([
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Child-Category created successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
