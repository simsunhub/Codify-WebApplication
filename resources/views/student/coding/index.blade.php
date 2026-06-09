@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <!-- Welcome and Action Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 32px; margin-top: 24px;">
        <div>
            <h1 class="page-title" style="font-size: 32px; font-weight: 800; background: linear-gradient(135deg, #fff 0%, var(--brand, #f97316) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ __('messages.coding.title') ?? 'Coding Playground' }}</h1>
            <p style="color: var(--text-muted, #64748b); margin-top: 8px; font-size: 15px;">{{ __('messages.coding.subtitle') ?? 'Solve challenges, test your algorithms, and build your technical skills.' }}</p>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
        <!-- Card 1: Solved challenges count -->
        <div class="stats-card" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 24px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #6366f1;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
            </div>
            <h3 style="font-size: 13px; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin: 0 0 8px 0;">{{ __('messages.coding.solved') ?? 'Solved Challenges' }}</h3>
            <p style="font-size: 36px; font-weight: 800; color: #fff; margin: 0;">{{ $totalSolved }} / {{ $problems->count() }}</p>
        </div>

        <!-- Card 2: Success rate -->
        <div class="stats-card" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 24px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #10b981;">
                <i class="fas fa-fire" style="font-size: 24px;"></i>
            </div>
            <h3 style="font-size: 13px; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin: 0 0 8px 0;">{{ __('messages.coding.success_rate') ?? 'Success Rate' }}</h3>
            <p style="font-size: 36px; font-weight: 800; color: #fff; margin: 0;">{{ $successRate }}%</p>
        </div>

        <!-- Card 3: Language usage stats -->
        <div class="stats-card" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 24px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #ef4444;">
                <i class="fas fa-code-branch" style="font-size: 24px;"></i>
            </div>
            <h3 style="font-size: 13px; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin: 0 0 8px 0;">{{ __('messages.coding.lang_analysis') ?? 'Preferred Languages' }}</h3>
            <div style="margin-top: 8px; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px;">
                @forelse($langStats as $ls)
                    @if($ls->language)
                        <span style="display: inline-block; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 8px; font-size: 12px; color: #fff; font-weight: 600;">{{ $ls->language->name }}: {{ $ls->count }}</span>
                    @endif
                @empty
                    <span style="color: var(--text-muted, #64748b); font-size: 13.5px;">{{ __('messages.coding.no_data') ?? 'No attempts yet' }}</span>
                @endforelse
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1.1fr; gap: 32px; align-items: start;">
        <!-- Left Column: Challenges List -->
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 20px; margin-top: 0;">{{ __('messages.coding.recommended') ?? 'Coding Challenges' }}</h2>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($problems as $p)
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 20px; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'; this.style.transform='none'">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span class="badge" style="
                                    background: {{ $p->difficulty === 'easy' ? 'rgba(16,185,129,0.1)' : ($p->difficulty === 'medium' ? 'rgba(245,158,11,0.1)' : 'rgba(239,68,68,0.1)') }};
                                    color: {{ $p->difficulty === 'easy' ? '#10b981' : ($p->difficulty === 'medium' ? '#f59e0b' : '#ef4444') }};
                                    font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px;">
                                    {{ __('messages.coding.' . strtolower($p->difficulty)) ?? ucfirst($p->difficulty) }}
                                </span>
                                <span style="font-size: 12px; color: var(--text-muted, #64748b);">{{ $p->category }}</span>
                            </div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #fff; margin: 4px 0 0 0;">{{ $p->title }}</h3>
                        </div>
                        <a href="{{ route('student.coding.show', $p->slug) }}" class="btn btn-primary" style="padding: 10px 18px; font-size: 13px;">
                            {{ __('messages.coding.open_problem') ?? 'Open Editor' }} <i class="fas fa-chevron-right style="margin-left: 6px; font-size: 11px;"></i>
                        </a>
                    </div>
                @empty
                    <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 16px; padding: 40px; text-align: center; color: var(--text-muted, #64748b);">
                        {{ __('messages.coding.no_problems') ?? 'No coding problems available.' }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Recent Attempts -->
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 20px; margin-top: 0;">{{ __('messages.coding.recent_attempts') ?? 'Recent Attempts' }}</h2>
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 16px;">
                @forelse($submissions as $sub)
                    <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 12px; display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                        <div>
                            <h4 style="font-size: 14px; font-weight: 600; color: #fff; margin: 0 0 4px 0;">{{ $sub->problem->title ?? 'Challenge' }}</h4>
                            <span style="font-size: 12px; color: var(--text-muted, #64748b);">{{ $sub->language->name ?? 'Language' }} • {{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '' }}</span>
                        </div>
                        <span class="badge" style="
                            background: {{ $sub->status === 'accepted' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }};
                            color: {{ $sub->status === 'accepted' ? '#10b981' : '#ef4444' }};
                            font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 4px 8px; border-radius: 6px;">
                            {{ $sub->status }}
                        </span>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted, #64748b); padding: 20px 0;">
                        {{ __('messages.coding.no_recent_attempts') ?? 'No attempts made yet.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection