@extends('layouts.app')

@section('title', 'บันทึกเวลาเข้า-ออกงาน')

@push('styles')
<style>
    .att-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
    }
    .live-clock-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
        padding: 2.25rem 2rem;
    }
    .clock-display {
        font-size: 3.5rem;
        font-weight: 800;
        letter-spacing: 2px;
        color: var(--text-main);
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .btn-checkin {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 1.25rem 2rem;
        font-size: 1.15rem;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px -8px rgba(16, 185, 129, 0.5);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    .btn-checkin:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px rgba(16, 185, 129, 0.7);
        color: white;
    }
    .btn-checkout {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 1.25rem 2rem;
        font-size: 1.15rem;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px -8px rgba(245, 158, 11, 0.5);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    .btn-checkout:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px rgba(245, 158, 11, 0.7);
        color: white;
    }
    .btn-disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .status-box {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 16px;
        padding: 1.25rem;
    }
    .leave-today-banner {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, rgba(99, 102, 241, 0.1) 100%);
        border: 1px dashed rgba(59, 130, 246, 0.4);
        border-radius: 20px;
        padding: 1.5rem;
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
                        <i class="bi bi-fingerprint"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ระบบบันทึกเวลาปฏิบัติงาน</h3>
                        <p class="text-muted mb-0 small">บันทึกเวลาเข้า-ออกงานประจำวัน และตรวจสอบความตรงต่อเวลาแบบ Real-time</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('attendances.my-history', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-clock-history me-1 text-primary"></i> ประวัติการลงเวลาของฉัน
                    </a>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR'))
                        <a href="{{ route('attendances.report', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                            <i class="bi bi-file-earmark-bar-graph-fill me-1 text-success"></i> รายงานสรุปเวลาทำงาน
                        </a>
                    @endif
                    <a href="{{ route('leaves.create', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-calendar-plus me-1 text-warning"></i> ยื่นใบลา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Live Clock & Greeting Card -->
    <div class="col-12">
        <div class="live-clock-card text-center">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3 status-pill">
                <span class="pulse-dot"></span>
                <span class="small fw-semibold text-theme">ระบบบันทึกเวลาทำงานแบบ Real-time</span>
            </div>
            
            <div class="clock-display font-monospace mb-2" id="attendanceLiveClock">
                {{ date('H:i:s') }}
            </div>
            
            <p class="text-muted fs-5 mb-0">
                <i class="bi bi-calendar3 me-2"></i>{{ date('l, d F Y') }}
                <span class="mx-2">•</span>
                <span>เวลาเริ่มงานมาตรฐาน: <strong class="text-theme">09:00 น.</strong></span>
            </p>
        </div>
    </div>

    <!-- 2. จุดเชื่อมโยงสำคัญ: หากวันนี้ได้รับอนุมัติการลาแล้ว -->
    @if($isLeaveToday)
        <div class="col-12">
            <div class="leave-today-banner d-flex align-items-center gap-4">
                <div class="stat-icon-wrapper icon-indigo flex-shrink-0" style="width: 60px; height: 60px; font-size: 1.8rem;">
                    <i class="bi bi-sun-fill text-warning"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-primary">วันนี้คุณอยู่ในสถานะ "อนุมัติการลา" (Approved Leave)</h4>
                    <p class="mb-0 text-muted">
                        ประเภทการลา: <strong class="text-theme">{{ $todayAttendance->leaveRequest->leaveType->name ?? 'ลางาน' }}</strong>
                        <span class="mx-2">•</span>
                        ระบบได้ทำการบันทึกสถานะการลาให้ท่านโดยอัตโนมัติแล้ว <strong>ไม่จำเป็นต้องกดเข้างาน และระบบจะไม่นับว่าขาดงาน</strong>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Today's Attendance Actions -->
    <div class="col-12">
        <div class="row g-4">
            <!-- Check-in Action Box -->
            <div class="col-md-6">
                <div class="att-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-inline-flex p-3 rounded-4 mb-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-box-arrow-in-right fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-theme">บันทึกเวลาเข้างาน</h4>
                        <p class="text-muted small mb-4">เข้างานก่อนหรือเท่ากับ 09:00 น. ถือว่าตรงเวลา</p>

                        @if($todayAttendance && !empty($todayAttendance->check_in))
                            <div class="status-box mb-4">
                                <span class="text-muted small d-block mb-1">บันทึกเวลาเข้างานแล้วเมื่อ</span>
                                <span class="fs-3 fw-bold font-monospace text-success">{{ $todayAttendance->check_in }} น.</span>
                                <div class="mt-2">
                                    @if($todayAttendance->status === 'on_time')
                                        <span class="badge bg-success-subtle text-success px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>เข้างานตรงเวลา
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-3 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>เข้างานสาย
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        @if($isLeaveToday)
                            <button type="button" class="btn btn-checkin btn-disabled" disabled>
                                <i class="bi bi-shield-lock-fill"></i> อยู่ในวันลา
                            </button>
                        @elseif($todayAttendance && !empty($todayAttendance->check_in))
                            <button type="button" class="btn btn-checkin btn-disabled" disabled>
                                <i class="bi bi-check2-circle"></i> เช็คอินเรียบร้อยแล้ว
                            </button>
                        @else
                            <form action="{{ route('attendances.checkin.process', [], false) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-checkin" onclick="return confirm('ยืนยันบันทึกเวลาเข้างาน ณ ขณะนี้?');">
                                    <i class="bi bi-fingerprint fs-4"></i> กดบันทึกเวลาเข้างาน
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Check-out Action Box -->
            <div class="col-md-6">
                <div class="att-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-inline-flex p-3 rounded-4 mb-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="bi bi-box-arrow-right fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1 text-theme">บันทึกเวลาเลิกงาน</h4>
                        <p class="text-muted small mb-4">เวลาเลิกงานปกติ 17:00 น. บันทึกเมื่อสิ้นสุดการทำงาน</p>

                        @if($todayAttendance && !empty($todayAttendance->check_out))
                            <div class="status-box mb-4">
                                <span class="text-muted small d-block mb-1">บันทึกเวลาเลิกงานแล้วเมื่อ</span>
                                <span class="fs-3 fw-bold font-monospace text-warning">{{ $todayAttendance->check_out }} น.</span>
                                <div class="mt-2">
                                    <span class="badge bg-success-subtle text-success px-3 py-1">
                                        <i class="bi bi-check-all me-1"></i>ปฏิบัติงานครบถ้วนประจำวัน
                                    </span>
                                </div>
                            </div>
                        @elseif($todayAttendance && !empty($todayAttendance->check_in))
                            <div class="status-box mb-4">
                                <span class="text-muted small d-block mb-1">กำลังปฏิบัติงาน</span>
                                <span class="fs-5 fw-bold text-primary">พร้อมบันทึกเลิกงานเมื่อเสร็จสิ้นภารกิจ</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        @if($isLeaveToday)
                            <button type="button" class="btn btn-checkout btn-disabled" disabled>
                                <i class="bi bi-shield-lock-fill"></i> อยู่ในวันลา
                            </button>
                        @elseif(!$todayAttendance || empty($todayAttendance->check_in))
                            <button type="button" class="btn btn-checkout btn-disabled" disabled title="กรุณาบันทึกเวลาเข้างานก่อน">
                                <i class="bi bi-lock-fill"></i> บันทึกเลิกงาน (รอเช็คอิน)
                            </button>
                        @elseif(!empty($todayAttendance->check_out))
                            <button type="button" class="btn btn-checkout btn-disabled" disabled>
                                <i class="bi bi-check2-all"></i> เช็คเอาท์เรียบร้อยแล้ว
                            </button>
                        @else
                            <form action="{{ route('attendances.checkout.process', [], false) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-checkout" onclick="return confirm('ยืนยันบันทึกเวลาเลิกงาน ณ ขณะนี้?');">
                                    <i class="bi bi-box-arrow-right fs-4"></i> กดบันทึกเวลาเลิกงาน
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Recent Attendance History Table (Past 7 Days) -->
    <div class="col-12">
        <div class="att-card overflow-hidden">
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-table me-1 text-primary"></i> ประวัติการลงเวลา 7 วันล่าสุดของฉัน
                </div>
                <a href="{{ route('attendances.my-history', [], false) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-1">
                    <i class="bi bi-calendar3 me-1"></i> ดูประวัติทั้งหมดรายเดือน
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เวลาเข้างาน</th>
                            <th>เวลาเลิกงาน</th>
                            <th>สถานะการลงเวลา</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendances as $record)
                            <tr>
                                <td class="fw-bold text-theme">
                                    {{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}
                                    <span class="badge bg-secondary-subtle text-secondary small ms-1">
                                        {{ \Carbon\Carbon::parse($record->date)->format('D') }}
                                    </span>
                                </td>
                                <td>
                                    @if($record->check_in)
                                        <span class="font-monospace fw-semibold">{{ $record->check_in }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_out)
                                        <span class="font-monospace fw-semibold">{{ $record->check_out }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->status === 'on_time')
                                        <span class="badge bg-success-subtle text-success px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>ตรงเวลา
                                        </span>
                                    @elseif($record->status === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>มาสาย
                                        </span>
                                    @elseif($record->status === 'leave')
                                        <span class="badge bg-info-subtle text-info px-2 py-1">
                                            <i class="bi bi-sun-fill me-1"></i>ลางาน (อนุมัติแล้ว)
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>ขาดงาน
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $record->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">ยังไม่มีประวัติการลงเวลา</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Live Clock Function
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElem = document.getElementById('attendanceLiveClock');
        if (clockElem) {
            clockElem.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateClock, 1000);
</script>
@endsection
