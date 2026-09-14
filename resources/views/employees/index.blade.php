@extends('layouts.app')

@section('title', 'จัดการข้อมูลพนักงาน')

@push('styles')
<style>
    .emp-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
    }
    .table-custom {
        color: var(--text-main);
        vertical-align: middle;
    }
    .table-custom th {
        background: var(--badge-bg);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--surface-border);
        padding: 14px 18px;
    }
    .table-custom td {
        background: transparent;
        color: var(--text-main);
        border-bottom: 1px solid var(--surface-border);
        padding: 16px 18px;
        font-size: 0.92rem;
    }
    .emp-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }
    .filter-input {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
    }
    .filter-input:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        color: var(--text-main);
        box-shadow: 0 0 0 3px var(--accent-glow);
        outline: none;
    }
    .filter-input option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    .btn-create-emp {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 10px 22px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px -6px rgba(99, 102, 241, 0.6);
    }
    .btn-create-emp:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 12px 22px -6px rgba(99, 102, 241, 0.8);
    }
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        text-decoration: none;
    }
    .action-btn:hover {
        transform: scale(1.08);
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Top Header & Actions -->
    <div class="col-12">
        <div class="hero-welcome-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-purple">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ระบบจัดการข้อมูลพนักงาน</h3>
                        <p class="text-muted mb-0 small">
                            จัดการบัญชี กำหนดสิทธิ์ แผนก และตำแหน่งงาน
                            @if(Auth::user()->isHr())
                                <span class="badge bg-warning-subtle text-warning ms-2"><i class="bi bi-info-circle me-1"></i>โหมด HR: จัดการพนักงานทั่วไป</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger ms-2"><i class="bi bi-shield-check me-1"></i>โหมด Admin: จัดการได้ทุกคน</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('employees.create', [], false) }}" class="btn btn-create-emp d-flex align-items-center gap-2 text-decoration-none">
                        <i class="bi bi-person-plus-fill fs-5"></i>
                        <span>เพิ่มพนักงานใหม่</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">พนักงานทั้งหมด</span>
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalEmployees) }} <span class="fs-6 text-muted fw-normal">คน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-indigo" style="width: 44px; height: 44px;">
                <i class="bi bi-people-fill fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">สถานะปฏิบัติงาน (Active)</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($activeEmployees) }} <span class="fs-6 text-muted fw-normal">คน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-green" style="width: 44px; height: 44px;">
                <i class="bi bi-person-check-fill fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">ลาออกแล้ว (Resigned)</span>
                <h4 class="fw-bold mb-0 text-danger">{{ number_format($resignedEmployees) }} <span class="fs-6 text-muted fw-normal">คน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-amber" style="width: 44px; height: 44px;">
                <i class="bi bi-person-x-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="col-12">
        <div class="emp-card p-4">
            <form method="GET" action="{{ route('employees.index', [], false) }}" class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text filter-input border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-input border-start-0" 
                            placeholder="ค้นหารหัส, ชื่อ, อีเมล, เบอร์โทร..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select name="department_id" class="form-select filter-input">
                        <option value="">-- ทุกแผนก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="role" class="form-select filter-input">
                        <option value="">-- ทุกระดับสิทธิ์ --</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                        <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="status" class="form-select filter-input">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center" title="ค้นหา">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    @if(request()->hasAny(['search', 'department_id', 'role', 'status']))
                        <a href="{{ route('employees.index', [], false) }}" class="btn btn-outline-secondary rounded-3 d-flex align-items-center justify-content-center" title="ล้างตัวกรอง">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Employees List Table -->
    <div class="col-12">
        <div class="emp-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">รหัสพนักงาน</th>
                            <th>พนักงาน</th>
                            <th>แผนก / ตำแหน่ง</th>
                            <th>ระดับสิทธิ์</th>
                            <th>วันเริ่มงาน</th>
                            <th>สถานะ</th>
                            <th class="text-end" style="width: 140px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace px-2 py-1">
                                        {{ $emp->emp_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="emp-avatar">
                                            {{ mb_substr($emp->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('employees.show', $emp, false) }}" class="fw-bold text-decoration-none text-theme d-block">
                                                {{ $emp->name }}
                                            </a>
                                            <span class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $emp->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $emp->department->name ?? '-' }}</div>
                                    <span class="text-muted small">{{ $emp->position->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($emp->role === 'admin')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="bi bi-shield-shaded me-1"></i>Admin
                                        </span>
                                    @elseif($emp->role === 'hr')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="bi bi-person-gear me-1"></i>HR
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-person me-1"></i>Employee
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small font-monospace">
                                        {{ $emp->start_date ? $emp->start_date->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($emp->status === 'active')
                                        <span class="badge bg-success-subtle text-success px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>Resigned
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- ดูโปรไฟล์ (ทุกคนดูได้) -->
                                        <a href="{{ route('employees.show', $emp, false) }}" class="action-btn bg-info-subtle text-info" title="ดูโปรไฟล์">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <!-- แก้ไข (Admin แก้ไขได้ทุกคน, HR แก้ไข Admin ไม่ได้) -->
                                        @if(Auth::user()->isAdmin() || (Auth::user()->isHr() && !$emp->isAdmin()))
                                            <a href="{{ route('employees.edit', $emp, false) }}" class="action-btn bg-warning-subtle text-warning" title="แก้ไข">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @else
                                            <span class="action-btn bg-secondary-subtle text-muted opacity-50" title="ไม่มีสิทธิ์แก้ไขบัญชี Admin">
                                                <i class="bi bi-lock-fill"></i>
                                            </span>
                                        @endif

                                        <!-- ลบ (Admin ลบได้ทุกคนยกเว้นตัวเอง, HR ลบ Admin ไม่ได้) -->
                                        @if(Auth::user()->id !== $emp->id && (Auth::user()->isAdmin() || (Auth::user()->isHr() && !$emp->isAdmin())))
                                            <form action="{{ route('employees.destroy', $emp, false) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบพนักงาน {{ $emp->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn bg-danger-subtle text-danger border-0" title="ลบพนักงาน">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    <span>ไม่พบข้อมูลพนักงานตามเงื่อนไขที่ค้นหา</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
                <div class="p-3 border-top border-theme d-flex justify-content-center">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
