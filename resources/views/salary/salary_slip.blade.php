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
                        <p class="text-muted">เพื่อความปลอดภัย กรุณากรอกเลขประจำตัวประชาชน 13 หลัก เพื่อยืนยันตัวตนก่อนดาวน์โหลดสลิปเงินเดือน</p>
                    </div>

                    <form action="{{ route('salary_slip.verify', [], false) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="month" class="form-label">เดือน</label>
                                <select class="form-select" id="month" name="month" required>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ date('m') == $i ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="year" class="form-label">ปี</label>
                                <select class="form-select" id="year" name="year" required>
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="id_card" class="form-label fw-bold">เลขประจำตัวประชาชน 13 หลัก</label>
                            <input type="password" class="form-control form-control-lg text-center letter-spacing-2" 
                                id="id_card" name="id_card" maxlength="13" 
                                placeholder="X-XXXX-XXXXX-XX-X" required>
                            @error('id_card')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-unlock-fill me-2"></i> ยืนยันและดาวน์โหลดสลิป
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-2 {
        letter-spacing: 2px;
    }
</style>
@endsection
