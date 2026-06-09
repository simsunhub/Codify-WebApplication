@extends('admin.layouts.app')
@section('title', __('Application details'))
@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ __('Applicant Information') }}</h2>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 20px;">
            <strong>{{ __('Full name') }}:</strong> {{ $application->full_name }}<br>
            <strong>Email / {{ __('Telephone') }}:</strong> {{ $application->email }} / {{ $application->phone }}<br>
            <strong>{{ __('Direction') }}:</strong> {{ $application->expertise }}<br>
            <strong>{{ __('Experience') }} ({{ __('year') }}):</strong> {{ $application->experience_years }} {{ __('year') }}<br>
            <strong>{{ __('Contact page') }} (CV/{{ __('Portfolio') }}):</strong> 
            @if($application->portfolio_url)
                <a href="{{ $application->portfolio_url }}" target="_blank">{{ __('Portfolio link') }}</a>
            @else
                {{ __('Not specified') }}
            @endif
        </div>
        <div style="margin-bottom: 30px; background: rgba(0,0,0,0.02); padding: 15px; border-radius: 8px;">
            <strong>{{ __('Information about himself') }} (Bio):</strong><br>
            <p style="white-space: pre-wrap; margin-top: 8px;">{{ $application->bio }}</p>
        </div>

        @if($application->status === 'pending')
            <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 20px;">
                <h4>{{ __('Review the request') }}</h4>
                <form action="{{ route('admin.teachers.approve', $application->id) }}" method="POST" style="display:inline-block; margin-right:10px;">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label>{{ __('Admin notice') }} ({{ __('Not required at admission') }}):</label>
                        <input type="text" name="admin_notes" class="form-control" placeholder="{{ __('Good luck, welcome!') }}" style="width: 300px; display:block; margin-top:5px;">
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('{{ __('Would you accept this student as a teacher?') }}')">{{ __('Reception') }} (Approve)</button>
                </form>

                <form action="{{ route('admin.teachers.reject', $application->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label>{{ __('Reason for refusal') }} <span class="text-danger">*</span>:</label>
                        <input type="text" name="admin_notes" class="form-control" placeholder="{{ __('Please write the reason...') }}" style="width: 300px; display:block; margin-top:5px;" required>
                    </div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Reject?') }}')">{{ __('Turn down') }} (Reject)</button>
                </form>
            </div>
        @else
            <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 20px;">
                <strong>{{ __('Status') }}:</strong> 
                @if($application->status === 'approved')
                    <span class="badge badge-success">{{ __('Accepted') }}</span>
                @else
                    <span class="badge badge-danger">{{ __('Rejected') }}</span>
                @endif
                <br>
                <strong>{{ __('Seen admin') }}:</strong> {{ $application->reviewer->name ?? __('Administrator') }}<br>
                <strong>{{ __('Warning') }}:</strong> {{ $application->admin_notes ?? '—' }}
            </div>
        @endif
    </div>
</div>
@endsection