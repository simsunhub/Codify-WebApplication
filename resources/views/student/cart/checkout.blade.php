@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px; max-width: 600px;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('student.cart.index') }}" style="color: var(--brand); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> {{ __('messages.cart.browse') }}
        </a>
    </div>

    <!-- Checkout Card -->
    <div style="background: var(--bg-2); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 32px; box-shadow: 0 15px 30px rgba(0,0,0,0.25);">
        <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 24px; border-bottom:1px solid rgba(255,255,255,0.06); padding-bottom:16px;">
            <i class="fas fa-credit-card" style="color:var(--brand); margin-right:8px;"></i> {{ __('messages.cart.checkout_title') }}
        </h1>

        <!-- Order Summary banner -->
        <div style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.04); border-radius:12px; padding:16px; display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <div>
                <span style="font-size:13px; color:var(--text-muted);">{{ __('messages.cart.total_payable') }}</span>
                <h3 style="font-size:22px; font-weight:800; color:#fff; margin-top:2px;">${{ $total }}</h3>
            </div>
            <span class="badge" style="background:rgba(99, 102, 241,0.1); color:var(--brand); font-size:12px; font-weight:700;">{{ $cart->items()->count() }} {{ __('messages.cart.courses_count_simple') }}</span>
        </div>

        <!-- Payment Form -->
        <form action="{{ route('student.cart.checkout.process') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            
            <div>
                <label style="display:block; font-size:13.5px; color:var(--text-muted); margin-bottom:6px; font-weight:600;">{{ __('messages.cart.card_name') }}</label>
                <input type="text" name="card_name" placeholder="John Doe" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 14.5px; outline:none;">
            </div>

            <div>
                <label style="display:block; font-size:13.5px; color:var(--text-muted); margin-bottom:6px; font-weight:600;">{{ __('messages.cart.card_number') }}</label>
                <input type="text" name="card_number" id="card_number" placeholder="4000 1234 5678 9010" maxlength="19" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 14.5px; outline:none; font-family:monospace;">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:13.5px; color:var(--text-muted); margin-bottom:6px; font-weight:600;">{{ __('messages.cart.expiration') }}</label>
                    <input type="text" name="card_expiry" id="card_expiry" maxlength="5" placeholder="MM/YY" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 14.5px; outline:none; font-family:monospace; text-align:center;">
                </div>
                <div>
                    <label style="display:block; font-size:13.5px; color:var(--text-muted); margin-bottom:6px; font-weight:600;">CVC / CVV</label>
                    <input type="password" name="card_cvc" maxlength="3" placeholder="***" required style="width: 100%; padding: 12px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 14.5px; outline:none; font-family:monospace; text-align:center;">
                </div>
            </div>

            <div style="background:rgba(99, 102, 241,0.03); border:1px dashed rgba(99, 102, 241,0.2); border-radius:10px; padding:14px; margin-top:8px; display:flex; gap:10px; align-items:start;">
                <i class="fas fa-info-circle" style="color:var(--brand); margin-top:2px;"></i>
                <p style="margin:0; font-size:12.5px; color:var(--text-muted); line-height:1.5;">{{ __('messages.cart.demo_warning') }}</p>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px; font-weight: 700; margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-lock"></i> {{ __('messages.cart.confirm_payment') }} (${{ $total }})
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardNumber = document.getElementById('card_number');
    const cardExpiry = document.getElementById('card_expiry');
    
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
});
</script>
@endsection
