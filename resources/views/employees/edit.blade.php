@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลพนักงาน')

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
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-amber">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">แก้ไขข้อมูลพนักงาน</h3>
                        <p class="text-muted mb-0 small">
                            กำลังแก้ไข: <strong>{{ $employee->name }}</strong> (รหัส: {{ $employee->emp_code }})
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('employees.show', $employee, false) }}" class="btn btn-outline-secondary btn-sm rounded-3">
                        <i class="bi bi-eye me-1"></i> ดูโปรไฟล์
                    </a>
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

            <form method="POST" action="{{ route('employees.update', $employee, false) }}">
                @csrf
                @method('PUT')

                <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge"></i> ข้อมูลบัญชีผู้ใช้และระดับสิทธิ์
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="emp_code" class="form-label form-label-custom">
                            รหัสพนักงาน <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-custom" id="emp_code" name="emp_code" 
                            value="{{ old('emp_code', $employee->emp_code) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="role" class="form-label form-label-custom">
                            ระดับสิทธิ์ (Role) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-control-custom" id="role" name="role" required>
                            @foreach($allowedRoles as $roleKey => $roleLabel)
                                <option value="{{ $roleKey }}" {{ old('role', $employee->role) == $roleKey ? 'selected' : '' }}>
                                    {{ $roleLabel }}
                                </option>
                            @endforeach
                        </select>
                        @if(Auth::user()->isHr())
                            <div class="form-text small text-warning"><i class="bi bi-shield-exclamation me-1"></i>บัญชี HR ไม่สามารถยกระดับเป็น Admin ได้</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label form-label-custom">
                            สถานะการทำงาน <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-control-custom" id="status" name="status" required>
                            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>กำลังปฏิบัติงาน (Active)</option>
                            <option value="resigned" {{ old('status', $employee->status) == 'resigned' ? 'selected' : '' }}>พ้นสภาพ/ลาออก (Resigned)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="email" class="form-label form-label-custom">
                            อีเมล (ใช้เข้าสู่ระบบ) <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email" 
                            value="{{ old('email', $employee->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label form-label-custom">
                            รหัสผ่านใหม่ <span class="text-muted fw-normal small">(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</span>
                        </label>
                        <input type="password" class="form-control form-control-custom" id="password" name="password" 
                            placeholder="ระบุอย่างน้อย 6 ตัวอักษรเพื่อรีเซ็ต">
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
                            value="{{ old('name', $employee->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label form-label-custom">
                            เบอร์โทรศัพท์
                        </label>
                        <input type="text" class="form-control form-control-custom" id="phone" name="phone" 
                            value="{{ old('phone', $employee->phone) }}" placeholder="เช่น 081-234-5678">
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
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
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
                                    {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="start_date" class="form-label form-label-custom">
                            วันที่เริ่มงาน
                        </label>
                        <input type="date" class="form-control form-control-custom" id="start_date" name="start_date" 
                            value="{{ old('start_date', $employee->start_date ? $employee->start_date->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="address" class="form-label form-label-custom">
                        ที่อยู่ปัจจุบัน
                    </label>
                    <textarea class="form-control form-control-custom" id="address" name="address" rows="3" 
                        placeholder="ระบุที่อยู่ติดต่อได้...">{{ old('address', $employee->address) }}</textarea>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-4" style="border-top: 1px dashed var(--surface-border);">
                    <a href="{{ route('employees.index', [], false) }}" class="btn btn-cancel-custom d-flex align-items-center gap-2">
                        <i class="bi bi-x-lg"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-submit-custom d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle fs-5"></i> บันทึกการแก้ไข
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
        if (!deptSelect || !posSelect) return;

        // เก็บ options ทั้งหมดของตำแหน่งงานไว้
        const allPosOptions = Array.from(posSelect.querySelectorAll('option')).slice(1);
        const initialSelectedPos = "{{ old('position_id', $employee->position_id) }}";

        function filterPositions(isInit = false) {
            const selectedDept = deptSelect.value;
            const currentSelectedPos = isInit ? initialSelectedPos : posSelect.value;

            // รีเซ็ตตัวเลือกใน dropdown ตำแหน่ง
            posSelect.innerHTML = '<option value="">-- ไม่ระบุตำแหน่ง --</option>';

            allPosOptions.forEach(opt => {
                const optDept = opt.getAttribute('data-department');
                if (!selectedDept || optDept === selectedDept || !optDept) {
                    const cloned = opt.cloneNode(true);
                    if (cloned.value === currentSelectedPos) {
                        cloned.selected = true;
                    }
                    posSelect.appendChild(cloned);
                }
            });
        }

        deptSelect.addEventListener('change', () => filterPositions(false));
        filterPositions(true);
    });
</script>
@endpush
@endsection
