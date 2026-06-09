@extends('admin.layouts.app')

@section('title', __('Connections'))

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Name') }}</th>
                    <th>Email</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('The date') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr class="{{ !$contact->is_read ? 'table-warning' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->subject }}</td>
                    <td>
                        @if($contact->is_read)
                            <span class="badge bg-success">{{ __('Read') }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ __('New') }}</span>
                        @endif
                    </td>
                    <td>{{ $contact->created_at->format('d.m.Y') }}</td>
                    <td>
                        <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Turn it off?') }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">{{ __('No contact') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $contacts->links() }}</div>
@endsection