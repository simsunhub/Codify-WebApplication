@extends('admin.layouts.app')

@section('title', __('Content Review Queue'))

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
<div class="welcome-banner animate-fade-in" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #3b0764 100%); border-radius: 24px; padding: 36px 40px; color: #ffffff; margin-bottom: 32px; border: 1px solid rgba(255, 255, 255, 0.08);">
    <h2 class="welcome-banner-title" style="font-size: 28px; font-weight: 800; margin-bottom: 10px; letter-spacing: -0.03em;">{{ __('Content Review Queue') }}</h2>
    <p class="welcome-banner-text" style="color: rgba(241, 245, 249, 0.8); font-size: 15px; margin: 0;">{{ __('Moderate and review course contents submitted by instructors before publishing them to the public platform.') }}</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 animate-fade-in" role="alert" style="border-radius: 16px; background: rgba(16, 185, 129, 0.1); color: #047857;">
        <i class="fa-solid fa-circle-check me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="premium-card animate-fade-in delay-1">
    <div class="premium-card-header">
        <h3 class="premium-card-title">
            <i class="fa-solid fa-list-check text-primary" style="color: var(--brand) !important;"></i>
            {{ __('Pending Moderation') }}
        </h3>
    </div>
    <div class="premium-card-body p-0">
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Course') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Instructor') }}</th>
                        <th>{{ __('Price & Level') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($course->image_path)
                                        <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}" class="rounded" style="width: 64px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 64px; height: 40px; font-size: 12px;">
                                            No Image
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $course->title }}</div>
                                        <small class="text-muted">{{ Str::limit($course->description, 60) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $course->category->name ?? '-' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $course->instructor->name ?? 'Unknown' }}</div>
                                <small class="text-muted">{{ $course->instructor->email ?? '' }}</small>
                            </td>
                            <td>
                                <div><strong class="text-success">${{ number_format($course->price, 2) }}</strong></div>
                                <span class="badge bg-secondary text-capitalize">{{ $course->level }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.content-review.approve', $course->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success px-3" style="border-radius: 10px;">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.content-review.reject', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Rejecting this course will delete it. Are you sure?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 10px;">
                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fa-solid fa-circle-check d-block mb-3 fs-3 text-muted"></i>
                                {{ __('All clear! No pending courses for moderation.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $courses->links() }}</div>
@endsection
