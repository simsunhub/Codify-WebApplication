@extends('admin.layouts.app')
@section('title', __('Payment details'))
@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ __('Transaction Information') }}</h2>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 20px;">
            <strong>{{ __('A student') }}:</strong> {{ $payment->user->name ?? __('A student') }} ({{ $payment->user->email ?? '' }})<br>
            <strong>{{ __('The amount') }}:</strong> ${{ number_format($payment->amount, 2) }}<br>
            <strong>{{ __('Payment method') }}:</strong> {{ strtoupper($payment->payment_method) }}<br>
            <strong>{{ __('Status') }}:</strong> <span class="badge badge-success">{{ strtoupper($payment->status) }}</span><br>
            <strong>{{ __('Transaction') }} ID:</strong> {{ $payment->transaction_id ?? '—' }}<br>
            <strong>{{ __('The date') }}:</strong> {{ $payment->created_at->format('d.m.Y H:i:s') }}
        </div>
    </div>
</div>
@endsection