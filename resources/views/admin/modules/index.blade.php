@extends('admin.layouts.app')

@section('title', __('messages.admin.modules.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 text-dark fw-bold">{{ __('messages.admin.modules.title') }}</h3>
        <p class="text-muted mb-0">{{ __('messages.admin.modules.subtitle') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
        <i class="fas fa-check-circle text-success fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<form action="{{ route('admin.modules.update') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase fs-7 fw-bold text-muted border-bottom">
                        <tr>
                            <th class="px-4 py-3" style="width: 30%;">{{ __('messages.admin.modules.module_name') }}</th>
                            <th class="px-4 py-3" style="width: 25%;">{{ __('messages.admin.modules.statistics') }}</th>
                            <th class="px-4 py-3" style="width: 30%;">{{ __('messages.admin.modules.accessibility') }}</th>
                            <th class="px-4 py-3 text-end" style="width: 15%;">{{ __('messages.admin.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($modules as $module)
                            @php
                                $icon = match($module->module_name) {
                                    'my_learning' => 'fa-graduation-cap text-primary',
                                    'practice' => 'fa-code text-success',
                                    'assignments' => 'fa-tasks text-warning',
                                    'quizzes' => 'fa-question-circle text-info',
                                    'wishlist' => 'fa-heart text-danger',
                                    'messages' => 'fa-comment-alt text-secondary',
                                    'certificates' => 'fa-award text-success',
                                    'purchases' => 'fa-shopping-bag text-primary',
                                    'playlist' => 'fa-list text-warning',
                                    'watch_later' => 'fa-clock text-indigo',
                                    'profile' => 'fa-user-cog text-dark',
                                    default => 'fa-cube text-secondary'
                                };

                                $translatedName = __('messages.dash.' . $module->module_name);
                                if ($translatedName === 'messages.dash.' . $module->module_name && $module->module_name === 'my_learning') {
                                    $translatedName = __('messages.learning.title');
                                }

                                $statCount = $stats[$module->module_name] ?? 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2.5 rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="fas {{ $icon }} fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $translatedName }}</h6>
                                            <code class="text-muted fs-8">{{ $module->module_name }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="d-inline-flex align-items-center gap-2 px-2.5 py-1 rounded-pill bg-light text-secondary fs-7 fw-medium">
                                        <i class="fas fa-chart-line"></i>
                                        @if($module->module_name === 'my_learning')
                                            {{ __('messages.admin.modules.students_count', ['count' => $statCount]) }}
                                        @elseif($module->module_name === 'profile')
                                            {{ __('messages.admin.modules.students_count', ['count' => $statCount]) }}
                                        @else
                                            {{ __('messages.admin.modules.active_items', ['count' => $statCount]) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column gap-2">
                                        <select name="modules[{{ $module->module_name }}][accessible_by]" class="form-select form-select-sm rounded-3 select-accessibility" data-module="{{ $module->module_name }}">
                                            <option value="all" {{ $module->accessible_by === 'all' ? 'selected' : '' }}>
                                                🔓 {{ __('messages.admin.modules.accessible_by_all') }}
                                            </option>
                                            <option value="premium_only" {{ $module->accessible_by === 'premium_only' ? 'selected' : '' }}>
                                                👑 {{ __('messages.admin.modules.accessible_by_premium') }}
                                            </option>
                                            <option value="disabled" {{ $module->accessible_by === 'disabled' ? 'selected' : '' }}>
                                                🚫 {{ __('messages.admin.modules.accessible_by_disabled') }}
                                            </option>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="form-check form-switch d-inline-block">
                                        <!-- Hidden fallback to send 0 when unchecked -->
                                        <input type="hidden" name="modules[{{ $module->module_name }}][is_enabled]" value="0">
                                        <input class="form-check-input lms-module-toggle" type="checkbox" 
                                               name="modules[{{ $module->module_name }}][is_enabled]" value="1"
                                               id="toggle-{{ $module->module_name }}"
                                               {{ $module->is_enabled && $module->accessible_by !== 'disabled' ? 'checked' : '' }}
                                               {{ $module->accessible_by === 'disabled' ? 'disabled' : '' }}
                                               style="width: 42px; height: 21px; cursor: pointer;">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mb-5">
        <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-3 fw-semibold shadow-sm">
            <i class="fas fa-save me-2"></i>{{ __('messages.dash.save') }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAccessElements = document.querySelectorAll('.select-accessibility');
        
        selectAccessElements.forEach(select => {
            select.addEventListener('change', function() {
                const moduleName = this.getAttribute('data-module');
                const toggle = document.getElementById('toggle-' + moduleName);
                
                if (this.value === 'disabled') {
                    toggle.checked = false;
                    toggle.disabled = true;
                } else {
                    toggle.disabled = false;
                    toggle.checked = true;
                }
            });
        });
    });
</script>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
    .py-1 rounded-pill { border-radius: 50rem; }
    .p-2\.5 { padding: 0.625rem; }
    .fs-7 { font-size: 0.85rem; }
    .fs-8 { font-size: 0.75rem; }
    .select-accessibility {
        max-width: 240px;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        font-weight: 500;
        color: #475569;
        transition: all 0.2s;
    }
    .select-accessibility:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }
    .divide-y tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .divide-y tr:hover {
        background-color: #f8fafc;
    }
    .form-check-input:checked {
        background-color: #f97316 !important;
        border-color: #f97316 !important;
    }
</style>
@endsection
