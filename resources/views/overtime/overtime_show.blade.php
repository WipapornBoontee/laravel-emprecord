@extends('layouts.app')

@section('title', 'สรุปค่าล่วงเวลา (OT)')

@push('styles')
<style>
    .ot-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        overflow: hidden;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .ot-header {
        background: var(--primary-gradient);
        padding: 20px 25px;
        color: white;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .ot-month-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .ot-stat-card {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .ot-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.04);
        border-color: var(--card-hover-border);
    }
    .ot-stat-title {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .ot-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    .value-hours {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .value-pay {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .ot-empty-state {
        padding: 40px 20px;
        text-align: center;
        background: var(--badge-bg);
        border-radius: 16px;
        border: 1px dashed var(--surface-border);
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="ot-card fade-in">
                
                <div class="ot-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; font-size: 1.25rem;">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">สรุปล่วงเวลา (OT)</h5>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="ot-month-badge">
                            <i class="bi bi-calendar-event me-2"></i>เดือน {{ Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}
                        </div>
                        @if(!$hasCheckedInToday)
                            <button type="button" class="btn btn-light text-muted fw-bold rounded-pill px-4 shadow-sm" style="border: 2px solid rgba(255,255,255,0.5); opacity: 0.7;" disabled title="ต้องสแกนเข้างานก่อนถึงจะขอ OT ได้">
                                <i class="bi bi-plus-circle-fill me-2"></i> ขอทำ OT
                            </button>
                        @elseif(isset($hasAnyOTToday) && $hasAnyOTToday)
                            <button type="button" class="btn btn-light text-muted fw-bold rounded-pill px-4 shadow-sm" style="border: 2px solid rgba(255,255,255,0.5); opacity: 0.7;" disabled title="คุณได้ขอ OT สำหรับวันนี้ไปแล้ว">
                                <i class="bi bi-check-circle-fill me-2"></i> ขอทำ OT ไปแล้ว
                            </button>
                        @else
                            <a href="{{ route('overtime.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm" style="border: 2px solid rgba(255,255,255,0.5);">
                                <i class="bi bi-plus-circle-fill me-2"></i> ขอทำ OT
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(isset($hasOTToday) && $hasOTToday && \Carbon\Carbon::now()->format('H:i') < '17:30')
                        @php
                            // คำนวณเวลาสิ้นสุด OT จากชั่วโมงที่ขอไว้
                            $otMinutes = (int) ($todayOtHours * 60);
                            $endTime = \Carbon\Carbon::createFromTimeString('17:30:00')->addMinutes($otMinutes)->format('H:i');
                        @endphp
                        <div class="alert alert-success d-flex align-items-center fade-in shadow-sm border-0" role="alert" style="background-color: #d1fae5; color: #065f46;">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div class="fw-bold">เริ่มเข้างานทำ OT ตามที่ขอ ในเวลา 17.30 น. - {{ $endTime }} น. (จำนวน {{ $todayOtHours }} ชม.)</div>
                        </div>
                    @endif

                    <!-- Summary Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="salary-header-gradient text-center d-flex flex-column align-items-center">
                    <div class="avatar-wrapper rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px; background-color: var(--badge-bg); border: 2px solid var(--surface-border);">
                        <i class="bi bi-person-fill" style="font-size: 2.5rem; color: var(--text-main);"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ Auth::user()->name }}</h4>
                    <p class="mb-0 opacity-75 small"><i class="bi bi-person-badge me-1"></i>รหัสพนักงาน: {{ Auth::user()->emp_code ?? '-' }}</p>
                </div>
                               
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="ot-stat-card">
                                <p class="ot-stat-title"><i class="bi bi-stopwatch me-2 text-primary"></i>ชั่วโมงล่วงเวลาทั้งหมด (เดือนนี้)</p>
                                <h2 class="ot-stat-value value-hours">{{ ($totalOtHours) }} <span class="fs-6 text-muted fw-normal">ชม.</span></h2>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="bi bi-list-columns-reverse text-primary me-2"></i>รายละเอียดการทำ OT รายวัน</h6>
                    
                    @if(count($otDetails) > 0)
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>เวลาสแกนเข้า-ออก</th>
                                        <th>ชั่วโมง OT (ได้จริง)</th>
                                        <th>สถานะการทำ OT</th>
                                        <th>หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otDetails as $detail)
                                        <tr>
                                            <td class="fw-medium">{{ $detail['date'] }}</td>
                                            @if($detail['status'] == 'pending')
                                                <td class="text-muted">-</td>
                                                <td class="text-muted">-</td>
                                                <td><span class="badge bg-secondary">รออนุมัติ</span></td>
                                                <td class="text-muted">รอการอนุมัติจาก HR</td>
                                            @elseif($detail['status'] == 'rejected')
                                                <td class="text-muted">-</td>
                                                <td class="text-muted">-</td>
                                                <td><span class="badge bg-danger">ไม่อนุมัติ</span></td>
                                                <td class="text-danger">{{ $detail['hr_reject_reason'] }}</td>
                                            @else
                                                <td class="text-muted">เริ่ม 17:00 น. - <span class="badge {{ $detail['check_out'] ? 'bg-secondary-subtle text-secondary' : 'bg-secondary' }} px-2 py-1">{{ $detail['check_out'] ? $detail['check_out'].' น.' : 'ยังไม่สแกนออก' }}</span></td>
                                                <td>
                                                    @if($detail['check_out'])
                                                        <div class="fw-bold text-primary text-center">{{ $detail['ot_hours'] }} ชม.</div>
                                                        <div class="small text-muted mb-1 text-center">
                                                            (ขอไว้ {{ $detail['requested_hours'] }} ชม. | ทำได้ {{ floor($detail['actual_minutes']/60) }} ชม. {{ $detail['actual_minutes']%60 }} นาที)
                                                        </div>
                                                    @else
                                                        <div class="fw-bold text-muted text-center">-</div>
                                                        <div class="small text-muted mb-1">(ขอไว้ {{ $detail['requested_hours'] }} ชม.)</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$detail['check_out'])
                                                        <span class="badge bg-info">รอสแกนออก</span>
                                                    @elseif($detail['ot_hours'] >= $detail['requested_hours'])
                                                        <span class="badge bg-success">ทำเสร็จ</span>
                                                    @else
                                                        <div class="mb-1"><span class="badge bg-warning text-dark">ยังไม่เสร็จ</span></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($detail['check_out'] && $detail['ot_hours'] < $detail['requested_hours'])
                                                        @if($detail['early_checkout_reason'])
                                                            <div class="small text-success"><i class="bi bi-check-circle-fill"></i> แจ้ง HR แล้ว</div>
                                                        @else
                                                            <form action="{{ route('overtime.reason') }}" method="POST" class="d-flex flex-column align-items-center mt-2">
                                                                @csrf
                                                                <input type="hidden" name="overtime_id" value="{{ $detail['overtime_id'] }}">
                                                                <div class="input-group input-group-sm w-100">
                                                                    <input type="text" name="early_checkout_reason" class="form-control form-control-sm" placeholder="เหตุผลที่กลับก่อน..." required>
                                                                    <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-send-fill"></i></button>
                                                                </div>
                                                            </form>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="ot-empty-state">
                            <i class="bi bi-inbox-fill text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">ไม่มีข้อมูลการทำล่วงเวลาในเดือนนี้</h6>
                            <p class="mb-0 small">การทำล่วงเวลาจะนับเฉพาะการเช็คเอาท์หลัง 17:00 น. เท่านั้น</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection