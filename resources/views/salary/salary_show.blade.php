@extends('layouts.app')

@section('title', 'ข้อมูลฐานเงินเดือน')

@push('styles')
<style>
    .salary-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        position: relative;
    }
    .salary-header-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        padding: 40px 30px;
        color: white;
        text-align: center;
        position: relative;
    }
    .salary-header-gradient::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 0;
        right: 0;
        height: 40px;
        background: #ffffff;
        border-radius: 50% 50% 0 0;
    }
    .avatar-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px auto;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.5);
    }
    .stat-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border-color: #3b82f6;
    }
    .stat-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0;
    }
    .stat-value.text-success-gradient {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="salary-card fade-in">
                
                <!-- Header / Profile Section -->
                <div class="salary-header-gradient">
                    <div class="avatar-wrapper">
                        <i class="bi bi-person-fill" style="font-size: 3.5rem; color: white;"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ Auth::user()->name }}</h3>
                    <p class="mb-0 opacity-75"><i class="bi bi-person-badge me-1"></i>รหัสพนักงาน: {{ Auth::user()->emp_code ?? '-' }}</p>
                </div>

                <!-- Salary Info Section -->
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-5">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fs-6 fw-medium">
                            <i class="bi bi-wallet2 me-1"></i> ข้อมูลฐานเงินเดือนปัจจุบัน
                        </span>
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <div class="stat-box">
                                <p class="stat-title"><i class="bi bi-cash-stack me-2"></i>ฐานเงินเดือน (Base Salary)</p>
                                <h2 class="stat-value">฿ {{ number_format($baseSalary, 2) }}</h2>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-box">
                                <p class="stat-title"><i class="bi bi-calendar-check me-2"></i>ค่าแรงต่อวัน (คำนวณจากเดือนนี้ {{ $daysInMonth }} วัน)</p>
                                <h2 class="stat-value text-success-gradient">฿ {{ number_format($dailyWage, 2) }}</h2>
                                <p class="text-muted small mt-2 mb-0">อัตราต่อวันนำไปใช้สำหรับคำนวณการหักเงินกรณีขาดงาน</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
