@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px; max-width: 1280px; margin: 0 auto;">
    
    <!-- Page Header -->
    <div style="margin-bottom: 32px;">
        <h1 class="page-title" style="font-size: 32px; font-weight: 800; background: linear-gradient(135deg, #fff 0%, var(--brand, #f97316) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0;">
            {{ __('messages.cart.title') ?? 'Shopping Cart' }}
        </h1>
        <p style="color: var(--text-muted, #64748b); margin-top: 8px; font-size: 15px;">
            {{ __('messages.cart.subtitle') ?? 'Manage the courses you selected and proceed to checkout.' }}
        </p>
    </div>

    <!-- Alert Banners -->
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 16px; color: #10b981; margin-bottom: 24px; font-weight: 600; font-size: 14.5px;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 16px; color: #ef4444; margin-bottom: 24px; font-weight: 600; font-size: 14.5px;">
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 16px; color: #fff; margin-bottom: 24px; font-size: 14.5px;">
            <i class="fas fa-info-circle" style="margin-right: 8px; color: var(--brand, #f97316);"></i> {{ session('info') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; align-items: start;">
        
        <!-- Left Column: Cart Items List -->
        <div>
            @if($cartItems->isEmpty())
                <div class="glass-card" style="padding: 48px; text-align: center; background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(255, 255, 255, 0.1); border-radius: 20px;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255, 255, 255, 0.03); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--text-muted, #64748b);">
                        <i class="fas fa-shopping-cart" style="font-size: 28px;"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 8px;">{{ __('messages.cart.empty_title') ?? 'Your cart is empty' }}</h3>
                    <p style="color: var(--text-muted, #64748b); font-size: 14px; margin-bottom: 24px;">{{ __('messages.cart.empty_desc') ?? 'Explore our library of top courses and find the perfect class for your needs.' }}</p>
                    <a href="/courses" class="btn btn-primary" style="padding: 10px 24px; text-decoration: none; font-size: 14px; font-weight: 600;">{{ __('messages.cart.explore_courses') ?? 'Explore Courses' }}</a>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($cartItems as $item)
                        <div class="glass-card" style="display: flex; gap: 20px; padding: 20px; background: rgba(10, 10, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; backdrop-filter: blur(16px); align-items: center;">
                            <!-- Course Thumbnail -->
                            <div style="width: 120px; height: 75px; border-radius: 12px; overflow: hidden; background: rgba(0,0,0,0.2); flex-shrink: 0; border: 1px solid rgba(255,255,255,0.05);">
                                @if($item->course->image_path)
                                    <img src="{{ asset('storage/' . $item->course->image_path) }}" alt="{{ $item->course->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.1); font-size:24px;">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Course Info -->
                            <div style="flex: 1; min-width: 0;">
                                <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin: 0 0 6px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $item->course->title }}
                                </h3>
                                <div style="font-size: 13px; color: var(--text-muted, #64748b);">
                                    {{ __('messages.cart.by_instructor') ?? 'By' }} {{ $item->course->instructor->name ?? $item->course->user->name ?? 'Instructor' }}
                                </div>
                            </div>
                            
                            <!-- Course Price & Action -->
                            <div style="display: flex; align-items: center; gap: 24px; flex-shrink: 0;">
                                <span style="font-size: 18px; font-weight: 800; color: #fff;">${{ number_format($item->price, 2) }}</span>
                                
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.15); color: #ef4444; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Right Column: Order Summary & Coupon Form -->
        @if(!$cartItems->isEmpty())
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- Order Summary Card -->
                <div class="glass-card" style="background: rgba(10, 10, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 24px; backdrop-filter: blur(16px); display: flex; flex-direction: column; gap: 16px;">
                    <h2 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0 0 4px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.06); padding-bottom: 12px;">
                        {{ __('messages.cart.summary') ?? 'Order Summary' }}
                    </h2>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-muted, #64748b);">
                        <span>{{ __('messages.cart.subtotal') ?? 'Subtotal' }}</span>
                        <span style="color: #fff; font-weight: 600;">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($discount > 0)
                        <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-muted, #64748b);">
                            <span>{{ __('messages.cart.discount') ?? 'Discount' }}</span>
                            <span style="color: #10b981; font-weight: 600;">-${{ number_format($discount, 2) }}</span>
                        </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; color: #fff; border-top: 1px solid rgba(255, 255, 255, 0.06); padding-top: 12px; margin-top: 4px;">
                        <span>{{ __('messages.cart.total') ?? 'Total' }}</span>
                        <span style="color: var(--brand, #f97316);">${{ number_format($total, 2) }}</span>
                    </div>
                    
                    <a href="{{ route('cart.checkout') }}" class="btn btn-primary" style="text-align: center; padding: 12px; font-weight: 700; text-decoration: none; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-credit-card"></i> {{ __('messages.cart.checkout_btn') ?? 'Proceed to Checkout' }}
                    </a>
                </div>
                
                <!-- Coupon Code Card -->
                <div class="glass-card" style="background: rgba(10, 10, 20, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 24px; backdrop-filter: blur(16px);">
                    <h3 style="font-size: 15px; font-weight: 700; color: #fff; margin: 0 0 12px 0;">
                        {{ __('messages.cart.coupon_title') ?? 'Have a coupon?' }}
                    </h3>
                    
                    @if($cart->coupon)
                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); padding: 8px 12px; border-radius: 10px; margin-bottom: 12px;">
                            <span style="color: #10b981; font-weight: 600; font-size: 13px;">
                                <i class="fas fa-tags" style="margin-right: 4px;"></i> {{ $cart->coupon->code }}
                            </span>
                            <span style="color: #10b981; font-size: 12px; font-weight: 700;">{{ __('messages.cart.applied') ?? 'Applied' }}</span>
                        </div>
                    @endif
                    
                    <form action="{{ route('student.cart.coupon') }}" method="POST" style="display: flex; gap: 8px;">
                        @csrf
                        <input type="text" name="code" placeholder="{{ __('messages.cart.coupon_placeholder') ?? 'Enter coupon code' }}" required style="flex: 1; padding: 10px 14px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; color: #fff; font-size: 13.5px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--brand, #f97316)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <button type="submit" class="btn" style="padding: 10px 16px; font-size: 13.5px; font-weight: 600; background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                            {{ __('messages.cart.coupon_apply') ?? 'Apply' }}
                        </button>
                    </form>
                </div>
                
            </div>
        @endif
        
    </div>
</div>
@endsection