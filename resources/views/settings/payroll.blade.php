@extends('layouts.app')

@section('title', 'ตั้งค่ารายการหักเงินเดือน')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i>ตั้งค่าเกณฑ์การหักเงิน (ภาษี & ประกันสังคม)</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.payroll.update', [], false) }}" method="POST">
                        @csrf
                        
                        <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2"><i class="bi bi-shield-plus text-success me-2"></i>ส่วนที่ 1: ประกันสังคม (Social Security)</h6>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="ss_min_salary" class="form-label">ฐานเงินเดือนขั้นต่ำที่ถูกหัก (บาท)</label>
                                <input type="number" class="form-control" id="ss_min_salary" name="ss_min_salary" value="{{ old('ss_min_salary', $setting->ss_min_salary ?? 0) }}" required>
                                <small class="text-muted">กำหนดเป็น 1 หากต้องการให้หักทุกคนที่มีเงินเดือน</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ss_percent" class="form-label">เปอร์เซ็นต์การหัก (%)</label>
                                <input type="number" step="0.01" class="form-control" id="ss_percent" name="ss_percent" value="{{ old('ss_percent', $setting->ss_percent ?? 5.00) }}" required>
                                <small class="text-muted">ปกติ 5%</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ss_max_deduction" class="form-label">เพดานหักสูงสุด (บาท)</label>
                                <input type="number" class="form-control" id="ss_max_deduction" name="ss_max_deduction" value="{{ old('ss_max_deduction', $setting->ss_max_deduction ?? 750) }}" required>
                                <small class="text-muted">ปกติสูงสุด 750 บาท/เดือน</small>
                            </div>
                        </div>

                        <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2"><i class="bi bi-bank text-danger me-2"></i>ส่วนที่ 2: หักภาษี ณ ที่จ่าย (Withholding Tax)</h6>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="tax_min_salary" class="form-label">ฐานเงินเดือนขั้นต่ำที่ต้องเสียภาษี (บาท)</label>
                                <input type="number" class="form-control" id="tax_min_salary" name="tax_min_salary" value="{{ old('tax_min_salary', $setting->tax_min_salary ?? 26000) }}" required>
                                <small class="text-muted">เช่น 26,000 บาทขึ้นไป</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tax_percent" class="form-label">เปอร์เซ็นต์การหัก (%)</label>
                                <input type="number" step="0.01" class="form-control" id="tax_percent" name="tax_percent" value="{{ old('tax_percent', $setting->tax_percent ?? 3.00) }}" required>
                                <small class="text-muted">เช่น หักเหมา 3%</small>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>บันทึกการตั้งค่า</button>
                        </div>
                    </form>

                </div>
            </div>
            
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>คำแนะนำ:</strong> ค่าเกณฑ์ที่ตั้งไว้นี้ จะถูกนำไปคำนวณอัตโนมัติเมื่อพนักงานดาวน์โหลดสลิปเงินเดือน 
                หากฐานเงินเดือน (Base Salary) ของพนักงานไม่ถึงเกณฑ์ขั้นต่ำที่ตั้งไว้ ยอดหักรายการนั้นๆ จะเป็น 0 บาท
            </div>
        </div>
    </div>
</div>
@endsection
