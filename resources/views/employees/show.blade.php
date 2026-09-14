@extends('layouts.app')

@section('title', 'โปรไฟล์พนักงาน - ' . $employee->name)

@push('styles')
<style>
    .profile-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
    }
    .profile-hero {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(168, 85, 247, 0.08) 100%);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        padding: 2.5rem;
    }
    .profile-avatar-lg {
        width: 90px;
        height: 90px;
        border-radius: 24px;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }
    .info-tile {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 16px;
        padding: 1.2rem;
        transition: all 0.2s ease;
    }
    .info-tile:hover {
        border-color: var(--accent-color);
    }
    .info-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0.35rem;
    }
    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-main);
        word-break: break-word;
    }
    .quota-card {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 18px;
        padding: 1.25rem;
    }
    .readonly-banner {
        background: rgba(99, 102, 241, 0.08);
        border: 1px dashed rgba(99, 102, 241, 0.3);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        color: var(--text-main);
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Read-Only Notice for Regular Employees -->
    @if(Auth::user()->isEmployee())
        <div class="col-12">
            <div class="readonly-banner d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper icon-indigo flex-shrink-0" style="width: 38px; height: 38px;">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <span class="fw-bold">โหมดดูข้อมูลส่วนตัว (Read-Only)</span>
                    <span class="d-block text-muted small">
                        พนักงานไม่สามารถแก้ไขข้อมูลส่วนตัวด้วยตนเองได้ หากต้องการเปลี่ยนแปลงข้อมูล กรุณาติดต่อฝ่ายบุคคล (HR) หรือผู้ดูแลระบบ
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- 1. Profile Hero Section -->
    <div class="col-12">
        <div class="profile-hero">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-4">
                    <div class="profile-avatar-lg">
                        {{ mb_substr($employee->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h2 class="fw-bold mb-0 text-theme">{{ $employee->name }}</h2>
                            @if($employee->role === 'admin')
                                <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-shield-shaded me-1"></i>Admin</span>
                            @elseif($employee->role === 'hr')
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-person-gear me-1"></i>HR</span>
                            @else
                                <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-person me-1"></i>Employee</span>
                            @endif

                            @if($employee->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x-circle-fill me-1"></i>Resigned
                                </span>
                            @endif
                        </div>
                        <p class="text-muted mb-0">
                            รหัสพนักงาน: <code class="fw-bold fs-6">{{ $employee->emp_code }}</code>
                            <span class="mx-2">•</span>
                            <i class="bi bi-envelope me-1"></i>{{ $employee->email }}
                        </p>
                    </div>
                </div>

                <!-- Action Button for Admin & HR (Only if authorized) -->
                <div class="d-flex align-items-center gap-2">
                    @if(Auth::user()->isAdmin() || (Auth::user()->isHr() && !$employee->isAdmin()))
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning rounded-3 px-4 py-2 d-flex align-items-center gap-2 fw-semibold">
                            <i class="bi bi-pencil-square"></i> แก้ไขข้อมูล
                        </a>
                    @endif

                    @if(Auth::user()->isAdmin() || Auth::user()->isHr())
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                            <i class="bi bi-arrow-left me-1"></i> กลับหน้ารายการ
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                            <i class="bi bi-house me-1"></i> กลับหน้าหลัก
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Personal & Employment Details -->
    <div class="col-lg-8">
        <div class="profile-card p-4 p-md-5 mb-4">
            <h5 class="fw-bold mb-4 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill"></i> รายละเอียดข้อมูลส่วนบุคคลและการทำงาน
            </h5>

            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="info-tile">
                        <div class="info-label"><i class="bi bi-diagram-3 me-1 text-primary"></i>แผนกงาน</div>
                        <div class="info-value">{{ $employee->department->name ?? 'ยังไม่ระบุ' }}</div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="info-tile">
                        <div class="info-label"><i class="bi bi-briefcase me-1 text-info"></i>ตำแหน่งงาน</div>
                        <div class="info-value">{{ $employee->position->name ?? 'ยังไม่ระบุ' }}</div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="info-tile">
                        <div class="info-label"><i class="bi bi-telephone me-1 text-success"></i>เบอร์โทรศัพท์</div>
                        <div class="info-value">{{ $employee->phone ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="info-tile">
                        <div class="info-label"><i class="bi bi-calendar-event me-1 text-warning"></i>วันที่เริ่มงาน</div>
                        <div class="info-value">
                            {{ $employee->start_date ? $employee->start_date->format('d F Y') : '-' }}
                            @if($employee->start_date)
                                <span class="badge bg-secondary-subtle text-secondary small ms-1">
                                    ({{ $employee->start_date->diffForHumans() }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="info-tile">
                        <div class="info-label"><i class="bi bi-geo-alt me-1 text-danger"></i>ที่อยู่ปัจจุบัน</div>
                        <div class="info-value">{{ $employee->address ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Leave Requests -->
        <div class="profile-card p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i> ประวัติการลาล่าสุด
                </h5>
            </div>
            
            @if($recentLeaves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-custom mb-0">
                        <thead>
                            <tr>
                                <th>ประเภทการลา</th>
                                <th>ช่วงวันที่</th>
                                <th>จำนวนวัน</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLeaves as $leave)
                                <tr>
                                    <td class="fw-semibold">{{ $leave->leaveType->name ?? '-' }}</td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td>{{ $leave->days_count }} วัน</td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success-subtle text-success">อนุมัติแล้ว</span>
                                        @elseif($leave->status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger">ปฏิเสธ</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">รออนุมัติ</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0 py-3 text-center">ยังไม่มีประวัติการยื่นลา</p>
            @endif
        </div>
    </div>

    <!-- 3. Leave Balances Sidebar -->
    <div class="col-lg-4">
        <div class="profile-card p-4 mb-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart-fill"></i> สิทธิ์วันลาคงเหลือ (ปี {{ date('Y') }})
            </h5>

            <div class="d-flex flex-column gap-3">
                @forelse($leaveBalances as $balance)
                    <div class="quota-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold">{{ $balance->leaveType->name ?? 'ไม่ระบุ' }}</span>
                            <span class="badge bg-primary-subtle text-primary fw-bold">
                                เหลือ {{ $balance->remaining_days }} วัน
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 8px; border-radius: 4px;">
                            @php
                                $percentUsed = $balance->total_days > 0 ? ($balance->used_days / $balance->total_days) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentUsed }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small" style="font-size: 0.78rem;">
                            <span>ใช้ไป: {{ $balance->used_days }} วัน</span>
                            <span>สิทธิ์ทั้งหมด: {{ $balance->total_days }} วัน</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-calendar-x fs-2 d-block mb-1 opacity-50"></i>
                        ยังไม่มีข้อมูลสิทธิ์วันลาประจำปีนี้
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
