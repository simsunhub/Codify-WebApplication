<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\HeroSection;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        $faqs = Faq::all();
        $testimonials = Testimonial::all();
        $heroes = HeroSection::all();

        return view('admin.cms.index', compact('pages', 'faqs', 'testimonials', 'heroes'));
    }

    public function storeFaq(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        Faq::create($request->all());
        return redirect()->route('admin.cms.index')->with('success', __('FAQ added successfully!'));
    }

    public function destroyFaq($id)
    {
        Faq::findOrFail($id)->delete();
        return redirect()->route('admin.cms.index')->with('success', __('FAQ has been disabled.'));
    }
}