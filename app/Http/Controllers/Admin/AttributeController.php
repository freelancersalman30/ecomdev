<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $colors = Color::withCount('variants')->get();
        $sizes = Size::withCount('variants')->get();

        return view('admin.attributes.index', compact('colors', 'sizes'));
    }

    public function storeColor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
        ]);

        Color::create($request->only('name', 'code'));

        return redirect()->back()->with('success', 'Color attribute created!');
    }

    public function storeSize(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
        ]);

        Size::create($request->only('name', 'code'));

        return redirect()->back()->with('success', 'Size/Pinout attribute created!');
    }
}
