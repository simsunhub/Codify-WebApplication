@extends('admin.layouts.app')
@section('title', __('Marketing and Promotions'))
@section('content')
<div class="row g-4" style="display:flex; gap: 24px;">
    
    <div style="flex: 1;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Email {{ __('Campaigns') }}</h2>
            </div>
            <div class="table-container" style="border: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Campaign') }}</th>
                            <th>{{ __('The subject') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $camp)
                        <tr>
                            <td><strong>{{ $camp->name }}</strong></td>
                            <td>{{ $camp->subject }}</td>
                            <td><span class="badge badge-success">{{ strtoupper($camp->status) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 20px; color: var(--text-muted);">{{ __('No campaigns') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection