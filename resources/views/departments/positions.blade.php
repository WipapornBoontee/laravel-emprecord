@extends('layouts.app')

@section('title', 'จัดการตำแหน่งงาน')

@push('styles')
<style>
    .dept-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
    }
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 14px;
        padding: 0.75rem 1.1rem;
        font-size: 0.95rem;
    }
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
        outline: none;
    }
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        color: white;
    }
    .nav-pills-custom .nav-link {
        color: var(--text-muted);
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .nav-pills-custom .nav-link.active {
        background: var(--primary-gradient);
        color: white;
    }
    .table-switch {
        cursor: pointer;
        width: 2.8rem !important;
        height: 1.45rem !important;
    }
    .table-switch:focus {
        box-shadow: 0 0 0 3px var(--accent-glow);
    }
    .table-switch:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="hero-welcome-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-indigo">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">โครงสร้างองค์กร: ตำแหน่งงาน</h3>
                        <p class="text-muted mb-0 small">จัดการรายชื่อตำแหน่งงานสำหรับพนักงานในองค์กร</p>
                    </div>
                </div>

                <!-- Nav Switcher -->
                <ul class="nav nav-pills nav-pills-custom bg-body-tertiary p-1 rounded-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('departments.index', [], false) }}">
                            <i class="bi bi-building me-1"></i> แผนกงาน (Departments)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('departments.positions', [], false) }}">
                            <i class="bi bi-briefcase me-1"></i> ตำแหน่งงาน (Positions)
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin())
    <!-- Add Position Form (เฉพาะ Admin) -->
    <div class="col-lg-4">
        <div class="dept-card p-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> เพิ่มตำแหน่งงานใหม่
            </h5>

            @if ($errors->any())
                <div class="alert alert-danger rounded-3 border-0 small mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('departments.positions.store', [], false) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small text-theme">ชื่อตำแหน่งงาน <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control form-control-custom" 
                        placeholder="เช่น Software Developer, HR Officer" required value="{{ old('name') }}">
                </div>
                <button type="submit" class="btn btn-submit-custom w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i> บันทึกตำแหน่งงาน
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Positions List Table -->
    <div class="{{ Auth::user()->isAdmin() ? 'col-lg-8' : 'col-12' }}">
        <div class="dept-card overflow-hidden">
            <div class="p-4 border-bottom border-theme d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-theme">รายชื่อตำแหน่งงานทั้งหมด ({{ $positions->count() }} ตำแหน่ง)</h5>
                    <small class="text-muted">จัดการสถานะการใช้งาน ดูรายชื่อพนักงาน และแก้ไขข้อมูลตำแหน่ง</small>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>ชื่อตำแหน่ง</th>
                            <th style="width: 130px;">จำนวนพนักงาน</th>
                            <th style="width: 140px;" class="text-center">สถานะ</th>
                            <th class="text-end" style="width: 150px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $index => $pos)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-theme">
                                    <i class="bi bi-award-fill text-warning me-2"></i>{{ $pos->name }}
                                </td>
                                <td>
                                    <button type="button" 
                                        class="badge bg-success-subtle text-success border-0 px-2 py-1 cursor-pointer" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewPosModal{{ $pos->id }}"
                                        title="คลิกเพื่อดูรายชื่อพนักงาน">
                                        <i class="bi bi-people me-1"></i>{{ $pos->users_count }} คน
                                    </button>
                                </td>
                                <td class="text-center">
                                    @if(Auth::user()->isAdmin())
                                        <form action="{{ route('departments.positions.toggleStatus', $pos, false) }}" method="POST" class="d-inline-flex align-items-center justify-content-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                                <input class="form-check-input table-switch m-0" type="checkbox" role="switch" 
                                                    id="switch_pos_{{ $pos->id }}" 
                                                    onchange="this.form.submit()" 
                                                    {{ ($pos->is_active ?? true) ? 'checked' : '' }}
                                                    title="{{ ($pos->is_active ?? true) ? 'คลิกเพื่อปิดใช้งาน' : 'คลิกเพื่อเปิดใช้งาน' }}">
                                                <label class="form-check-label small fw-semibold cursor-pointer {{ ($pos->is_active ?? true) ? 'text-success' : 'text-muted' }}" for="switch_pos_{{ $pos->id }}">
                                                    {{ ($pos->is_active ?? true) ? 'เปิด' : 'ปิด' }}
                                                </label>
                                            </div>
                                        </form>
                                    @else
                                        @if($pos->is_active ?? true)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i>เปิดใช้งาน
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">
                                                <i class="bi bi-pause-circle-fill me-1"></i>ปิดใช้งาน
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <!-- ดูสมาชิก (View) - ทั้ง Admin และ HR ดูได้ -->
                                        <button type="button" class="btn btn-outline-info btn-sm rounded-3 py-1 px-2" 
                                            data-bs-toggle="modal" data-bs-target="#viewPosModal{{ $pos->id }}" title="ดูพนักงานที่ครองตำแหน่งนี้">
                                            <i class="bi bi-eye"></i> ดูข้อมูล
                                        </button>

                                        @if(Auth::user()->isAdmin())
                                            <!-- แก้ไข (Edit) - เฉพาะ Admin -->
                                            <button type="button" class="btn btn-outline-warning btn-sm rounded-3 py-1 px-2" 
                                                data-bs-toggle="modal" data-bs-target="#editPosModal{{ $pos->id }}" title="แก้ไขชื่อ/สถานะ">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- ลบ (Delete) - เฉพาะ Admin -->
                                            @if($pos->users_count === 0)
                                                <form action="{{ route('departments.positions.destroy', $pos, false) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('ยืนยันลบตำแหน่ง {{ $pos->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 py-1 px-2" title="ลบตำแหน่ง">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 py-1 px-2 disabled opacity-50" 
                                                    title="ไม่สามารถลบได้เนื่องจากมีพนักงานดำรงตำแหน่งนี้อยู่">
                                                    <i class="bi bi-lock-fill"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">ยังไม่มีข้อมูลตำแหน่งงาน</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section -->
@foreach($positions as $pos)
    <!-- Modal: ดูสมาชิกในตำแหน่ง (View Members) -->
    <div class="modal fade" id="viewPosModal{{ $pos->id }}" tabindex="-1" aria-labelledby="viewPosModalLabel{{ $pos->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content form-card border-0 shadow-lg">
                <div class="modal-header border-bottom border-theme pb-3">
                    <h5 class="modal-title fw-bold text-theme d-flex align-items-center gap-2" id="viewPosModalLabel{{ $pos->id }}">
                        <i class="bi bi-award-fill text-warning"></i>
                        <span>พนักงานในตำแหน่ง: {{ $pos->name }}</span>
                        <span class="badge bg-success-subtle text-success fs-6">{{ $pos->users_count }} คน</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($pos->users->count() > 0)
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>รหัส / ชื่อพนักงาน</th>
                                        <th>อีเมล</th>
                                        <th>แผนกสังกัด</th>
                                        <th class="text-center">บทบาท</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pos->users as $u)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-circle-sm bg-warning-subtle text-warning fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%;">
                                                        {{ mb_substr($u->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-theme">{{ $u->name }}</div>
                                                        <small class="text-muted">{{ $u->employee_id ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small text-muted">{{ $u->email }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $u->department->name ?? 'ไม่ระบุแผนก' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light-subtle border border-secondary-subtle text-uppercase text-secondary" style="font-size: 0.72rem;">
                                                    {{ $u->role }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 opacity-50 d-block mb-2"></i>
                            <span>ยังไม่มีพนักงานดำรงตำแหน่งนี้</span>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top border-theme pt-2">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: แก้ไขตำแหน่ง (Edit Position) -->
    <div class="modal fade" id="editPosModal{{ $pos->id }}" tabindex="-1" aria-labelledby="editPosModalLabel{{ $pos->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content form-card border-0 shadow-lg">
                <div class="modal-header border-bottom border-theme pb-3">
                    <h5 class="modal-title fw-bold text-theme d-flex align-items-center gap-2" id="editPosModalLabel{{ $pos->id }}">
                        <i class="bi bi-pencil-square text-warning"></i>
                        <span>แก้ไขตำแหน่งงาน: {{ $pos->name }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('departments.positions.update', $pos, false) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-theme">ชื่อตำแหน่งงาน <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom" 
                                value="{{ old('name', $pos->name) }}" required>
                        </div>
                        <div class="mb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active_pos_{{ $pos->id }}" 
                                    name="is_active" value="1" {{ ($pos->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-theme small" for="is_active_pos_{{ $pos->id }}">
                                    เปิดใช้งานตำแหน่งนี้ (Active)
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">หากปิดการใช้งาน ตำแหน่งนี้จะไม่แสดงในตัวเลือกเพิ่ม/ย้ายพนักงานใหม่</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-theme pt-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
