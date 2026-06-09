@extends('admin.layouts.app')

@section('title', __('messages.admin.coding_platform_management') ?? 'Coding Platform Management')

@section('content')
<div class="row g-4">
    <!-- Left Column: Programming Languages -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="card-title fw-bold m-0"><i class="fa-solid fa-code text-primary me-2"></i>{{ __('messages.admin.programming_languages') ?? 'Programming Languages' }}</h5>
            </div>
            <div class="card-body">
                <!-- Add Language Form -->
                <form action="{{ route('admin.coding.language.store') }}" method="POST" class="mb-4 pb-4 border-bottom border-light">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('messages.admin.language_name') ?? 'Name' }}</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Python" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('messages.admin.language_slug') ?? 'Slug' }}</label>
                        <input type="text" name="slug" class="form-control" placeholder="e.g. python" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('messages.admin.monaco_mode') ?? 'Monaco Mode' }}</label>
                        <input type="text" name="ace_mode" class="form-control" placeholder="e.g. python" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 py-2"><i class="fa-solid fa-plus me-2"></i>{{ __('messages.admin.add_language') ?? 'Add Language' }}</button>
                </form>

                <!-- Languages List Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.admin.name') ?? 'Name' }}</th>
                                <th>{{ __('messages.admin.status') ?? 'Status' }}</th>
                                <th class="text-end">{{ __('messages.admin.action') ?? 'Action' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($languages as $lang)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $lang->name }}</div>
                                    <small class="text-muted"><code>{{ $lang->slug }}</code></small>
                                </td>
                                <td>
                                    @if($lang->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-10">{{ __('messages.admin.active') ?? 'Active' }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-10">{{ __('messages.admin.inactive') ?? 'Inactive' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.coding.language.toggle', $lang->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="{{ $lang->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Coding Problems -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0"><i class="fa-solid fa-terminal text-primary me-2"></i>{{ __('messages.admin.coding_problems') ?? 'Coding Problems' }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.admin.title') ?? 'Title' }}</th>
                                <th>{{ __('messages.admin.category') ?? 'Category' }}</th>
                                <th>{{ __('messages.admin.difficulty') ?? 'Difficulty' }}</th>
                                <th>{{ __('messages.admin.creator') ?? 'Creator' }}</th>
                                <th>{{ __('messages.admin.statistics') ?? 'Statistics' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($problems as $p)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $p->title }}</div>
                                </td>
                                <td><span class="text-muted">{{ $p->category }}</span></td>
                                <td>
                                    @if($p->difficulty === 'easy')
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-10">{{ __('messages.coding.easy') ?? 'Easy' }}</span>
                                    @elseif($p->difficulty === 'medium')
                                        <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-10">{{ __('messages.coding.medium') ?? 'Medium' }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-10">{{ __('messages.coding.hard') ?? 'Hard' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $p->creator->name ?? 'System' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ __('messages.admin.attempts') ?? 'Attempts' }}: {{ $p->attempt_count }}</small>
                                    <small class="text-muted d-block">{{ __('messages.admin.solved') ?? 'Solved' }}: {{ $p->solved_count }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fa-regular fa-folder-open fs-2 mb-3 d-block text-opacity-25"></i>
                                    {{ __('messages.admin.no_problems_found') ?? 'No coding problems found.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($problems->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $problems->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection