@extends('admin.layouts.app')

@section('title', 'Course Details')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Course Details</h1>
        <p class="text-muted mb-0">Preview the course record before editing or deleting it.</p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-secondary px-4 py-2">
            <i class="fa-solid fa-pen-to-square me-2"></i>Edit
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-primary px-4 py-2">Back to List</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="edudash-card h-100">
            <div class="edudash-card-header">
                <div>
                    <h2 class="edudash-card-title">{{ $course->title }}</h2>
                    <div class="text-muted small">Slug: {{ $course->slug }}</div>
                </div>
                <span class="badge {{ $course->status === 'published' ? 'text-bg-success' : 'text-bg-secondary' }} rounded-pill px-3 py-2">
                    {{ ucfirst($course->status) }}
                </span>
            </div>

            <div class="edudash-card-body">
                <div class="row g-4 align-items-start">
                    <div class="col-md-5">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="img-fluid rounded-4 border">
                        @else
                            <div class="rounded-4 border d-flex align-items-center justify-content-center bg-light" style="min-height: 240px;">
                                <i class="fa-solid fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-7">
                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Instructor</div>
                            <div class="fw-semibold">{{ $course->user->name ?? 'Admin' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Category</div>
                            <div class="fw-semibold">{{ $course->category->name ?? 'Uncategorized' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Price</div>
                            <div class="price-pill d-inline-flex">
                                @if((float) $course->price === 0.0)
                                    Free
                                @else
                                    ${{ number_format((float) $course->price, 2) }}
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Created</div>
                            <div class="fw-semibold">{{ $course->created_at?->format('d M Y, H:i') }}</div>
                        </div>

                        <div>
                            <div class="text-muted small text-uppercase fw-bold mb-1">Description</div>
                            <div class="text-secondary lh-lg">{{ $course->description ?: 'No description provided.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="edudash-card h-100">
            <div class="edudash-card-header">
                <div>
                    <h2 class="edudash-card-title">Quick Stats</h2>
                    <div class="text-muted small">Course activity snapshot</div>
                </div>
            </div>

            <div class="edudash-card-body">
                <div class="d-grid gap-3">
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="text-muted small mb-1">Lessons</div>
                        <div class="fw-bold fs-5">{{ $course->lessons->count() }}</div>
                    </div>
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="text-muted small mb-1">Enrollments</div>
                        <div class="fw-bold fs-5">{{ $course->enrollments->count() }}</div>
                    </div>
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="text-muted small mb-1">Reviews</div>
                        <div class="fw-bold fs-5">{{ $course->reviews->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection