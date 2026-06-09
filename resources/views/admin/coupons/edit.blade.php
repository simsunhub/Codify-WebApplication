@extends('admin.layouts.app')
@section('title', __('Change Coupon'))
@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h2 class="card-title">{{ __('Edit coupon') }}</h2>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Coupon code') }} *</label>
                <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Type') }} *</label>
                <select name="type" class="form-select" required>
                    <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>{{ __('Percentage') }} (%)</option>
                    <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }} ($)</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Meaning') }} *</label>
                <input type="number" name="value" class="form-control" value="{{ $coupon->value }}" min="0" step="0.01" required>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Usage count') }}</label>
                <input type="number" name="max_uses" class="form-control" value="{{ $coupon->max_uses }}" min="1">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Expiration date') }}</label>
                <input type="date" name="expires_at" class="form-control" value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('Activity status') }} *</label>
                <select name="is_active" class="form-select" required>
                    <option value="1" {{ $coupon->is_active ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="0" {{ !$coupon->is_active ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
        </form>
    </div>
</div>
@endsection