@extends('admin.layouts.app')
@section('title', __('CMS (Content Management)'))
@section('content')
<div class="row g-4" style="display:flex; gap: 24px;">
    
    <div style="flex: 1;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">FAQ ({{ __('Frequently asked questions') }})</h2>
            </div>
            <div class="table-container" style="border: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Question') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                        <tr>
                            <td><strong>{{ $faq->question }}</strong></td>
                            <td>
                                <form action="{{ route('admin.cms.faq.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('FAQ{{ __('Can you turn it off?') }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align:center; padding: 20px; color: var(--text-muted);">FAQ{{ __('narrow was not found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                <h5>{{ __('New') }} FAQ {{ __('to add') }}</h5>
                <form action="{{ route('admin.cms.faq.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <input type="text" name="question" class="form-control" placeholder="{{ __('The question is...') }}" required>
                    </div>
                    <div style="margin-bottom:10px;">
                        <textarea name="answer" rows="2" class="form-control" placeholder="{{ __('The answer is...') }}" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('To add') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection