@extends('admin.layouts.app')
@section('title', __('Payment history'))
@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ __('All payments in the system') }}</h2>
    </div>
    <div class="table-container" style="border: none; border-radius: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('A student') }}</th>
                    <th>{{ __('Payment') }} ID / {{ __('Transaction') }}</th>
                    <th>{{ __('The amount') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th>{{ __('It\'s time') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $payment->user->name ?? __('A student') }}</strong><br><small>{{ $payment->user->email ?? '' }}</small></td>
                    <td>
                        <a href="{{ route('admin.payments.show', $payment->id) }}">
                            {{ $payment->transaction_id ?? 'ID ' . $payment->id }}
                        </a>
                    </td>
                    <td><strong style="color: var(--green);">${{ number_format($payment->amount, 2) }}</strong></td>
                    <td><span class="badge badge-success">{{ strtoupper($payment->payment_method) }}</span></td>
                    <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">{{ __('No payments found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $payments->links() }}</div>
@endsection