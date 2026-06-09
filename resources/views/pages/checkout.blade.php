@extends('layouts.app')

@section('title', __('Payment for') . ' ' . $course->title)
@section('page-title', __('Checkout'))

@section('content')
<style>
    .card {
        background: var(--bg-secondary) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: var(--radius-lg) !important;
        box-shadow: var(--shadow-card) !important;
        color: var(--text-primary) !important;
        transition: all 0.3s ease;
    }
    .card:hover {
        border-color: rgba(99, 102, 241, 0.2) !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5) !important;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary) !important;
        letter-spacing: 0.3px;
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: var(--radius-md) !important;
        color: #fff !important;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
        box-shadow: none !important;
    }
    .form-control:focus {
        border-color: var(--brand) !important;
        background: rgba(255, 255, 255, 0.06) !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    }
    .form-control::placeholder {
        color: var(--text-muted) !important;
        opacity: 0.7;
    }
    .badge-brand {
        background: var(--brand-light) !important;
        color: #818cf8 !important;
        border: 1px solid rgba(99, 102, 241, 0.2) !important;
    }
    
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px var(--bg-secondary) inset !important;
        -webkit-text-fill-color: #fff !important;
    }
</style>

<div style="max-width: 900px; margin: 0 auto; padding: 30px 0;">
    <div class="grid-2" style="gap: 30px; align-items: start;">
        
        <!-- Course info & summary -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">{{ __('messages.cart.summary_title') }}</h3>
            
            <div style="display: flex; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                <img src="{{ $course->image ? asset('storage/' . $course->image) : asset('images/course-placeholder.jpg') }}" 
                     alt="{{ $course->title }}" 
                     style="width: 100px; height: 60px; object-fit: cover; border-radius: var(--radius-sm);"
                     onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&auto=format&fit=crop&q=80'">
                <div>
                    <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 4px; color: var(--text-primary); line-height: 1.4;">
                        {{ $course->title }}
                    </h4>
                    <span class="badge badge-brand">{{ $course->category->name }}</span>
                    <span style="font-size: 12px; color: var(--text-muted); margin-left: 8px;">{{ ucfirst($course->level) }}</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-secondary);">
                <span>{{ __('messages.cart.price') }}</span>
                <span>${{ number_format($course->price, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 16px; color: var(--text-primary); border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 12px;">
                <span>{{ __('messages.cart.total') }}</span>
                <span>${{ number_format($course->price, 2) }}</span>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">{{ __('messages.cart.confirm_payment') }}</h3>
            
            <form action="{{ route('course.checkout.process', $course->slug) }}" method="POST" id="checkout-form">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="card_name">{{ __('messages.cart.card_name') }}</label>
                    <input type="text" name="card_name" id="card_name" class="form-control" placeholder="IVAN IVANOV" value="{{ old('card_name') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="card_number">{{ __('messages.cart.card_number') }}</label>
                    <div style="position: relative;">
                        <input type="text" name="card_number" id="card_number" class="form-control" placeholder="4000 1234 5678 9010" maxlength="19" value="{{ old('card_number') }}" required>
                        <i class="far fa-credit-card" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px; pointer-events: none;"></i>
                    </div>
                </div>
                
                <div class="grid-2" style="gap: 16px;">
                    <div class="form-group">
                        <label class="form-label" for="card_expiry">{{ __('messages.cart.expiration') }}</label>
                        <input type="text" name="card_expiry" id="card_expiry" class="form-control" placeholder="MM/YY" maxlength="5" value="{{ old('card_expiry') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="card_cvc">CVC / CVV</label>
                        <input type="password" name="card_cvc" id="card_cvc" class="form-control" placeholder="123" maxlength="4" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg btn-full" style="margin-top: 10px;">
                    <i class="fas fa-lock" style="font-size: 12px;"></i> {{ __('messages.cart.confirm_payment') }} (${{ number_format($course->price, 2) }})
                </button>
            </form>
        </div>
        
    </div>
</div>

<script>
// Simple card input masking and helpers
document.addEventListener('DOMContentLoaded', function() {
    const cardNumber = document.getElementById('card_number');
    const cardExpiry = document.getElementById('card_expiry');
    const cardCVC = document.getElementById('card_cvc');
    
    // Auto space card number
    cardNumber?.addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, '');
        let newVal = '';
        for (let i = 0; i < val.length; i++) {
            if (i > 0 && i % 4 === 0) newVal += ' ';
            newVal += val[i];
        }
        e.target.value = newVal;
    });

    // Auto slash expiry
    cardExpiry?.addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, '');
        if (val.length >= 2) {
            e.target.value = val.slice(0,2) + '/' + val.slice(2,4);
        } else {
            e.target.value = val;
        }
    });

    // CVC numbers only
    cardCVC?.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });
});
</script>
@endsection
