@extends('admin.layouts.app')
@section('title', __('Income and Payments'))
@section('content')
<div class="grid-3" style="margin-bottom: 28px;">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-val">${{ number_format($totalSales, 2) }}</div>
            <div class="stat-lbl">{{ __('General Trade') }} ({{ __('From courses') }})</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-building"></i></div>
        <div>
            <div class="stat-val">${{ number_format($systemEarning, 2) }}</div>
            <div class="stat-lbl">{{ __('Platform net income') }} (System Share)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-hand-holding-dollar"></i></div>
        <div>
            <div class="stat-val">${{ number_format($instructorEarning, 2) }}</div>
            <div class="stat-lbl">{{ __('Income attributable to teachers') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ __('Withdrawal requests from teachers') }}</h2>
    </div>
    <div class="table-container" style="border: none; border-radius: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('The teacher') }}</th>
                    <th>{{ __('The amount') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th>{{ __('On the requested date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr>
                    <td><strong>{{ $w->user->name ?? __('The teacher') }}</strong><br><small>{{ $w->user->email ?? '' }}</small></td>
                    <td><strong style="color: var(--green);">${{ number_format($w->amount, 2) }}</strong></td>
                    <td><span class="badge badge-warning">{{ strtoupper($w->payment_method) }}</span></td>
                    <td>{{ $w->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($w->status === 'approved')
                            <span class="badge badge-success">{{ __('Translated') }}</span>
                        @elseif($w->status === 'rejected')
                            <span class="badge badge-danger">{{ __('Not accepted') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('Waiting') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($w->status === 'pending')
                            <div style="display:flex; gap: 8px;">
                                <form action="{{ route('admin.revenue.withdraw.approve', $w->id) }}" method="POST" onsubmit="return confirm('{{ __('Mark as paid') }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Lock up') }}</button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectPrompt('{{ $w->id }}')">{{ __('Turn down') }}</button>
                            </div>
                        @else
                            {{ $w->admin_notes ?? __('Processed') }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">{{ __('No withdrawal requests') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function rejectPrompt(id) {
    const note = prompt("{{ __('Write the reason for rejection:') }}");
    if (note) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/revenue/withdraw/${id}/reject`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const notesInput = document.createElement('input');
        notesInput.type = 'hidden';
        notesInput.name = 'admin_notes';
        notesInput.value = note;
        form.appendChild(notesInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection