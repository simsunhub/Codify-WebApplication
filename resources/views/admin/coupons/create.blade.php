@extends('admin.layouts.app')
@section('title', __('Create a new coupon'))
@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h2 class="card-title">{{ __('Add a coupon') }}</h2>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Coupon code') }} *</label>
                <input type="text" name="code" class="form-control" placeholder="{{ __('For example: HELLO2026') }}" required>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Type') }} *</label>
                <select name="type" class="form-select" required>
                    <option value="percent">{{ __('Percentage') }} (%)</option>
                    <option value="fixed">{{ __('Fixed') }} ($)</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Meaning') }} (Value) *</label>
                <input type="number" name="value" class="form-control" min="0" step="0.01" required>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Maximum number of uses') }} (Max Uses)</label>
                <input type="number" name="max_uses" class="form-control" min="1">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Expiration date') }}</label>
                <input type="date" name="expires_at" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">{{ __('To keep') }}</button>
        </form>
    </div>
</div>
@endsection