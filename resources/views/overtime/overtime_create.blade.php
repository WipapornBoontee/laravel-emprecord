@extends('layouts.app')

@section('title', 'ขอทำล่วงเวลา (OT)')

@push('styles')
<style>
    .ot-form-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 12px;
    }
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
    }
    .form-control-custom:read-only {
        background: var(--badge-bg);
        color: var(--text-muted);
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="ot-form-card p-4">
                <div class="border-bottom border-theme pb-3 mb-4">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-clock-history me-2"></i>ยื่นคำขอทำงานล่วงเวลา (OT)</h5>
                </div>
                <div>
                    <form action="{{ route('overtime.request') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        
                        <div class="mb-3">
                            <label for="date" class="form-label fw-bold text-theme small">วันที่ต้องการทำ OT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-custom" id="date_display" value="{{ date('d/m/Y') }}" readonly>
                            <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="hours" class="form-label fw-bold text-theme small">จำนวนชั่วโมง <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-custom" id="hours" name="hours" step="1" min="1" max="24" required placeholder="OT">
                                <span class="input-group-text bg-transparent border-theme text-theme">ชั่วโมง</span>
                            </div>
                            <div class="form-text small text-muted">ระบุเป็นตัวเลขจำนวนเต็มเท่านั้น เช่น 1, 2, 3</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-theme small">เหตุผล/รายละเอียดงาน <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-custom" id="description" name="description" rows="3" required placeholder="ระบุงานที่ต้องทำล่วงเวลา..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> ส่งคำขอ
                            </button>
                            <a href="{{ route('overtime.show') }}" class="btn btn-outline-secondary rounded-pill py-2">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
