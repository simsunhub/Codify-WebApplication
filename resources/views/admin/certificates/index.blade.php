@extends('admin.layouts.app')
@section('title', __('messages.certificates.templates_title'))
@section('content')
<div class="row g-4" style="display:flex; gap: 24px;">
    {{-- Templates list --}}
    <div style="flex: 2;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{ __('messages.certificates.active_templates') }}</h2>
            </div>
            <div class="table-container" style="border: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.certificates.name') }}</th>
                            <th>{{ __('messages.certificates.layout_metadata') }}</th>
                            <th>{{ __('messages.certificates.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $tpl)
                        <tr>
                            <td><strong>{{ $tpl->name }}</strong></td>
                            <td><code>{{ json_encode($tpl->layout) }}</code></td>
                            <td>{{ $tpl->created_at->format('d.m.Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 40px; color: var(--text-muted);">{{ __('messages.certificates.no_templates') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Template Form --}}
    <div style="flex: 1;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{ __('messages.certificates.add_template') }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.certificates.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('messages.certificates.template_name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('messages.certificates.name_placeholder') }}" required>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">{{ __('messages.certificates.design_settings') }}</label>
                        <textarea name="layout" rows="5" class="form-control" placeholder='{"background_color": "#f8fafc", "border_color": "#e2e8f0", "text_color": "#1e293b"}' style="font-family: monospace;" required>{"background_color": "#f8fafc", "border_color": "#e2e8f0", "text_color": "#1e293b"}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.certificates.save_template') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
