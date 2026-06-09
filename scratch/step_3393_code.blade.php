@extends('teacher.layouts.app')
@section('title', __('messages.teacher.dashboard'))
@section('breadcrumb', __('messages.dash.dashboard'))

@section('extra-css')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    /* ═══════════════════════════════════════════════════
       TEACHER DASHBOARD — Modern Premium Design System
    ═══════════════════════════════════════════════════ */

    body { font-family: 'Plus Jakarta Sans', sans-serif !important; }

    /* ── Keyframes ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseLive {
        0%, 100% { transform: scale(1); opacity: 1; }
        50%       { transform: scale(1.55); opacity: 0.55; }
    }
    @keyframes ringBell {
        0%   { transform: rotate(0); }
        10%  { transform: rotate(14deg); }
        20%  { transform: rotate(-10deg); }
        30%  { transform: rotate(11deg); }
        40%  { transform: rotate(-7deg); }
        50%  { transform: rotate(8deg); }
        60%  { transform: rotate(-4deg); }
        80%  { transform: rotate(3deg); }
        100% { tr











































































































































































































































































































































































































































































































































































                    'rgba(16,185,129,0.82)',
                    'rgba(245,158,11,0.82)',
                    'rgba(239,68,68,0.82)',
                    'rgba(236,72,153,0.82)'
                ],
                hoverBackgroundColor: ['#4f46e5','#8b5cf6','#10b981','#f59e0b','#ef4444','#ec4899'],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { animateRotate: true, duration: 800 },
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyleWidth: 9,
                        padding: 14,
                        font: { size: 11.5, weight: '600' },
                        color: '#64748b'
                    }
                },
                tooltip: { callbacks: { label: ctx => `  ${ctx.label}: ${ctx.parsed}` } }
            }
        }
    });

    /* ── Animated counters ── */
    document.querySelectorAll('.t-kpi-val[data-count]').forEach(el => {
        const target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target === 0) return;
        const step = target / (800 / 16);
        let cur = 0;
        const tick = () => {
            cur = Math.min(cur + step, target);
            el.textContent = Math.floor(cur).toLocaleString();
            if (cur < target) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });
});
</script>
@endsection
