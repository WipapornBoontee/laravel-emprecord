@extends('layouts.app')

@section('title', 'สรุปค่าล่วงเวลา (OT)')

@push('styles')
<style>
    .ot-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .ot-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .ot-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.04);
        border-color: #cbd5e1;
    }
    .ot-stat-title {
        color: #64748b;
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
    .ot-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .ot-table th {
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 12px 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    .ot-table th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .ot-table th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    
    .ot-table td {
        padding: 12px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 0.9rem;
    }
    .ot-table tr:hover td {
        background-color: #f8fafc;
    }
    .ot-empty-state {
        padding: 40px 20px;
        text-align: center;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
        color: #64748b;
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
                        <a href="{{ route('overtime.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm" style="border: 2px solid rgba(255,255,255,0.5);">
                            <i class="bi bi-plus-circle-fill me-2"></i> ขอทำ OT
                        </a>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <!-- Summary Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 mb-3">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="salary-header-gradient">
                    <div class="avatar-wrapper">
                        <i class="bi bi-person-fill" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h4 class="fw-bold mb-1">{{ Auth::user()->name }}</h4>
                    <p class="mb-0 opacity-75 small"><i class="bi bi-person-badge me-1"></i>รหัสพนักงาน: {{ Auth::user()->emp_code ?? '-' }}</p>
                </div>
                <div class="col-12">
                    <div class="stat-box">
                        <p class="stat-title"><i class="bi bi-cash-stack me-2"></i>ฐานเงินเดือน (Base Salary)</p>
                        <h2 class="stat-value">฿ {{ number_format($baseSalary, 2) }}</h2>
                    </div>
                </div>
                               
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="ot-stat-card">
                                <p class="ot-stat-title"><i class="bi bi-stopwatch me-2 text-primary"></i>ชั่วโมงล่วงเวลาทั้งหมด (เดือนนี้)</p>
                                <h2 class="ot-stat-value value-hours">{{ number_format($totalOtHours, 2) }} <span class="fs-6 text-muted fw-normal">ชม.</span></h2>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="bi bi-list-columns-reverse text-primary me-2"></i>รายละเอียดการทำ OT รายวัน</h6>
                    
                    @if(count($otDetails) > 0)
                        <div class="table-responsive">
                            <table class="ot-table text-center">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>เวลาเข้างาน</th>
                                        <th>เวลาเลิกงาน</th>
                                        <th>ชั่วโมง OT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otDetails as $detail)
                                        <tr>
                                            <td class="fw-medium">{{ $detail['date'] }}</td>
                                            <td class="text-muted">{{ $detail['check_in'] }}</td>
                                            <td><span class="badge bg-danger-subtle text-danger px-2 py-1">{{ $detail['check_out'] }}</span></td>
                                            <td>
                                                @php
                                                    $h = floor($detail['ot_minutes'] / 60);
                                                    $m = $detail['ot_minutes'] % 60;
                                                @endphp
                                                <div class="fw-bold text-primary">{{ $h > 0 ? $h . ' ชม. ' : '' }}{{ $m }} นาที</div>
                                                <div class="small text-muted">({{ number_format($detail['ot_hours'], 2) }} ชม.)</div>
                                            </td>
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