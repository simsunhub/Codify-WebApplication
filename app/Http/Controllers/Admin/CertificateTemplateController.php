<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        $templates = CertificateTemplate::all();
        return view('admin.certificates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout' => 'required|string',
        ]);

        $layout = json_decode($request->layout, true) ?: [];

        CertificateTemplate::create([
            'name' => $request->name,
            'layout' => $layout,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.certificates.index')->with('success', __('messages.certificates.template_added'));
    }
}
