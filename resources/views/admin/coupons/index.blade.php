@extends('admin.layouts.app')
@section('title', __('Coupons'))
@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">{{ __('Manage Coupons') }}</h2>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">{{ __('New coupon') }}</a>
    </div>
    <div class="table-container" style="border: none; border-radius: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Meaning') }}</th>
                    <th>{{ __('Limitation') }} ({{ __('Use Max') }})</th>
                    <th>{{ __('Deadline') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr>
                    <td><strong>{{ $coupon->code }}</strong></td>
                    <td>{{ $coupon->type === 'percent' ? __('Percentage (%)') : __('Fixed ($)') }}</td>
                    <td>{{ $coupon->type === 'percent' ? $coupon->value . '%' : '$' . $coupon->value }}</td>
                    <td>{{ $coupon->max_uses ?? __('Without limitation') }}</td>
                    <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d.m.Y') : __('Without a deadline') }}</td>
                    <td>
                        @if($coupon->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap: 8px;">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('{{ __('Delete coupon?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">{{ __('Coupons have not been created') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $coupons->links() }}</div>
@endsection