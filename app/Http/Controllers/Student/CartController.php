<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected function getOrCreateCart()
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cartItems = $cart->items()->with('course.user')->get();
        
        $subtotal = $cart->getSubtotal();
        $discount = $cart->getDiscount();
        $total = $cart->getTotal();

        return view('student.cart.index', compact('cart', 'cartItems', 'subtotal', 'discount', 'total'));
    }

    public function add($courseId)
    {
        $userId = auth()->id();
        $course = Course::findOrFail($courseId);

        // 1. Prevent author from buying their own course
        if ($course->instructor_id === $userId || $course->user_id === $userId) {
            return back()->with('error', __('You cannot purchase your own course.'));
        }

        // 2. Check if already enrolled
        if ($course->isEnrolledBy($userId)) {
            return back()->with('error', __('You are already enrolled in this course.'));
        }

        $cart = $this->getOrCreateCart();

        // 3. Check if already in cart
        $exists = $cart->items()->where('course_id', $courseId)->exists();
        if ($exists) {
            return redirect()->route('cart.index')->with('info', __('This course is already in your cart.'));
        }

        // 4. Add to cart
        $cart->items()->create([
            'course_id' => $course->id,
            'price' => $course->price,
        ]);

        return redirect()->route('cart.index')->with('success', __('Course added to cart successfully.'));
    }

    public function remove($itemId)
    {
        $cart = $this->getOrCreateCart();
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        return redirect()->route('cart.index')->with('success', __('Item removed from cart.'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();
        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', __('Invalid or expired coupon code.'));
        }

        $cart = $this->getOrCreateCart();
        $cart->update(['coupon_id' => $coupon->id]);

        return back()->with('success', __('Coupon applied successfully.'));
    }

    public function checkout()
    {
        $cart = $this->getOrCreateCart();
        if ($cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty.'));
        }

        $cartItems = $cart->items()->with('course.user')->get();
        $subtotal = $cart->getSubtotal();
        $discount = $cart->getDiscount();
        $total = $cart->getTotal();

        return view('student.cart.checkout', compact('cart', 'cartItems', 'subtotal', 'discount', 'total'));
    }

    public function processCheckout(Request $request)
    {
        $cart = $this->getOrCreateCart();
        if ($cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', __('Your cart is empty.'));
        }

        $subtotal = $cart->getSubtotal();
        $discount = $cart->getDiscount();
        $total = $cart->getTotal();

        // 1. Create Order
        $order = Order::create([
            'user_id' => auth()->id(),
            'coupon_id' => $cart->coupon_id,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'status' => 'completed',
        ]);

        // 2. Create Order Items & Enroll User
        foreach ($cart->items as $item) {
            $course = $item->course;
            
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price' => $item->price,
            ]);

            Enrollment::create([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);

            // Notify Instructor
            $instructor = $course->instructor ?? $course->user;
            if ($instructor) {
                Notification::create([
                    'user_id' => $instructor->id,
                    'type' => 'enrollment',
                    'title' => __('messages.notifications.new_course_purchase'),
                    'body' => __('messages.notifications.student_purchased_course', [
                        'student' => auth()->user()->name,
                        'course' => $course->title,
                    ]),
                    'url' => route('teacher.courses.index'),
                    'is_read' => false,
                ]);
            }
        }

        // 3. Create Payment record
        Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'amount' => $total,
            'payment_method' => 'card',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // 4. Save Coupon Usage
        if ($cart->coupon_id) {
            CouponUsage::create([
                'coupon_id' => $cart->coupon_id,
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'discount_amount' => $discount,
            ]);

            if ($cart->coupon) {
                $cart->coupon->increment('used_count');
            }
        }

        // 5. Clear Cart
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);

        return redirect()->route('student.orders.show', $order->id)
            ->with('success', __('Payment received successfully! Registration for courses has been completed.'));
    }
}