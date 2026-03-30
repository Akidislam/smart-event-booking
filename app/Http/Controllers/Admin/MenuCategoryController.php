<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::withCount('menuItems')->latest()->paginate(15);
        return view('admin.menu.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menu_categories,name',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        MenuCategory::create($request->only('name', 'icon', 'description'));

        return back()->with('success', 'Menu category created successfully!');
    }

    public function update(Request $request, MenuCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:menu_categories,name,' . $category->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $category->update($request->only('name', 'icon', 'description'));

        return back()->with('success', 'Menu category updated successfully!');
    }

    public function destroy(MenuCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Menu category deleted successfully!');
    }
}
