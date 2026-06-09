@extends('teacher.layouts.app')
@section('title', __('Income and Payments'))
@section('breadcrumb', __('Finance'))

@section('content')

@if(session('success'))
<div class="ed-alert ed-alert-success mb-4">
    <i class="fa-solid fa-circle-check" style="font-size:16px;margin-top:1px;"></i>
    <div style="flex:1;">{{ session('success') }}</div>
    <button class="ed-alert-close" onclick="this.closest('.ed-alert').remove()">×</button>
</div>
@endif

@if($errors->any())
<div class="ed-alert ed-alert-error mb-4">
    <i class="fa-solid fa-circle-exclamation" style="font-size:16px;margin-top:1px;"></i>
    <div style="flex:1;">
        <ul style="margin:0;padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button class="ed-alert-close" onclick="this.closest('.ed-alert').remove()">×</button>
</div>
@endif

{{-- ── STATS ROW ───────────────────────────────────────────── --}}
<div class="row g-4 mb-4">
    {{-- Card 1: Balance --}}
    <div class="col-12 col-md-4">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(99, 102, 241, 0.12); color: var(--brand);">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('Current Balance') }}</div>
                <div class="t-kpi-value">${{ number_format($balance, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Card 2: Total Withdrawn --}}
    @php
        $totalWithdrawn = $withdrawals->where('status', 'approved')->sum('amount');
        $pendingWithdrawn = $withdrawals->where('status', 'pending')->sum('amount');
    @endphp
    <div class="col-12 col-md-4">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: var(--green);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('Total Withdrawn') }}</div>
                <div class="t-kpi-value">${{ number_format($totalWithdrawn, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Card 3: Pending --}}
    <div class="col-12 col-md-4">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(245, 158, 11, 0.12); color: var(--yellow);">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('Pending Requests') }}</div>
                <div class="t-kpi-value">${{ number_format($pendingWithdrawn, 2) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── SPLIT ROW ───────────────────────────────────────────── --}}
<div class="row g-4">
    {{-- Request payment Form --}}
    <div class="col-lg-5">
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-money-bill-transfer me-2" style="color:var(--brand);"></i>{{ __('Request payment') }}
                    </div>
                    <div class="ed-card-subtitle">{{ __('Withdraw funds from your balance to a card or wallet') }}</div>
                </div>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.revenue.withdraw') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="ed-form-label">{{ __('Amount of withdrawal') }} ($) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" min="10" max="{{ $balance }}" step="0.01" placeholder="{{ __('for example: 50') }}" required>
                        <div class="form-text mt-2 text-muted" style="font-size:12px;">
                            {{ __('Minimum withdrawal is $10. Must not exceed your balance.') }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="ed-form-label">{{ __('Payment method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">{{ __('Choose a method') }}...</option>
                            <option value="mbank">MBANK ({{ __('Kyrgyzstan') }})</option>
                            <option value="elcart">{{ __('Elcart') }} ({{ __('Any bank') }})</option>
                            <option value="visa">Visa / Mastercard</option>
                            <option value="odengi">{{ __('Oh!Money / Elsom') }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="ed-form-label">{{ __('Payment details') }} <span class="text-danger">*</span></label>
                        <textarea name="payment_details" rows="4" class="form-control" placeholder="{{ __('Enter your card or wallet number and your full name...') }}" required></textarea>
                    </div>

                    <button type="submit" class="ed-btn ed-btn-primary w-100" style="justify-content: center;" {{ $balance < 10 ? 'disabled' : '' }}>
                        <i class="fa-solid fa-paper-plane"></i> {{ __('Send a request') }}
                    </button>
                    
                    @if($balance < 10)
                        <div class="form-text text-danger mt-3" style="font-size:11.5px; text-align:center; font-weight: 600;">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ __('Your balance is at the withdrawal threshold') }} ($10).
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Withdrawal History Table --}}
    <div class="col-lg-7">
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-clock-rotate-left me-2" style="color:var(--brand);"></i>{{ __('Release history') }}
                    </div>
                    <div class="ed-card-subtitle">{{ __('Your payment inquiries and their status') }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table ed-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('The amount') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th class="text-end" style="width:140px;">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($withdrawals as $w)
                        <tr>
                            <td style="color:var(--text-muted); font-weight:600;">{{ $loop->iteration }}</td>
                            <td>
                                <span style="font-size:13px; color:var(--text);">{{ $w->created_at->format('d.m.Y H:i') }}</span>
                            </td>
                            <td>
                                <strong style="font-size:14px; color:var(--text);">${{ number_format($w->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="ed-badge ed-badge-indigo" style="font-weight:700;">{{ strtoupper($w->payment_method) }}</span>
                            </td>
                            <td class="text-end">
                                @if($w->status === 'approved')
                                    <span class="ed-badge ed-badge-green">
                                        <i class="fa-solid fa-circle-check me-1"></i> {{ __('Done') }}
                                    </span>
                                @elseif($w->status === 'rejected')
                                    <span class="ed-badge ed-badge-red">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> {{ __('Rejected') }}
                                    </span>
                                @else
                                    <span class="ed-badge ed-badge-yellow">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> {{ __('Checking') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div style="font-size:40px; margin-bottom:14px;">💸</div>
                                <div style="font-weight:700; color:var(--text); font-size:15px;">{{ __('Payment history is empty') }}</div>
                                <div style="color:var(--text-muted); font-size:13px; margin-top: 6px;">{{ __('No withdrawals have been made yet') }}.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection