@extends('layouts.app')

@section('title', 'จัดการคำขอทำงานล่วงเวลา (OT)')

@push('styles')
<style>
    .ot-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 2rem;
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
    .ot-header.handled {
        background: linear-gradient(135deg, #334155 0%, #0f172a 100%);
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
        text-align: center;
    }
    .ot-table td {
        padding: 12px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 0.9rem;
        text-align: center;
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ส่วนที่ 1: รายการรออนุมัติ -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="ot-card fade-in">
                <div class="ot-header">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2"></i>คำขอ OT ที่รอการอนุมัติ (Pending)</h5>
                </div>
                
                <div class="card-body p-4">
                    @if(count($pendingRequests) > 0)
                        <form action="{{ route('overtime.bulkApprove') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success fw-bold">
                                    <i class="bi bi-check2-all me-1"></i> อนุมัติรายการที่เลือก
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="ot-table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                            <th>วันที่ขอทำ OT</th>
                                            <th>ชื่อพนักงาน</th>
                                            <th>จำนวนชั่วโมง</th>
                                            <th>รายละเอียดงาน</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingRequests as $request)
                                            <tr>
                                                <td><input type="checkbox" name="overtime_ids[]" value="{{ $request->id }}" class="form-check-input ot-checkbox"></td>
                                                <td class="fw-medium">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                                                <td>{{ $request->user->name }}</td>
                                                <td><span class="badge bg-primary fs-6">{{ $request->hours }} ชม.</span></td>
                                                <td class="text-start text-muted">{{ $request->description }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <!-- ปุ่มอนุมัติเดี่ยว -->
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $request->id }}').submit();"><i class="bi bi-check-lg"></i> อนุมัติ</button>
                                                        <!-- ปุ่มปฏิเสธ (เปิด Modal) -->
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                            <i class="bi bi-x-lg"></i> ปฏิเสธ
                                                        </button>
                                                    </div>

                                                    <!-- Modal ปฏิเสธ -->
                                                    <div class="modal fade text-start" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content border-0 shadow">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title"><i class="bi bi-x-circle-fill me-2"></i>ปฏิเสธคำขอ OT</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>ปฏิเสธคำขอของ <strong>{{ $request->user->name }}</strong> (จำนวน {{ $request->hours }} ชม.)</p>
                                                                    <!-- ย้าย form ปฏิเสธมาไว้ที่ปุ่ม หรือครอบเฉพาะเนื้อหา -->
                                                                </div>
                                                                <div class="modal-footer d-block">
                                                                    <form action="{{ route('overtime.reject', $request->id) }}" method="POST" class="d-flex flex-column w-100">
                                                                        @csrf
                                                                        <div class="mb-3">
                                                                            <label for="hr_reject_reason" class="form-label fw-bold text-dark">เหตุผลที่ปฏิเสธ <span class="text-danger">*</span></label>
                                                                            <textarea class="form-control" name="hr_reject_reason" rows="3" required placeholder="กรุณาระบุเหตุผลเพื่อให้พนักงานทราบ..."></textarea>
                                                                        </div>
                                                                        <div class="d-flex justify-content-end gap-2">
                                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                                                                            <button type="submit" class="btn btn-danger">ยืนยันการปฏิเสธ</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        
                        <!-- ซ่อน Form อนุมัติเดี่ยวไว้ข้างนอกป้องกัน Form ซ้อน Form -->
                        @foreach($pendingRequests as $request)
                            <form id="approve-form-{{ $request->id }}" action="{{ route('overtime.approve', $request->id) }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endforeach
                    @else
                        <div class="ot-empty-state">
                            <i class="bi bi-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">ไม่มีคำขอ OT ที่รอการอนุมัติ</h6>
                            <p class="mb-0 small">พนักงานทั้งหมดได้รับการจัดการเรียบร้อยแล้ว</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ส่วนที่ 2: ประวัติการทำรายการ -->
    <div class="row justify-content-center mt-3">
        <div class="col-lg-12">
            <div class="ot-card fade-in">
                <div class="ot-header handled">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>ประวัติคำขอที่จัดการแล้ว (Handled)</h5>
                </div>
                
                <div class="card-body p-4">
                    @if(count($handledRequests) > 0)
                        <div class="table-responsive">
                            <table class="ot-table text-center">
                                <thead>
                                    <tr>
                                        <th>วันที่ทำ OT</th>
                                        <th>ชื่อพนักงาน</th>
                                        <th>ชั่วโมง OT</th>
                                        <th>รายละเอียดงาน</th>
                                        <th>สถานะ</th>
                                        <th>เหตุผลการปฏิเสธ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($handledRequests as $request)
                                        <tr>
                                            <td class="fw-medium">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                                            <td>{{ $request->user->name }}</td>
                                            <td>{{ $request->hours }} ชม.</td>
                                            <td class="text-start text-muted small">{{ $request->description }}</td>
                                            <td>
                                                @if($request->status == 'approved')
                                                    <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle-fill me-1"></i>อนุมัติแล้ว</span>
                                                @elseif($request->status == 'rejected')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-x-circle-fill me-1"></i>ปฏิเสธ</span>
                                                @endif
                                            </td>
                                            <td class="text-danger small text-start">{{ $request->hr_reject_reason ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="ot-empty-state">
                            <i class="bi bi-inbox text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">ยังไม่มีประวัติการทำรายการ</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // สคริปต์สำหรับ Checkbox เลือกทั้งหมด (Bulk Approve)
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const otCheckboxes = document.querySelectorAll('.ot-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                otCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    });
</script>
@endpush
@endsection