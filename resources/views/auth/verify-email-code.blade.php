@extends('layouts.app')

@section('title', __('messages.auth.register.verify_title'))

@section('extra-css')
<style>
    .auth-page {
        min-height: calc(100vh - 64px);
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
    }
    .auth-card {
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        width: 100%;
        max-width: 440px;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 40px;
    }
    .auth-card h1 {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
        text-align: center;
    }
    .auth-card .subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        text-align: center;
        margin-bottom: 32px;
        line-height: 1.5;
    }
    .code-input-container {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 24px;
    }
    .code-digit-input {
        width: 50px;
        height: 60px;
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 12px !important;
        font-size: 24px !important;
        font-weight: 700 !important;
        text-align: center !important;
        transition: all 0.2s ease !important;
    }
    .code-digit-input:focus {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        outline: none !important;
    }
    .alert-error {
        background: rgba(244, 63, 94, 0.12);
        color: #f43f5e;
        border: 1px solid rgba(244, 63, 94, 0.2);
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
    }
    .alert-success {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
    }
    .resend-container {
        margin-top: 24px;
        text-align: center;
        font-size: 13.5px;
        color: var(--text-secondary);
    }
    .btn-link {
        background: none;
        border: none;
        color: var(--brand);
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        font-family: inherit;
        font-size: inherit;
        text-decoration: none;
        transition: var(--transition);
    }
    .btn-link:hover {
        color: #818cf8;
        text-decoration: underline;
    }
    .btn-link:disabled {
        color: var(--text-muted);
        cursor: not-allowed;
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>{{ __('messages.auth.register.verify_header') }}</h1>
        <p class="subtitle">{!! __('messages.auth.register.verify_subtitle', ['email' => $email]) !!}</p>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register.verify-email.submit') }}" method="POST" id="verify-form">
            @csrf
            <div class="code-input-container">
                <input type="text" maxlength="1" class="code-digit-input" required autocomplete="off">
                <input type="text" maxlength="1" class="code-digit-input" required autocomplete="off">
                <input type="text" maxlength="1" class="code-digit-input" required autocomplete="off">
                <input type="text" maxlength="1" class="code-digit-input" required autocomplete="off">
                <input type="text" maxlength="1" class="code-digit-input" required autocomplete="off">
                <input type="text" maxlength="1" class="code-digit-input" required autocomplete="off">
            </div>

            <!-- Hidden input to hold the full code -->
            <input type="hidden" name="code" id="full-code">

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">{{ __('messages.auth.register.submit_btn') }}</button>
        </form>

        <div class="resend-container">
            <form action="{{ route('register.resend-code') }}" method="POST" id="resend-form" style="display: inline;">
                @csrf
                <span id="timer-text">{!! __('messages.auth.register.resend_wait', ['seconds' => 60]) !!}</span>
                <button type="submit" id="resend-btn" class="btn-link" style="display: none;">{{ __('messages.auth.register.resend_btn') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.code-digit-input');
    const form = document.getElementById('verify-form');
    const hiddenCode = document.getElementById('full-code');

    inputs.forEach((input, index) => {
        // Auto focus first input
        if (index === 0) input.focus();

        input.addEventListener('input', function(e) {
            const val = e.target.value;
            // Clean non-digits
            e.target.value = val.replace(/[^0-9]/g, '');

            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateFullCode();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const data = (e.clipboardData || window.clipboardData).getData('text');
            const digits = data.replace(/[^0-9]/g, '').substring(0, 6);

            for (let i = 0; i < digits.length; i++) {
                if (inputs[i]) {
                    inputs[i].value = digits[i];
                    if (inputs[i + 1]) inputs[i + 1].focus();
                }
            }
            updateFullCode();
        });
    });

    function updateFullCode() {
        let code = '';
        inputs.forEach(input => {
            code += input.value;
        });
        hiddenCode.value = code;
    }

    form.addEventListener('submit', function(e) {
        updateFullCode();
        if (hiddenCode.value.length !== 6) {
            e.preventDefault();
            alert("{{ __('messages.auth.register.alert_enter_code') }}");
        }
    });

    // Countdown Timer & Fetch API for Resend Button
    let secondsLeft = 60;
    const timerText = document.getElementById('timer-text');
    const resendBtn = document.getElementById('resend-btn');
    const resendForm = document.getElementById('resend-form');
    
    // Create or locate dynamic status container
    let ajaxStatusContainer = document.getElementById('ajax-status-container');
    if (!ajaxStatusContainer) {
        ajaxStatusContainer = document.createElement('div');
        ajaxStatusContainer.id = 'ajax-status-container';
        const card = document.querySelector('.auth-card');
        card.insertBefore(ajaxStatusContainer, document.getElementById('verify-form'));
    }

    if (resendForm) {
        resendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (resendBtn.disabled) return;
            resendBtn.disabled = true;
            resendBtn.textContent = 'Gönderiliyor...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ajaxStatusContainer.className = 'alert-success';
                    ajaxStatusContainer.textContent = data.message;
                    // Reset timer
                    secondsLeft = 60;
                    if (timerText) {
                        timerText.style.display = 'inline';
                        timerText.innerHTML = 'Kodu tekrar göndermek için <strong id="countdown">60</strong> sn bekleyin';
                    }
                    if (resendBtn) {
                        resendBtn.style.display = 'none';
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Kodu Tekrar Gönder';
                    }
                    startCountdown();
                } else {
                    ajaxStatusContainer.className = 'alert-error';
                    ajaxStatusContainer.textContent = data.error || 'Bir hata oluştu.';
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Kodu Tekrar Gönder';
                }
            })
            .catch(error => {
                ajaxStatusContainer.className = 'alert-error';
                ajaxStatusContainer.textContent = 'İletişim hatası oluştu.';
                resendBtn.disabled = false;
                resendBtn.textContent = 'Kodu Tekrar Gönder';
            });
        });
    }

    let interval;
    function startCountdown() {
        if (interval) clearInterval(interval);
        interval = setInterval(() => {
            secondsLeft--;
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                countdownEl.textContent = secondsLeft;
            }

            if (secondsLeft <= 0) {
                clearInterval(interval);
                if (timerText) timerText.style.display = 'none';
                if (resendBtn) resendBtn.style.display = 'inline-block';
            }
        }, 1000);
    }

    startCountdown();
});
</script>
@endsection
