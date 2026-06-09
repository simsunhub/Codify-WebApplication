<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\EmailCampaign;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function index()
    {
        $promotions = Promotion::all();
        $campaigns = EmailCampaign::all();
        return view('admin.marketing.index', compact('promotions', 'campaigns'));
    }

    public function storePromotion(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_percent' => 'required|integer|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        Promotion::create($request->all());
        return redirect()->route('admin.marketing.index')->with('success', __('The promotion was successfully created!'));
    }
}