@extends('admin.layouts.app')

@section('title', __('Enrollments & Sales'))

@section('extra-css')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(24px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.75s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }

    /* Live Stat Cards */
    .dashboard-stat-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        max-width: 320px;
        margin-bottom: 32px;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        border-color: rgba(249, 115, 22, 0.2);
    }

    .dashboard-stat-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .dashboard-stat-card:hover .dashboard-stat-icon-wrapper {
        transform: rotate(5deg) scale(1.08);
    }

    .icon-grad-emerald { background: linear-gradient(135deg, #34d399 0%, #059669 100%); box-shadow: 0 8px 16px rgba(5, 150, 105, 0.2); }

    .stat-val {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .stat-lbl {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-top: 5px;
    }

    /* Cards */
    .premium-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .premium-card-header {
        padding: 24px 28px;
        background: transparent;
        border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .premium-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Custom tables */
    .premium-table {
        width: 100%;
        border-collapse: collapse;
    }

    .premium-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 16px 24px;
        border-bottom: 1.5px solid rgba(15, 23, 42, 0.05);
    }

    .premium-table td {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.04);
        color: #0f172a;
        vertical-align: middle;
    }

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tr:hover {
        background-color: rgba(249, 115, 22, 0.015);
    }
</style>
@endsection

@section('content')
<div class="welcome-banner animate-fade-in" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #115e59 100%); border-radius: 24px; padding: 36px 40px; color: #ffffff; margin-bottom: 32px; border: 1px solid rgba(255, 255, 255, 0.08);">
    <h2 class="welcome-banner-title" style="font-size: 28px; font-weight: 800; margin-bottom: 10px; letter-spacing: -0.03em;">{{ __('Enrollments & Sales') }}</h2>
    <p class="welcome-banner-text" style="color: rgba(241, 245, 249, 0.8); font-size: 15px; margin: 0;">{{ __('Track student course purchases, subscription revenue, and full transaction histories.') }}</p>
</div>

<!-- Total Sales Card -->
<div class="dashboard-stat-card animate-fade-in delay-1">
    <div class="dashboard-stat-icon-wrapper icon-grad-emerald">
        <i class="fa-solid fa-wallet"></i>
    </div>
    <div>
        <div class="stat-val">${{ number_format($totalSales, 2) }}</div>
        <div class="stat-lbl">{{ __('Total Sales Volume') }}</div>
    </div>
</div>

<div class="premium-card animate-fade-in delay-2">
    <div class="premium-card-header">
        <h3 class="premium-card-title">
            <i class="fa-solid fa-receipt text-primary" style="color: var(--brand) !important;"></i>
            {{ __('Transaction Log') }}
        </h3>
    </div>
    <div class="premium-card-body p-0">
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Transaction ID') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $payment->user->name ?? 'Guest' }}</div>
                                <small class="text-muted">{{ $payment->user->email ?? '' }}</small>
                            </td>
                            <td><strong class="text-success">${{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td><code class="small text-muted">{{ $payment->transaction_id ?? '-' }}</code></td>
                            <td>
                                @if($payment->status === 'completed' || $payment->status === 'success')
                                    <span class="badge bg-success">{{ __('Success') }}</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                                @endif
                            </td>
                            <td>{{ $payment->created_at ? $payment->created_at->format('M d, Y H:i') : ($payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : '-') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-inbox d-block mb-3 fs-3 text-muted"></i>
                                {{ __('No transaction records found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $payments->links() }}</div>
@endsection
