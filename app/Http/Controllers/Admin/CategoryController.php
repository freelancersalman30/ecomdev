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
    /**
     * Display all 3 tiers of categories.
     */
    public function index()
    {
        $categories = Category::withCount(['subCategories', 'products'])
            ->with(['subCategories' => function ($q) {
                $q->withCount(['childCategories', 'products'])
                    ->with(['childCategories' => function ($cq) {
                        $cq->withCount('products');
                    }]);
            }])
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $subCategories = SubCategory::with(['category'])->withCount(['childCategories', 'products'])->orderBy('name', 'asc')->get();
        $childCategories = ChildCategory::with(['subCategory.category'])->withCount('products')->orderBy('name', 'asc')->get();

        return view('admin.categories.index', compact('categories', 'subCategories', 'childCategories'));
    }

    /**
     * Store Primary Category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        // Ensure unique slug
        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-'.time();
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->icon ?: 'cpu',
            'description' => $request->description,
            'is_featured' => $request->has('is_featured') || $request->input('is_featured') == '1',
            'is_active' => $request->has('is_active') ? ($request->input('is_active') == '1') : true,
            'display_order' => $request->input('display_order', 0),
        ]);

        return redirect()->back()->with('success', 'Primary Category created successfully!');
    }

    /**
     * Update Primary Category
     */
    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if ($slug !== $category->slug) {
            $count = Category::where('slug', $slug)->where('id', '!=', $category->id)->count();
            if ($count > 0) {
                $slug .= '-'.time();
            }
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->icon ?: ($category->icon ?: 'cpu'),
            'description' => $request->description,
            'is_featured' => $request->has('is_featured') && ($request->input('is_featured') == '1' || $request->input('is_featured') === true),
            'is_active' => $request->has('is_active') ? ($request->input('is_active') == '1' || $request->input('is_active') === true) : true,
            'display_order' => $request->input('display_order', $category->display_order ?? 0),
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    /**
     * Delete Primary Category
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }

    /**
     * Store Sub-Category
     */
    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $count = SubCategory::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-'.time();
        }

        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? ($request->input('is_active') == '1') : true,
            'display_order' => $request->input('display_order', 0),
        ]);

        return redirect()->back()->with('success', 'Sub-Category created successfully!');
    }

    /**
     * Update Sub-Category
     */
    public function updateSubCategory(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if ($slug !== $subCategory->slug) {
            $count = SubCategory::where('slug', $slug)->where('id', '!=', $subCategory->id)->count();
            if ($count > 0) {
                $slug .= '-'.time();
            }
        }

        $subCategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? ($request->input('is_active') == '1' || $request->input('is_active') === true) : true,
            'display_order' => $request->input('display_order', $subCategory->display_order ?? 0),
        ]);

        return redirect()->back()->with('success', 'Sub-Category updated successfully!');
    }

    /**
     * Delete Sub-Category
     */
    public function destroySubCategory(SubCategory $subCategory)
    {
        $subCategory->delete();

        return redirect()->back()->with('success', 'Sub-Category deleted successfully!');
    }

    /**
     * Store Child-Category
     */
    public function storeChildCategory(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $count = ChildCategory::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-'.time();
        }

        ChildCategory::create([
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? ($request->input('is_active') == '1') : true,
            'display_order' => $request->input('display_order', 0),
        ]);

        return redirect()->back()->with('success', 'Child-Category created successfully!');
    }

    /**
     * Update Child-Category
     */
    public function updateChildCategory(Request $request, ChildCategory $childCategory)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if ($slug !== $childCategory->slug) {
            $count = ChildCategory::where('slug', $slug)->where('id', '!=', $childCategory->id)->count();
            if ($count > 0) {
                $slug .= '-'.time();
            }
        }

        $childCategory->update([
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? ($request->input('is_active') == '1' || $request->input('is_active') === true) : true,
            'display_order' => $request->input('display_order', $childCategory->display_order ?? 0),
        ]);

        return redirect()->back()->with('success', 'Child-Category updated successfully!');
    }

    /**
     * Delete Child-Category
     */
    public function destroyChildCategory(ChildCategory $childCategory)
    {
        $childCategory->delete();

        return redirect()->back()->with('success', 'Child-Category deleted successfully!');
    }
}
