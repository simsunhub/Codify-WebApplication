@extends('layouts.app')

@section('title', __('messages.dash.notifications'))

@section('extra-css')
<style>
    /* Premium Dark Glassmorphism Notification Page Styles */
    .notif-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 48px 24px;
    }
    
    .notif-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(24px) saturate(120%);
        -webkit-backdrop-filter: blur(24px) saturate(120%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        overflow: hidden;
        margin-top: 20px;
        transition: var(--transition);
    }
    
    .notif-card:hover {
        border-color: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 40px rgba(99, 102, 241, 0.15);
    }

    .notif-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.01);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    /* Navigation tabs styling */
    .notif-tabs {
        display: flex;
        background: rgba(0, 0, 0, 0.25);
        padding: 4px;
        border-radius: var(--radius-md);
        gap: 4px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }
    
    .notif-tab {
        padding: 8px 18px;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 10px;
        color: var(--text-muted);
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .notif-tab:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.03);
    }
    
    .notif-tab.active {
        background: linear-gradient(135deg, var(--brand), var(--accent));
        color: #fff;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
    }
    
    .notif-tab-badge {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-weight: 700;
    }

    .notif-tab.active .notif-tab-badge {
        background: rgba(255, 255, 255, 0.25);
    }
    
    .notif-actions {
        display: flex;
        gap: 8px;
    }

    .notif-item-wrapper {
        position: relative;
        transition: var(--transition);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    
    .notif-item-wrapper:last-child {
        border-bottom: none;
    }
    
    .notif-item-wrapper:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .notif-item {
        display: flex;
        gap: 18px;
        padding: 22px 24px;
        text-decoration: none;
        color: inherit;
        flex: 1;
        align-items: flex-start;
        transition: var(--transition);
    }
    
    .notif-item-wrapper.unread {
        background: rgba(99, 102, 241, 0.03);
    }
    
    .notif-item-wrapper.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, var(--brand), var(--accent));
        border-top-left-radius: 2px;
        border-bottom-left-radius: 2px;
    }
    
    .notif-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        transition: var(--transition);
    }
    
    .notif-item-wrapper:hover .notif-icon-box {
        transform: scale(1.06) translateY(-1px);
    }

    /* Curated dark high-fidelity neon colors */
    .icon-comment { background: rgba(168, 85, 247, 0.18); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
    .icon-reply { background: rgba(236, 72, 153, 0.18); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.3); }
    .icon-message { background: rgba(14, 165, 233, 0.18); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3); }
    .icon-review { background: rgba(217, 119, 6, 0.18); color: #fbbf24; border: 1px solid rgba(217, 119, 6, 0.3); }
    .icon-enrollment { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .icon-certificate { background: rgba(99, 102, 241, 0.18); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); }
    .icon-default { background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.12); }

    .notif-info {
        flex: 1;
        min-width: 0;
    }
    
    .notif-title-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 6px;
        gap: 10px;
    }
    
    .notif-title {
        font-size: 15.5px;
        font-weight: 700;
        color: #ffffff !important;
        line-height: 1.35;
    }
    
    .notif-time {
        font-size: 11.5px;
        color: var(--text-dim);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .notif-body {
        font-size: 13.5px;
        color: var(--text-muted);
        line-height: 1.55;
        margin-top: 4px;
    }
    
    .notif-action-container {
        padding-right: 24px;
        display: flex;
        align-items: center;
        align-self: center;
        z-index: 10;
    }
    
    .notif-action-btn {
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-muted) !important;
        border-radius: 10px;
        box-shadow: var(--shadow-sm);
    }
    
    .notif-item-wrapper:hover .notif-action-btn {
        opacity: 1;
        transform: scale(1);
    }
    
    .notif-action-btn:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171 !important;
        border-color: rgba(239, 68, 68, 0.3);
    }
    
    .notif-empty-state {
        text-align: center;
        padding: 70px 40px;
        background: transparent;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.03);
        color: var(--text-dim);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 22px;
        border: 2px dashed rgba(255, 255, 255, 0.12);
        position: relative;
    }
    
    .empty-state-icon i {
        animation: pulse-bell 2.5s infinite ease-in-out;
    }

    .btn-danger-glow {
        border-color: rgba(239, 68, 68, 0.3) !important;
        background: rgba(239, 68, 68, 0.05) !important;
        color: #f87171 !important;
    }
    .btn-danger-glow:hover {
        background: rgba(239, 68, 68, 0.15) !important;
        border-color: rgba(239, 68, 68, 0.5) !important;
        color: #fff !important;
        transform: translateY(-1px);
    }
    
    @keyframes pulse-bell {
        0%, 100% { transform: rotate(0); }
        15% { transform: rotate(-15deg); }
        30% { transform: rotate(12deg); }
        45% { transform: rotate(-10deg); }
        60% { transform: rotate(8deg); }
        75% { transform: rotate(-4deg); }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1280px; margin: 0 auto; padding-top: 20px;">
    @include('student.layouts.nav')

    <div class="notif-container" style="padding-top: 0;">
        <h1 class="page-title" style="font-size: 32px; font-weight: 800; background: linear-gradient(135deg, #fff 0%, var(--brand) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.02em; margin-bottom: 24px;">{{ __('messages.dash.notifications') }}</h1>
    
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); color: #34d399; padding: 14px 20px; border-radius: var(--radius-md); width: 100%;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle text-success" style="font-size: 16px;"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" style="color: #34d399; font-size: 14px; background: none; border: none;" onclick="this.closest('.alert').remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="notif-card">
        <div class="notif-header">
            <div class="notif-tabs">
                <a href="#" class="notif-tab active" id="tab-all" onclick="switchNotifTab(event, 'all')">
                    {{ __('messages.notifications.all') }}
                    <span class="notif-tab-badge">{{ $notifications->count() }}</span>
                </a>
                <a href="#" class="notif-tab" id="tab-unread" onclick="switchNotifTab(event, 'unread')">
                    {{ __('messages.notifications.unread') }}
                    <span class="notif-tab-badge" id="unread-count">{{ $notifications->where('is_read', false)->count() }}</span>
                </a>
            </div>
            <div class="notif-actions">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius:10px; font-size:13px; font-weight:600;">
                        <i class="fas fa-check-double me-1"></i> {{ __('messages.notifications.mark_all_read') }}
                    </button>
                </form>
                <form action="{{ route('notifications.clear-all') }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger-glow" style="border-radius:10px; font-size:13px; font-weight:600;">
                        <i class="fas fa-trash-alt me-1"></i> {{ __('messages.notifications.clear_all') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="notif-list" id="notif-list">
            @forelse($notifications as $notif)
                <div class="notif-item-wrapper {{ $notif->is_read ? 'read' : 'unread' }}" data-is-read="{{ $notif->is_read ? 'true' : 'false' }}">
                    <a href="{{ route('notifications.read', $notif->id) }}" class="notif-item">
                        <div class="notif-icon-box icon-{{ $notif->type ?? 'default' }}">
                            @if($notif->type === 'comment')
                                <i class="fas fa-comment"></i>
                            @elseif($notif->type === 'message')
                                <i class="fas fa-envelope"></i>
                            @elseif($notif->type === 'review')
                                <i class="fas fa-star"></i>
                            @elseif($notif->type === 'enrollment')
                                <i class="fas fa-user-plus"></i>
                            @elseif($notif->type === 'certificate')
                                <i class="fas fa-award"></i>
                            @else
                                <i class="fas fa-bell"></i>
                            @endif
                        </div>
                        <div class="notif-info">
                            <div class="notif-title-row">
                                <div class="notif-title">{{ $notif->title }}</div>
                                <div class="notif-time">
                                    <i class="far fa-clock"></i>
                                    {{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="notif-body">{{ $notif->body }}</div>
                        </div>
                    </a>
                    <div class="notif-action-container">
                        <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm notif-action-btn" title="{{ __('Delete') }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="notif-empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h5 class="font-bold mb-2" style="font-size: 16px; color: #fff;">{{ __('messages.notifications.no_notifications') }}</h5>
                    <p class="text-muted text-sm mb-0">{{ __('messages.notifications.empty_desc') }}</p>
                </div>
            @endforelse
            
            <div class="notif-empty-state d-none" id="unread-empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-check-circle" style="color: #10b981; border-color: #10b981; animation: none;"></i>
                </div>
                <h5 class="font-bold mb-2" style="font-size: 16px; color: #fff;">{{ __('Everything was read!') }}</h5>
                <p class="text-muted text-sm mb-0">{{ __('You have no unread notifications.') }}</p>
            </div>
        </div>
    </div>

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
    </div>
</div>
@endsection

@section('extra-js')
<script>
    let currentTab = 'all';

    function switchNotifTab(event, tab) {
        event.preventDefault();
        
        // Update active tab styles
        document.getElementById('tab-all').classList.remove('active');
        document.getElementById('tab-unread').classList.remove('active');
        
        document.getElementById('tab-' + tab).classList.add('active');
        currentTab = tab;
        
        // Filter elements
        const items = document.querySelectorAll('.notif-item-wrapper');
        let visibleCount = 0;
        
        items.forEach(item => {
            const isRead = item.getAttribute('data-is-read') === 'true';
            
            if (tab === 'all') {
                item.classList.remove('d-none');
                item.classList.add('d-flex');
                visibleCount++;
            } else if (tab === 'unread') {
                if (!isRead) {
                    item.classList.remove('d-none');
                    item.classList.add('d-flex');
                    visibleCount++;
                } else {
                    item.classList.remove('d-flex');
                    item.classList.add('d-none');
                }
            }
        });
        
        // Handle empty states
        const primaryEmptyState = document.querySelector('.notif-empty-state:not(#unread-empty-state)');
        const unreadEmptyState = document.getElementById('unread-empty-state');
        
        if (visibleCount === 0) {
            if (tab === 'unread') {
                unreadEmptyState.classList.remove('d-none');
                if (primaryEmptyState) primaryEmptyState.classList.add('d-none');
            } else {
                if (primaryEmptyState) {
                    primaryEmptyState.classList.remove('d-none');
                }
                unreadEmptyState.classList.add('d-none');
            }
        } else {
            unreadEmptyState.classList.add('d-none');
            if (primaryEmptyState && items.length === 0) {
                primaryEmptyState.classList.remove('d-none');
            } else if (primaryEmptyState) {
                primaryEmptyState.classList.add('d-none');
            }
        }
    }
</script>
@endsection