@extends('layouts.app')

@section('title', 'เพิ่มพนักงานใหม่')

@push('styles')
<style>
    .form-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
    }
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 14px;
        padding: 0.8rem 1.1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
        outline: none;
    }
    .form-control-custom option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    .form-label-custom {
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 12px 32px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.6);
    }
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 14px 24px -10px rgba(99, 102, 241, 0.8);
    }
    .btn-cancel-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-muted);
        border-radius: 14px;
        padding: 12px 28px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .btn-cancel-custom:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        
        <!-- Header Banner -->
        <div class="hero-welcome-card p-4 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="action-icon icon-purple">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 gradient-text">เพิ่มพนักงานใหม่</h3>
                    <p class="text-muted mb-0 small">
                        กรอกข้อมูลพนักงานเพื่อสร้างบัญชีผู้ใช้งาน และระบบจะจัดสรรโควตาวันลาเริ่มต้นให้อัตโนมัติ
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card p-4 p-md-5 mb-5">
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 border-0 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <div class="d-flex align-items-center mb-2 fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> โปรดตรวจสอบข้อผิดพลาดด้านล่าง
                    </div>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employees.store', [], false) }}">
                @csrf

                <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge"></i> ข้อมูลบัญชีผู้ใช้และระดับสิทธิ์
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="emp_code" class="form-label form-label-custom">
                            รหัสพนักงาน <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-custom" id="emp_code" name="emp_code" 
                            value="{{ old('emp_code') }}" placeholder="เช่น EMP004" required>
                    </div>

                    <div class="col-md-4">
                        <label for="role" class="form-label form-label-custom">
                            ระดับสิทธิ์ (Role) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-control-custom" id="role" name="role" required>
                            @foreach($allowedRoles as $roleKey => $roleLabel)
                                <option value="{{ $roleKey }}" {{ old('role') == $roleKey ? 'selected' : '' }}>
                                    {{ $roleLabel }}
                                </option>
                            @endforeach
                        </select>
                        @if(Auth::user()->isHr())
                            <div class="form-text small text-warning"><i class="bi bi-shield-exclamation me-1"></i>บัญชี HR กำหนดสิทธิ์ได้เฉพาะ Employee เท่านั้น</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label form-label-custom">
                            สถานะการทำงาน <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-control-custom" id="status" name="status" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>กำลังปฏิบัติงาน (Active)</option>
                            <option value="resigned" {{ old('status') == 'resigned' ? 'selected' : '' }}>พ้นสภาพ/ลาออก (Resigned)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="email" class="form-label form-label-custom">
                            อีเมล (ใช้เข้าสู่ระบบ) <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email" 
                            value="{{ old('email') }}" placeholder="employee@example.com" required>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label form-label-custom">
                            รหัสผ่านเริ่มต้น <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control form-control-custom" id="password" name="password" 
                            placeholder="อย่างน้อย 6 ตัวอักษร" required>
                    </div>
                </div>

                <hr class="my-4 opacity-25">

                <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill"></i> ข้อมูลส่วนตัวและการทำงาน
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label form-label-custom">
                            ชื่อ - นามสกุล <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-custom" id="name" name="name" 
                            value="{{ old('name') }}" placeholder="เช่น สมเกียรติ สว่างดี" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label form-label-custom">
                            เบอร์โทรศัพท์
                        </label>
                        <input type="text" class="form-control form-control-custom" id="phone" name="phone" 
                            value="{{ old('phone') }}" placeholder="เช่น 081-234-5678">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="department_id" class="form-label form-label-custom">
                            แผนกงาน
                        </label>
                        <select class="form-select form-control-custom" id="department_id" name="department_id">
                            <option value="">-- ไม่ระบุแผนก --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="position_id" class="form-label form-label-custom">
                            ตำแหน่งงาน
                        </label>
                        <select class="form-select form-control-custom" id="position_id" name="position_id">
                            <option value="">-- ไม่ระบุตำแหน่ง --</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" 
                                    data-department="{{ $pos->department_id ?? '' }}"
                                    {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="no-pos-warning" class="text-danger small mt-1 d-none">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> แผนกนี้ยังไม่มีตำแหน่งงาน ไม่สามารถบันทึกได้ กรุณาไปเพิ่มตำแหน่งงานก่อน
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="start_date" class="form-label form-label-custom">
                            วันที่เริ่มงาน
                        </label>
                        <input type="date" class="form-control form-control-custom" id="start_date" name="start_date" 
                            value="{{ old('start_date', date('Y-m-d')) }}">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="address" class="form-label form-label-custom">
                        ที่อยู่ปัจจุบัน
                    </label>
                    <textarea class="form-control form-control-custom" id="address" name="address" rows="3" 
                        placeholder="ระบุที่อยู่ติดต่อได้...">{{ old('address') }}</textarea>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-4" style="border-top: 1px dashed var(--surface-border);">
                    <a href="{{ route('employees.index', [], false) }}" class="btn btn-cancel-custom d-flex align-items-center gap-2">
                        <i class="bi bi-x-lg"></i> ยกเลิก
                    </a>
                    <button type="submit" id="btn-submit-emp" class="btn btn-submit-custom d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle fs-5"></i> บันทึกข้อมูลพนักงาน
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deptSelect = document.getElementById('department_id');
        const posSelect = document.getElementById('position_id');
        const submitBtn = document.getElementById('btn-submit-emp');
        const warningDiv = document.getElementById('no-pos-warning');
        if (!deptSelect || !posSelect) return;

        // เก็บ options ทั้งหมดของตำแหน่งงานไว้
        const allPosOptions = Array.from(posSelect.querySelectorAll('option')).slice(1); // ข้าม option แรก ("-- ไม่ระบุตำแหน่ง --")

        function filterPositions() {
            const selectedDept = deptSelect.value;
            const currentSelectedPos = posSelect.value;

            // รีเซ็ตตัวเลือกใน dropdown ตำแหน่ง
            posSelect.innerHTML = '<option value="">-- ไม่ระบุตำแหน่ง --</option>';

            let matchCount = 0;

            allPosOptions.forEach(opt => {
                const optDept = opt.getAttribute('data-department');
                // เงื่อนไข:
                // 1. ถ้ายังไม่ได้เลือกแผนก -> แสดงตำแหน่งทั้งหมด
                // 2. ถ้าเลือกแผนกแล้ว -> แสดงเฉพาะตำแหน่งที่ตรงกับแผนกนั้น (optDept === selectedDept) เท่านั้น!
                if (!selectedDept || (optDept && optDept === selectedDept)) {
                    const cloned = opt.cloneNode(true);
                    if (cloned.value === currentSelectedPos) {
                        cloned.selected = true;
                    }
                    posSelect.appendChild(cloned);
                    if (selectedDept) {
                        matchCount++;
                    }
                }
            });

            // หากเลือกแผนกแล้ว แต่ไม่พบตำแหน่งงานใดๆ เลยที่สังกัดแผนกนี้
            if (selectedDept && matchCount === 0) {
                const noPosOpt = document.createElement('option');
                noPosOpt.value = '';
                noPosOpt.textContent = '⚠️ ไม่พบตำแหน่งงานของแผนกนี้ (ไม่สามารถบันทึกได้)';
                noPosOpt.disabled = true;
                noPosOpt.selected = true;
                posSelect.appendChild(noPosOpt);

                // ปิดปุ่มบันทึก และแจ้งเตือนสีแดง
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50');
                    submitBtn.setAttribute('title', 'แผนกที่เลือกยังไม่มีตำแหน่งงาน');
                }
                if (warningDiv) {
                    warningDiv.classList.remove('d-none');
                }
            } else {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50');
                    submitBtn.removeAttribute('title');
                }
                if (warningDiv) {
                    warningDiv.classList.add('d-none');
                }
            }
        }

        // ป้องกันการ submit ฟอร์มหากแผนกไม่มีตำแหน่งงาน
        const form = deptSelect.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                const selectedDept = deptSelect.value;
                if (selectedDept) {
                    // ตรวจสอบว่าใน posSelect มี option ที่มีค่าหรือไม่
                    const validOptions = Array.from(posSelect.options).filter(o => o.value !== '');
                    if (validOptions.length === 0) {
                        e.preventDefault();
                        alert('แผนกที่เลือกยังไม่มีตำแหน่งงาน ไม่สามารถบันทึกข้อมูลพนักงานได้ กรุณาไปเพิ่มตำแหน่งงานของแผนกนี้ก่อน');
                        return false;
                    }
                    if (!posSelect.value) {
                        e.preventDefault();
                        alert('กรุณาเลือกตำแหน่งงานของพนักงาน');
                        posSelect.focus();
                        return false;
                    }
                }
            });
        }

        deptSelect.addEventListener('change', filterPositions);
        filterPositions(); // รันตอนโหลดหน้าแรกเผื่อมี old input
    });
</script>
@endpush
@endsection
