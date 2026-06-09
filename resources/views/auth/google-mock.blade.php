@extends('layouts.app')

@section('title', 'Google Sandbox Login')

@section('extra-css')
<style>
    .sandbox-container {
        min-height: calc(100vh - 64px);
        background: #0d0b21;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        color: #fff;
    }
    .sandbox-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 40px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }
    .sandbox-title {
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #a78bfa, #60a5fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .sandbox-subtitle {
        font-size: 14px;
        color: #9ca3af;
        text-align: center;
        margin-bottom: 32px;
    }
    .account-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }
    .account-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
    }
    .account-item:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: #60a5fa;
        transform: translateY(-2px);
    }
    .account-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }
    .account-info {
        flex: 1;
    }
    .account-name {
        font-size: 15px;
        font-weight: 600;
    }
    .account-email {
        font-size: 13px;
        color: #9ca3af;
    }
    .account-role {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        background: rgba(96, 165, 250, 0.15);
        color: #60a5fa;
        padding: 2px 8px;
        border-radius: 9999px;
    }
    .account-role.instructor {
        background: rgba(167, 139, 250, 0.15);
        color: #a78bfa;
    }
    .custom-trigger {
        display: block;
        text-align: center;
        font-size: 14px;
        color: #60a5fa;
        cursor: pointer;
        margin-top: 16px;
        text-decoration: underline;
    }
    .custom-form {
        display: none;
        margin-top: 24px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 24px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #d1d5db;
        margin-bottom: 6px;
    }
    .form-control {
        width: 100%;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 10px 14px;
        color: #fff;
        font-size: 14px;
        outline: none;
    }
    .form-control:focus {
        border-color: #60a5fa;
    }
    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, #6366f1, #2563eb);
        color: #fff;
        font-weight: 600;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        opacity: 0.9;
    }
</style>
@endsection

@section('content')
<div class="sandbox-container">
    <div class="sandbox-card">
        <div class="sandbox-title">Google OAuth Sandbox</div>
        <div class="sandbox-subtitle">Choose a pre-configured account or enter custom details.</div>

        <div class="account-list" id="accountList">
            <div class="account-item" onclick="selectMockAccount('mock_student_1', 'John Doe', 'student@example.com', 'student')">
                <div class="account-avatar">S</div>
                <div class="account-info">
                    <div class="account-name">John Doe (Student)</div>
                    <div class="account-email">student@example.com</div>
                </div>
                <span class="account-role">Student</span>
            </div>

            <div class="account-item" onclick="selectMockAccount('mock_instructor_1', 'Jane Smith', 'instructor@example.com', 'instructor')">
                <div class="account-avatar">I</div>
                <div class="account-info">
                    <div class="account-name">Jane Smith (Instructor)</div>
                    <div class="account-email">instructor@example.com</div>
                </div>
                <span class="account-role instructor">Instructor</span>
            </div>
            
            <div class="account-item" onclick="selectMockAccount('mock_admin_1', 'Admin User', 'admin@example.com', 'admin')">
                <div class="account-avatar">A</div>
                <div class="account-info">
                    <div class="account-name">Admin User (Admin)</div>
                    <div class="account-email">admin@example.com</div>
                </div>
                <span class="account-role" style="background: rgba(248, 113, 113, 0.15); color: #f87171;">Admin</span>
            </div>
        </div>

        <form id="customForm" class="custom-form" action="{{ route('auth.google.mock-callback') }}" method="POST">
            @csrf
            <input type="hidden" name="google_id" id="customGoogleId">
            
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" style="background: #0d0b21; color: #fff; border: 1px solid rgba(255, 255, 255, 0.08);">
                    <option value="student">Student</option>
                    <option value="instructor">Instructor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Sign In as Mock User</button>
            <a class="custom-trigger" onclick="toggleCustomForm(false)">Back to list</a>
        </form>

        <form id="directLoginForm" action="{{ route('auth.google.mock-callback') }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="google_id" id="directGoogleId">
            <input type="hidden" name="name" id="directName">
            <input type="hidden" name="email" id="directEmail">
            <input type="hidden" name="role" id="directRole">
        </form>

        <a id="showCustomBtn" class="custom-trigger" onclick="toggleCustomForm(true)">Sign in with another account</a>
    </div>
</div>

<script>
    function toggleCustomForm(show) {
        const list = document.getElementById('accountList');
        const form = document.getElementById('customForm');
        const btn = document.getElementById('showCustomBtn');

        if (show) {
            list.style.display = 'none';
            btn.style.display = 'none';
            form.style.display = 'block';
            document.getElementById('customGoogleId').value = 'mock_' + Date.now();
        } else {
            list.style.display = 'flex';
            btn.style.display = 'block';
            form.style.display = 'none';
        }
    }

    function selectMockAccount(id, name, email, defaultRole) {
        document.getElementById('directGoogleId').value = id;
        document.getElementById('directName').value = name;
        document.getElementById('directEmail').value = email;

        // Try to get role from redirect session parameter, if none, use defaultRole
        const sessionRole = "{{ session('social_role', '') }}";
        document.getElementById('directRole').value = sessionRole ? sessionRole : defaultRole;

        document.getElementById('directLoginForm').submit();
    }
</script>
@endsection