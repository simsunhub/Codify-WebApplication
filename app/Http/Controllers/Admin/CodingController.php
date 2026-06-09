<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgrammingLanguage;
use App\Models\CodingProblem;
use Illuminate\Http\Request;

class CodingController extends Controller
{
    public function index()
    {
        $languages = ProgrammingLanguage::orderBy('id')->get();
        $problems = CodingProblem::with('creator')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.coding.index', compact('languages', 'problems'));
    }

    public function toggleLanguage($id)
    {
        $lang = ProgrammingLanguage::findOrFail($id);
        $lang->update(['is_active' => !$lang->is_active]);
        return redirect()->route('admin.coding.index')->with('success', __('Changed language status!'));
    }

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:programming_languages,slug',
            'ace_mode' => 'required|string',
        ]);

        ProgrammingLanguage::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'ace_mode' => $request->ace_mode,
            'is_active' => true,
        ]);

        return redirect()->route('admin.coding.index')->with('success', __('A new programming language has been added.'));
    }
}