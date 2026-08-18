@extends('layout')

@section('title', 'ส่งข้อมูลแจ้งเคลมสินค้าชำรุด (Product Claim Form)')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card bg-dark text-white border-0 shadow-lg" style="background: rgba(30, 41, 59, 0.7) !important; backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08) !important;">
            <div class="card-body p-4 p-md-5">
                <h2 class="text-center mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700;">
                    <i class="bi bi-wrench-adjustable-circle-fill me-2"></i>แจ้งเคลมสินค้าชำรุด
                </h2>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 text-white" role="alert" style="background: rgba(16, 185, 129, 0.2); border-left: 4px solid #10b981 !important;">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('claim_store') }}" method="post" novalidate>
                    @csrf

                    <!-- Serial Number -->
                    <div class="mb-3">
                        <label for="serial_number" class="form-label fw-500">รหัสสินค้า (Serial Number)</label>
                        <input type="text" 
                               id="serial_number" 
                               name="serial_number" 
                               class="form-control bg-dark text-white border-secondary @error('serial_number') is-invalid @enderror" 
                               value="{{ old('serial_number') }}" 
                               placeholder="เช่น SN12345678"
                               style="background: rgba(15, 23, 42, 0.6) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                        @error('serial_number')
                            <div class="invalid-feedback text-danger mt-1">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Contact Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-500">อีเมลผู้ติดต่อ (Contact Email)</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control bg-dark text-white border-secondary @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="example@domain.com"
                               style="background: rgba(15, 23, 42, 0.6) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                        @error('email')
                            <div class="invalid-feedback text-danger mt-1">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Urgency Level -->
                    <div class="mb-3">
                        <label for="urgency" class="form-label fw-500">ระดับความเร่งด่วน (Urgency Level)</label>
                        <select id="urgency" 
                                name="urgency" 
                                class="form-select bg-dark text-white border-secondary @error('urgency') is-invalid @enderror"
                                style="background: rgba(15, 23, 42, 0.6) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #fff;">
                            <option value="" disabled {{ old('urgency') === null ? 'selected' : '' }}>-- เลือกระดับความเร่งด่วน --</option>
                            <option value="low" {{ old('urgency') === 'low' ? 'selected' : '' }}>ต่ำ (Low)</option>
                            <option value="medium" {{ old('urgency') === 'medium' ? 'selected' : '' }}>ปานกลาง (Medium)</option>
                            <option value="high" {{ old('urgency') === 'high' ? 'selected' : '' }}>สูง (High)</option>
                        </select>
                        @error('urgency')
                            <div class="invalid-feedback text-danger mt-1">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Damage Details / Symptom -->
                    <div class="mb-4">
                        <label for="symptom" class="form-label fw-500">อาการชำรุด (Damage Details)</label>
                        <textarea id="symptom" 
                                  name="symptom" 
                                  class="form-control bg-dark text-white border-secondary @error('symptom') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="ระบุรายละเอียดอาการชำรุดของสินค้าโดยละเอียด..."
                                  style="background: rgba(15, 23, 42, 0.6) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">{{ old('symptom') }}</textarea>
                        @error('symptom')
                            <div class="invalid-feedback text-danger mt-1">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/blogs" class="btn btn-outline-light px-4 py-2 order-2 order-md-1">
                            <i class="bi bi-arrow-left me-1"></i>กลับหน้ารวม
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 order-1 order-md-2" style="background: var(--primary-gradient); border: none;">
                            <i class="bi bi-send-fill me-1"></i>ส่งข้อมูลแจ้งเคลม
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
