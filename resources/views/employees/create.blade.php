@extends('layouts.app')

@section('title', 'เพิ่มพนักงานใหม่')

@push('styles')
<style>
    /* Section Glass Cards */
    .form-section-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 22px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .form-section-card:hover {
        border-color: var(--card-hover-border);
    }
    .form-section-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--surface-border);
        background: rgba(99, 102, 241, 0.03);
    }
    .form-section-body {
        padding: 1.5rem;
    }

    /* Input with Integrated Icon */
    .input-icon-group {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .input-icon-group .input-icon-left {
        position: absolute;
        left: 1rem;
        color: var(--text-muted);
        font-size: 1.05rem;
        pointer-events: none;
        z-index: 5;
        transition: color 0.25s ease;
    }
    .input-icon-group .form-control-pro,
    .input-icon-group .form-select-pro {
        padding-left: 2.85rem;
    }
    .input-icon-group .password-toggle-btn {
        position: absolute;
        right: 0.85rem;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0.35rem 0.5rem;
        border-radius: 8px;
        font-size: 1.1rem;
        z-index: 5;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .input-icon-group .password-toggle-btn:hover {
        color: var(--accent-color);
        background: var(--badge-bg);
    }

    /* Professional Form Controls */
    .form-control-pro,
    .form-select-pro {
        width: 100%;
        background-color: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 14px;
        padding: 0.75rem 1.1rem;
        font-size: 0.92rem;
        transition: all 0.25s ease;
    }
    .form-control-pro:focus,
    .form-select-pro:focus {
        background-color: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
        outline: none;
    }
    .form-control-pro::placeholder {
        color: var(--text-muted);
        opacity: 0.65;
    }
    .form-select-pro option {
        background-color: var(--dropdown-bg);
        color: var(--text-main);
    }
    .input-icon-group:focus-within .input-icon-left {
        color: var(--accent-color);
    }

    /* Labels & Required indicators */
    .form-label-pro {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.45rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .form-label-pro .req {
        color: #ef4444;
        font-weight: 700;
    }

    /* Live Preview Card */
    .preview-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12);
        position: sticky;
        top: 90px;
        overflow: hidden;
    }
    .preview-header-banner {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(168, 85, 247, 0.15) 100%);
        height: 85px;
        position: relative;
        border-bottom: 1px solid var(--surface-border);
    }
    .preview-avatar-wrap {
        position: relative;
        margin-top: -42px;
        margin-bottom: 12px;
        display: inline-block;
    }
    .preview-avatar {
        width: 80px;
        height: 80px;
        border-radius: 22px;
        background: var(--primary-gradient);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        border: 3px solid var(--surface-bg);
        transition: all 0.3s ease;
    }
    .preview-info-tile {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 12px;
        padding: 10px 12px;
        transition: all 0.2s ease;
    }
    .preview-info-tile:hover {
        border-color: var(--accent-color);
    }

    /* Buttons */
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 12px 32px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -8px rgba(99, 102, 241, 0.6);
    }
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 14px 26px -8px rgba(99, 102, 241, 0.8);
    }
    .btn-cancel-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-muted);
        border-radius: 14px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.25s ease;
        text-decoration: none;
    }
    .btn-cancel-custom:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
        transform: translateY(-1px);
    }

    /* Breadcrumbs */
    .breadcrumb-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
    .breadcrumb-custom a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .breadcrumb-custom a:hover {
        color: var(--accent-color);
    }
    .breadcrumb-custom .separator {
        color: var(--text-muted);
        opacity: 0.5;
        font-size: 0.75rem;
    }
    .breadcrumb-custom .active {
        color: var(--text-main);
        font-weight: 600;
    }

    /* Tips Card */
    .tip-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        padding: 1.25rem;
        backdrop-filter: blur(12px);
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        
        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb-custom">
            <a href="{{ route('dashboard', [], false) }}"><i class="bi bi-house-door me-1"></i>หน้าหลัก</a>
            <span class="separator"><i class="bi bi-chevron-right"></i></span>
            <a href="{{ route('employees.index', [], false) }}">จัดการพนักงาน</a>
            <span class="separator"><i class="bi bi-chevron-right"></i></span>
            <span class="active">เพิ่มพนักงานใหม่</span>
        </div>

        <!-- Header Banner -->
        <div class="hero-welcome-card p-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-purple">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h3 class="fw-bold mb-0 gradient-text">เพิ่มพนักงานใหม่</h3>
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                <i class="bi bi-magic me-1"></i>ระบบจัดสรรวันลาอัตโนมัติ
                            </span>
                        </div>
                        <p class="text-muted mb-0 small">
                            กรอกข้อมูลพนักงานเพื่อสร้างบัญชีผู้ใช้งาน และระบบจะกำหนดสิทธิ์การเข้าใช้งานทันที
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('employees.index', [], false) }}" class="btn btn-cancel-custom btn-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i>
                        <span>กลับหน้ารายชื่อ</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Global Errors Alert -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm" style="background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25) !important;">
                <div class="d-flex align-items-center gap-2 fw-bold mb-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <span>โปรดตรวจสอบและแก้ไขข้อผิดพลาดต่อไปนี้</span>
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employees.store', [], false) }}" id="createEmployeeForm">
            @csrf

            <div class="row g-4">
                <!-- Left Column: Form Fields (8 Cols) -->
                <div class="col-lg-8">
                    
                    <!-- 1. Account & Security Section -->
                    <div class="form-section-card mb-4">
                        <div class="form-section-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stat-icon-wrapper icon-indigo" style="width: 32px; height: 32px; border-radius: 10px; font-size: 1rem;">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-theme">ข้อมูลบัญชีผู้ใช้และระดับสิทธิ์</h6>
                            </div>
                            <span class="badge bg-body-tertiary text-muted border border-theme small">ส่วนที่ 1/3</span>
                        </div>
                        <div class="form-section-body">
                            <div class="row g-3">
                                <!-- Emp Code -->
                                <div class="col-md-4">
                                    <label for="emp_code" class="form-label-pro">
                                        <span>รหัสพนักงาน</span>
                                        <span class="req">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-upc-scan input-icon-left"></i>
                                        <input type="text" class="form-control-pro @error('emp_code') is-invalid @enderror" 
                                            id="emp_code" name="emp_code" value="{{ old('emp_code') }}" 
                                            placeholder="เช่น EMP004" required autocomplete="off">
                                    </div>
                                    @error('emp_code')
                                        <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role -->
                                <div class="col-md-4">
                                    <label for="role" class="form-label-pro">
                                        <span>ระดับสิทธิ์ (Role)</span>
                                        <span class="req">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-person-badge input-icon-left"></i>
                                        <select class="form-select form-select-pro @error('role') is-invalid @enderror" id="role" name="role" required>
                                            @foreach($allowedRoles as $roleKey => $roleLabel)
                                                <option value="{{ $roleKey }}" {{ old('role', 'employee') == $roleKey ? 'selected' : '' }}>
                                                    {{ $roleLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if(Auth::user()->isHr())
                                        <div class="form-text small text-warning mt-1">
                                            <i class="bi bi-info-circle me-1"></i>บัญชี HR กำหนดสิทธิ์ได้เฉพาะ Employee
                                        </div>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div class="col-md-4">
                                    <label for="status" class="form-label-pro">
                                        <span>สถานะการทำงาน</span>
                                        <span class="req">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-activity input-icon-left"></i>
                                        <select class="form-select form-select-pro @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>กำลังปฏิบัติงาน (Active)</option>
                                            <option value="resigned" {{ old('status') == 'resigned' ? 'selected' : '' }}>พ้นสภาพ/ลาออก (Resigned)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label-pro">
                                        <span>อีเมลสำหรับเข้าสู่ระบบ</span>
                                        <span class="req">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-envelope-at input-icon-left"></i>
                                        <input type="email" class="form-control-pro @error('email') is-invalid @enderror" 
                                            id="email" name="email" value="{{ old('email') }}" 
                                            placeholder="employee@dayhub.local" required autocomplete="email">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password with Show/Hide Toggle -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label-pro">
                                        <span>รหัสผ่านเริ่มต้น</span>
                                        <span class="req">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-key input-icon-left"></i>
                                        <input type="password" class="form-control-pro @error('password') is-invalid @enderror" 
                                            id="password" name="password" placeholder="อย่างน้อย 6 ตัวอักษร" required autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="แสดง/ซ่อนรหัสผ่าน" tabindex="-1">
                                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Personal Information Section -->
                    <div class="form-section-card mb-4">
                        <div class="form-section-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stat-icon-wrapper icon-green" style="width: 32px; height: 32px; border-radius: 10px; font-size: 1rem;">
                                    <i class="bi bi-person-lines-fill"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-theme">ข้อมูลส่วนตัวและการติดต่อ</h6>
                            </div>
                            <span class="badge bg-body-tertiary text-muted border border-theme small">ส่วนที่ 2/3</span>
                        </div>
                        <div class="form-section-body">
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label-pro">
                                        <span>ชื่อ - นามสกุล</span>
                                        <span class="req">*</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-person input-icon-left"></i>
                                        <input type="text" class="form-control-pro @error('name') is-invalid @enderror" 
                                            id="name" name="name" value="{{ old('name') }}" 
                                            placeholder="เช่น สมเกียรติ สว่างดี" required autocomplete="name">
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label-pro">
                                        <span>เบอร์โทรศัพท์</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-telephone input-icon-left"></i>
                                        <input type="tel" class="form-control-pro @error('phone') is-invalid @enderror" 
                                            id="phone" name="phone" value="{{ old('phone') }}" 
                                            placeholder="เช่น 081-234-5678" autocomplete="tel">
                                    </div>
                                    @error('phone')
                                        <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label for="address" class="form-label-pro">
                                        <span>ที่อยู่ปัจจุบัน</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-geo-alt input-icon-left" style="top: 1rem;"></i>
                                        <textarea class="form-control-pro @error('address') is-invalid @enderror" 
                                            id="address" name="address" rows="3" 
                                            placeholder="ระบุที่อยู่ติดต่อได้ เช่น เลขที่ อาคาร ถนน แขวง/ตำบล เขต/อำเภอ จังหวัด...">{{ old('address') }}</textarea>
                                    </div>
                                    @error('address')
                                        <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Organization & Employment Section -->
                    <div class="form-section-card mb-4">
                        <div class="form-section-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="stat-icon-wrapper icon-amber" style="width: 32px; height: 32px; border-radius: 10px; font-size: 1rem;">
                                    <i class="bi bi-buildings-fill"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-theme">ข้อมูลสังกัดและการจ้างงาน</h6>
                            </div>
                            <span class="badge bg-body-tertiary text-muted border border-theme small">ส่วนที่ 3/3</span>
                        </div>
                        <div class="form-section-body">
                            <div class="row g-3">
                                <!-- Department -->
                                <div class="col-md-4">
                                    <label for="department_id" class="form-label-pro">
                                        <span>แผนกงาน</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-building input-icon-left"></i>
                                        <select class="form-select form-select-pro @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                            <option value="">-- ไม่ระบุแผนก --</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Position -->
                                <div class="col-md-4">
                                    <label for="position_id" class="form-label-pro">
                                        <span>ตำแหน่งงาน</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-briefcase input-icon-left"></i>
                                        <select class="form-select form-select-pro @error('position_id') is-invalid @enderror" id="position_id" name="position_id">
                                            <option value="">-- ไม่ระบุตำแหน่ง --</option>
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}" 
                                                    data-department="{{ $pos->department_id ?? '' }}"
                                                    {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                                    {{ $pos->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="no-pos-warning" class="text-danger small mt-2 d-none">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i> แผนกนี้ยังไม่มีตำแหน่งงาน กรุณาไปเพิ่มตำแหน่งงานก่อน
                                    </div>
                                </div>

                                <!-- Start Date -->
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label-pro">
                                        <span>วันที่เริ่มงาน</span>
                                    </label>
                                    <div class="input-icon-group">
                                        <i class="bi bi-calendar-event input-icon-left"></i>
                                        <input type="date" class="form-control-pro @error('start_date') is-invalid @enderror" 
                                            id="start_date" name="start_date" 
                                            value="{{ old('start_date', date('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 pt-2 mb-5">
                        <a href="{{ route('employees.index', [], false) }}" class="btn btn-cancel-custom d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-x-lg"></i>
                            <span>ยกเลิก</span>
                        </a>
                        <button type="submit" id="btn-submit-emp" class="btn btn-submit-custom d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                            <span>บันทึกข้อมูลพนักงาน</span>
                        </button>
                    </div>

                </div>

                <!-- Right Column: Live Card Preview & Tips (4 Cols) -->
                <div class="col-lg-4">
                    
                    <!-- Live Employee Preview Card -->
                    <div class="preview-card mb-4">
                        <div class="preview-header-banner d-flex align-items-center justify-content-between px-3">
                            <span class="badge bg-dark bg-opacity-50 text-white border border-white border-opacity-25 small px-2 py-1">
                                <i class="bi bi-eye-fill me-1"></i> ตัวอย่างบัตรพนักงาน
                            </span>
                            <div class="status-indicator d-flex align-items-center gap-1">
                                <div class="pulse-dot" id="previewPulseDot"></div>
                                <span class="small fw-semibold" id="previewStatusText" style="color: #10b981; font-size: 0.75rem;">ปฏิบัติงาน</span>
                            </div>
                        </div>
                        <div class="p-4 text-center">
                            <div class="preview-avatar-wrap">
                                <div class="preview-avatar" id="previewAvatar">
                                    ?
                                </div>
                            </div>
                            
                            <h5 class="fw-bold mb-1 text-theme text-truncate" id="previewName">ชื่อ - นามสกุล</h5>
                            
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-3 flex-wrap">
                                <span class="badge bg-body-tertiary text-theme border border-theme" id="previewCode">
                                    <i class="bi bi-upc me-1"></i> รหัสพนักงาน
                                </span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="previewRole">
                                    <i class="bi bi-shield-check me-1"></i> Employee
                                </span>
                            </div>

                            <div class="text-start mt-3 pt-3 border-top border-theme">
                                <div class="d-flex flex-column gap-2">
                                    <div class="preview-info-tile d-flex align-items-center gap-2">
                                        <i class="bi bi-building text-primary"></i>
                                        <div class="small flex-grow-1 text-truncate">
                                            <span class="text-muted d-block" style="font-size: 0.7rem;">แผนกงาน</span>
                                            <span class="fw-semibold text-theme" id="previewDept">ยังไม่ระบุ</span>
                                        </div>
                                    </div>
                                    <div class="preview-info-tile d-flex align-items-center gap-2">
                                        <i class="bi bi-briefcase text-success"></i>
                                        <div class="small flex-grow-1 text-truncate">
                                            <span class="text-muted d-block" style="font-size: 0.7rem;">ตำแหน่งงาน</span>
                                            <span class="fw-semibold text-theme" id="previewPos">ยังไม่ระบุ</span>
                                        </div>
                                    </div>
                                    <div class="preview-info-tile d-flex align-items-center gap-2">
                                        <i class="bi bi-envelope text-info"></i>
                                        <div class="small flex-grow-1 text-truncate">
                                            <span class="text-muted d-block" style="font-size: 0.7rem;">อีเมล</span>
                                            <span class="fw-semibold text-theme" id="previewEmail">ยังไม่ระบุ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Helpful Guidelines -->
                    <div class="tip-card mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="stat-icon-wrapper icon-purple" style="width: 30px; height: 30px; border-radius: 8px; font-size: 0.9rem;">
                                <i class="bi bi-lightbulb-fill"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-theme" style="font-size: 0.9rem;">ข้อแนะนำในการสร้าง</h6>
                        </div>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                <span><strong>โควตาวันลา:</strong> ระบบจะจัดสรรสิทธิ์วันลาพักร้อน ลากิจ ลาป่วยเริ่มต้นให้อัตโนมัติทันที</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                <span><strong>การเข้าสู่ระบบ:</strong> พนักงานสามารถใช้อีเมลหรือรหัสพนักงานพร้อมรหัสผ่านเพื่อเข้าใช้งานได้</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-shield-lock-fill text-warning mt-1"></i>
                                <span><strong>ความปลอดภัย:</strong> รหัสผ่านจะถูกแฮชเข้ารหัส (Bcrypt) อย่างปลอดภัย</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const deptSelect = document.getElementById('department_id');
        const posSelect = document.getElementById('position_id');
        const submitBtn = document.getElementById('btn-submit-emp');
        const warningDiv = document.getElementById('no-pos-warning');
        
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('emp_code');
        const emailInput = document.getElementById('email');
        const roleSelect = document.getElementById('role');
        const statusSelect = document.getElementById('status');
        
        // Preview Elements
        const previewName = document.getElementById('previewName');
        const previewCode = document.getElementById('previewCode');
        const previewRole = document.getElementById('previewRole');
        const previewAvatar = document.getElementById('previewAvatar');
        const previewDept = document.getElementById('previewDept');
        const previewPos = document.getElementById('previewPos');
        const previewEmail = document.getElementById('previewEmail');
        const previewStatusText = document.getElementById('previewStatusText');
        const previewPulseDot = document.getElementById('previewPulseDot');

        // Password Toggle
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                togglePasswordIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }

        // Live Preview Updater
        function updateLivePreview() {
            // Name & Avatar
            const nameVal = nameInput ? nameInput.value.trim() : '';
            if (previewName) {
                previewName.textContent = nameVal || 'ชื่อ - นามสกุล';
            }
            if (previewAvatar) {
                if (nameVal) {
                    const initials = nameVal.split(' ').map(n => n.charAt(0)).slice(0, 2).join('').toUpperCase();
                    previewAvatar.textContent = initials || nameVal.charAt(0);
                } else {
                    previewAvatar.textContent = '?';
                }
            }

            // Code
            if (previewCode && codeInput) {
                const codeVal = codeInput.value.trim();
                previewCode.innerHTML = '<i class="bi bi-upc me-1"></i> ' + (codeVal || 'รหัสพนักงาน');
            }

            // Email
            if (previewEmail && emailInput) {
                previewEmail.textContent = emailInput.value.trim() || 'ยังไม่ระบุ';
            }

            // Role
            if (previewRole && roleSelect) {
                const roleVal = roleSelect.value;
                const roleText = roleSelect.options[roleSelect.selectedIndex]?.text || roleVal;
                let badgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
                let icon = 'bi-person';

                if (roleVal === 'admin') {
                    badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                    icon = 'bi-shield-check';
                } else if (roleVal === 'hr') {
                    badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                    icon = 'bi-person-badge';
                }
                previewRole.className = 'badge border ' + badgeClass;
                previewRole.innerHTML = `<i class="bi ${icon} me-1"></i> ${roleText.split(' ')[0]}`;
            }

            // Status
            if (statusSelect && previewStatusText && previewPulseDot) {
                const isResigned = statusSelect.value === 'resigned';
                if (isResigned) {
                    previewStatusText.textContent = 'พ้นสภาพ';
                    previewStatusText.style.color = '#ef4444';
                    previewPulseDot.style.backgroundColor = '#ef4444';
                } else {
                    previewStatusText.textContent = 'ปฏิบัติงาน';
                    previewStatusText.style.color = '#10b981';
                    previewPulseDot.style.backgroundColor = '#10b981';
                }
            }

            // Department
            if (previewDept && deptSelect) {
                const deptText = deptSelect.value ? deptSelect.options[deptSelect.selectedIndex]?.text : 'ยังไม่ระบุ';
                previewDept.textContent = deptText;
            }

            // Position
            if (previewPos && posSelect) {
                const posText = posSelect.value ? posSelect.options[posSelect.selectedIndex]?.text : 'ยังไม่ระบุ';
                previewPos.textContent = posText;
            }
        }

        // Attach listeners for live preview
        if (nameInput) nameInput.addEventListener('input', updateLivePreview);
        if (codeInput) codeInput.addEventListener('input', updateLivePreview);
        if (emailInput) emailInput.addEventListener('input', updateLivePreview);
        if (roleSelect) roleSelect.addEventListener('change', updateLivePreview);
        if (statusSelect) statusSelect.addEventListener('change', updateLivePreview);

        // Position Filter Logic
        if (deptSelect && posSelect) {
            const allPosOptions = Array.from(posSelect.querySelectorAll('option')).slice(1);

            function filterPositions() {
                const selectedDept = deptSelect.value;
                const currentSelectedPos = posSelect.value;

                posSelect.innerHTML = '<option value="">-- ไม่ระบุตำแหน่ง --</option>';
                let matchCount = 0;

                allPosOptions.forEach(opt => {
                    const optDept = opt.getAttribute('data-department');
                    if (!selectedDept || (optDept && optDept === selectedDept)) {
                        const cloned = opt.cloneNode(true);
                        if (cloned.value === currentSelectedPos) {
                            cloned.selected = true;
                        }
                        posSelect.appendChild(cloned);
                        if (selectedDept) {
                            matchCount++;
                        }
                    }
                });

                if (selectedDept && matchCount === 0) {
                    const noPosOpt = document.createElement('option');
                    noPosOpt.value = '';
                    noPosOpt.textContent = '⚠️ ไม่พบตำแหน่งงานของแผนกนี้ (ไม่สามารถบันทึกได้)';
                    noPosOpt.disabled = true;
                    noPosOpt.selected = true;
                    posSelect.appendChild(noPosOpt);

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50');
                        submitBtn.setAttribute('title', 'แผนกที่เลือกยังไม่มีตำแหน่งงาน');
                    }
                    if (warningDiv) {
                        warningDiv.classList.remove('d-none');
                    }
                } else {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50');
                        submitBtn.removeAttribute('title');
                    }
                    if (warningDiv) {
                        warningDiv.classList.add('d-none');
                    }
                }

                updateLivePreview();
            }

            deptSelect.addEventListener('change', filterPositions);
            posSelect.addEventListener('change', updateLivePreview);
            filterPositions();
        }

        // Form Submit Validation Protection
        const form = document.getElementById('createEmployeeForm');
        if (form && deptSelect && posSelect) {
            form.addEventListener('submit', function (e) {
                const selectedDept = deptSelect.value;
                if (selectedDept) {
                    const validOptions = Array.from(posSelect.options).filter(o => o.value !== '');
                    if (validOptions.length === 0) {
                        e.preventDefault();
                        alert('แผนกที่เลือกยังไม่มีตำแหน่งงาน ไม่สามารถบันทึกข้อมูลพนักงานได้ กรุณาไปเพิ่มตำแหน่งงานของแผนกนี้ก่อน');
                        return false;
                    }
                    if (!posSelect.value) {
                        e.preventDefault();
                        alert('กรุณาเลือกตำแหน่งงานของพนักงาน');
                        posSelect.focus();
                        return false;
                    }
                }
            });
        }

        // Initial preview run
        updateLivePreview();
    });
</script>
@endpush
@endsection
