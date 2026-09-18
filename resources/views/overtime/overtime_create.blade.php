@extends('layouts.app')

@section('title', 'ขอทำล่วงเวลา (OT)')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-wallet-fill me-2"></i>ยื่นคำขอทำล่วงเวลา (OT)</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('overtime.request') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        
                        <div class="mb-3">
                            <label for="date" class="form-label fw-bold">วันที่ต้องการทำ OT <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="hours" class="form-label fw-bold">จำนวนชั่วโมง <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="hours" name="hours" step="1" min="1" max="24" required placeholder="เช่น 2">
                                <span class="input-group-text">ชั่วโมง</span>
                            </div>
                            <div class="form-text">ระบุเป็นตัวเลขจำนวนเต็มเท่านั้น เช่น 1, 2, 3</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">เหตุผล/รายละเอียดงาน <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required placeholder="ระบุงานที่ต้องทำล่วงเวลา..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> ส่งคำขอ
                            </button>
                            <a href="{{ route('overtime.show') }}" class="btn btn-light rounded-pill py-2 text-secondary">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
