@extends('layouts.app')

@section('title', 'ดาวน์โหลดสลิปเงินเดือน')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-success text-white text-center py-4 rounded-top-4">
                    <i class="bi bi-file-earmark-pdf fs-1"></i>
                    <h4 class="mb-0 mt-2 fw-bold">ดาวน์โหลดสลิปเงินเดือน</h4>
                </div>
                <div class="card-body p-5">
                    
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <p class="text-muted">เลือกเดือนและปีที่ต้องการดาวน์โหลดสลิปเงินเดือน <br> <span class="text-danger fw-bold"><i class="bi bi-lock-fill"></i> ไฟล์ PDF จะถูกล็อกด้วยรหัสผ่าน<br>(ให้ใช้เลขประจำตัวประชาชน 13 หลักในการเปิด)</span></p>
                    </div>

                    <form action="{{ route('salary_slip.download', [], false) }}" method="GET">
                        <div class="row mb-4">
                            <div class="col-6">
                                <label for="month" class="form-label">เดือน</label>
                                <select class="form-select form-select-lg" id="month" name="month" required>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ date('m') == $i ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="year" class="form-label">ปี</label>
                                <select class="form-select form-select-lg" id="year" name="year" required>
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-download me-2"></i> ดาวน์โหลดไฟล์ PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
