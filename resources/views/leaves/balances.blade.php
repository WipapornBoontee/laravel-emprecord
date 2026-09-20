@extends('layouts.app')

@section('title', 'สิทธิ์วันลาคงเหลือ')

@push('styles')
<style>
    .balance-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .btn-apply-leave {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);
        text-decoration: none;
    }
    .btn-apply-leave:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.7);
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="hero-welcome-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-green">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">สิทธิ์วันลาคงเหลือประจำปี {{ $currentYear }}</h3>
                        <p class="text-muted mb-0 small">ตรวจสอบโควตาวันลาที่ได้รับ สิทธิ์ที่ใช้ไป และวันลาคงเหลือรายบุคคล</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('leaves.index', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-calendar2-check-fill me-1 text-primary"></i> ประวัติการลาของฉัน
                    </a>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR'))
                        <a href="{{ route('leaves.approvals', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                            <i class="bi bi-check2-square me-1 text-success"></i> ศูนย์พิจารณาอนุมัติ
                        </a>
                    @endif
                    <a href="{{ route('leaves.create', [], false) }}" class="btn btn-apply-leave d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>ยื่นใบลาใหม่</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quota Cards for Each Leave Type -->
    @forelse($leaveBalances as $balance)
        @php
            $percent = $balance->total_days > 0 ? round(($balance->used_days / $balance->total_days) * 100) : 0;
            $remainPercent = 100 - $percent;
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="balance-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fs-5 fw-bold text-theme">{{ $balance->leaveType->name ?? 'ไม่ระบุ' }}</span>
                        <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">
                            ปี {{ $balance->year }}
                        </span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mb-3">
                        <h2 class="display-5 fw-bold text-primary mb-0">{{ $balance->remaining_days }}</h2>
                        <span class="text-muted fw-semibold">วันคงเหลือ</span>
                    </div>

                    <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%" 
                            aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="row g-2 pt-3 border-top border-theme text-muted small">
                    <div class="col-6">
                        <span class="d-block">สิทธิ์ที่ได้รับ:</span>
                        <strong class="text-theme fs-6">{{ $balance->total_days }} วัน</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span class="d-block">ใช้ไปแล้ว:</span>
                        <strong class="text-danger fs-6">{{ $balance->used_days }} วัน</strong>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning rounded-4 border-0 p-4 text-center">
                <i class="bi bi-exclamation-circle fs-2 d-block mb-2"></i>
                <h5 class="fw-bold">ยังไม่พบข้อมูลสิทธิ์วันลาสำหรับปี {{ $currentYear }}</h5>
                <p class="small text-muted mb-0">ฝ่ายบุคคล (HR) จะทำการจัดสรรโควตาวันลาให้แก่พนักงานในระบบ</p>
            </div>
        </div>
    @endforelse

    <!-- Approved Leaves Table this year -->
    <div class="col-12">
        <div class="balance-card overflow-hidden p-0">
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-check2-all me-1 text-success"></i> รายการลาที่ได้รับการอนุมัติและหักวันลาแล้ว (ปี {{ $currentYear }})
                </div>
                <div>
                    <span class="badge bg-success-subtle text-success px-3 py-1 fw-bold">
                        {{ $approvedLeaves->count() }} รายการ
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>ประเภทการลา</th>
                            <th>วันที่เริ่มต้น</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>จำนวนวันลาที่หัก</th>
                            <th>เหตุผล</th>
                            <th>ผู้อนุมัติ</th>
                            <th>วันที่อนุมัติ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedLeaves as $leave)
                            <tr>
                                <td class="fw-bold text-theme">{{ $leave->leaveType->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 fw-bold">
                                        -{{ $leave->days_count }} วัน
                                    </span>
                                </td>
                                <td>{{ $leave->reason }}</td>
                                <td>{{ $leave->approver->name ?? '-' }}</td>
                                <td class="text-muted small">{{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">ยังไม่มีรายการลาที่ถูกหักวันลาในปีนี้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
