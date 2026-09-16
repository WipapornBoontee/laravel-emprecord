@extends('layouts.app')

@section('title', 'ค่าล่วงเวลา')

@section('content')
<div class="container py-6">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> สรุปค่าล่วงเวลา (OT)</h5>
                    <span class="badge bg-light text-dark fs-6">เดือน {{ Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4 mt-3">
                        <i class="bi bi-person-circle text-secondary" style="font-size: 3rem;"></i>
                        <h5 class="mt-2">พนักงาน: {{ Auth::user()->name }}</h5>
                        <p class="text-muted">เรทค่าล่วงเวลา: ฿ {{ number_format($hourlyRate, 2) }} / ชม.</p>
                    </div>
                    
                    <div class="row text-center mt-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">ชั่วโมงล่วงเวลาทั้งหมด (เดือนนี้)</p>
                                <h4 class="mb-0 text-primary">{{ number_format($totalOtHours, 2) }} <small class="fs-6 text-muted">ชม.</small></h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">รวมเงินค่าล่วงเวลา</p>
                                <h4 class="mb-0 text-success">฿ {{ number_format($totalOtPay, 2) }}</h4>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-5 mb-3 text-secondary"><i class="bi bi-list-check me-2"></i>รายละเอียดการทำ OT รายวัน</h6>
                    
                    @if(count($otDetails) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>เวลาเข้างาน</th>
                                        <th>เวลาเลิกงาน</th>
                                        <th>ชั่วโมง OT (ตามจริง)</th>
                                        <th>จำนวนเงิน (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otDetails as $detail)
                                        <tr>
                                            <td>{{ $detail['date'] }}</td>
                                            <td>{{ $detail['check_in'] }}</td>
                                            <td class="text-danger fw-bold">{{ $detail['check_out'] }}</td>
                                            <td class="text-primary">
                                                @php
                                                    $h = floor($detail['ot_minutes'] / 60);
                                                    $m = $detail['ot_minutes'] % 60;
                                                @endphp
                                                {{ $h > 0 ? $h . ' ชม. ' : '' }}{{ $m }} นาที <br>
                                                <small class="text-muted">({{ number_format($detail['ot_hours'], 2) }} ชม.)</small>
                                            </td>
                                            <td class="text-success fw-bold">฿ {{ number_format($detail['ot_pay'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center mt-3">
                            <i class="bi bi-info-circle me-2"></i> ไม่มีข้อมูลการทำล่วงเวลาในเดือนนี้ (หลัง 17:00 น.)
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection