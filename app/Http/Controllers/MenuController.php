<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Show all menu categories as landing page.
     */
    public function index()
    {
        $categories = MenuCategory::withCount('menuItems')->get();
        return view('menu.index', compact('categories'));
    }

    /**
     * Show menu items under a specific category with billing UI.
     */
    public function show(MenuCategory $category)
    {
        $categories = MenuCategory::all();
        $items = $category->menuItems()->where('is_available', true)->get();
        return view('menu.show', compact('category', 'categories', 'items'));
    }
}
