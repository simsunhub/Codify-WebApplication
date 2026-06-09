<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('courses')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'slug'        => 'nullable|string|max:255|unique:categories,slug',
            'icon'        => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'svg_file'    => 'nullable|file|mimetypes:image/svg+xml,text/xml,text/plain|max:2048',
            'svg_code'    => 'nullable|string',
        ]);

        $data = $request->only(['name', 'description']);
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $data['is_active'] = $request->has('is_active') ? true : false;

        $svgContent = null;
        if ($request->hasFile('svg_file')) {
            $svgContent = file_get_contents($request->file('svg_file')->getRealPath());
        } elseif ($request->filled('svg_code')) {
            $svgContent = $request->input('svg_code');
        }

        if ($svgContent !== null) {
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string(trim($svgContent));
            if ($xml === false || $xml->getName() !== 'svg') {
                libxml_clear_errors();
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'svg_code' => 'Lütfen geçerli bir SVG kodu veya dosyası yükleyin',
                ]);
            }
            libxml_clear_errors();
            $data['icon'] = trim($svgContent);
        } else {
            $data['icon'] = $request->input('icon');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', __('messages.admin.categories.created'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug'        => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'icon'        => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'svg_file'    => 'nullable|file|mimetypes:image/svg+xml,text/xml,text/plain|max:2048',
            'svg_code'    => 'nullable|string',
        ]);

        $data = $request->only(['name', 'description']);
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $data['is_active'] = $request->has('is_active') ? true : false;

        $svgContent = null;
        if ($request->hasFile('svg_file')) {
            $svgContent = file_get_contents($request->file('svg_file')->getRealPath());
        } elseif ($request->filled('svg_code')) {
            $svgContent = $request->input('svg_code');
        }

        if ($svgContent !== null) {
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string(trim($svgContent));
            if ($xml === false || $xml->getName() !== 'svg') {
                libxml_clear_errors();
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'svg_code' => 'Lütfen geçerli bir SVG kodu veya dosyası yükleyin',
                ]);
            }
            libxml_clear_errors();
            $data['icon'] = trim($svgContent);
        } else {
            $data['icon'] = $request->input('icon');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', __('messages.admin.categories.updated'));
    }

    public function destroy(Category $category)
    {
        if ($category->courses()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', __('messages.admin.categories.delete_error'));
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', __('messages.admin.categories.deleted'));
    }
}