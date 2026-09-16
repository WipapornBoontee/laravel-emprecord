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

    <!-- Add Position Form -->
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

    <!-- Positions List Table -->
    <div class="col-lg-8">
        <div class="dept-card overflow-hidden">
            <div class="p-4 border-bottom border-theme d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-theme">รายชื่อตำแหน่งงานทั้งหมด ({{ $positions->count() }} ตำแหน่ง)</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>ชื่อตำแหน่ง</th>
                            <th style="width: 150px;">จำนวนพนักงาน</th>
                            <th class="text-end" style="width: 100px;">จัดการ</th>
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
                                    <span class="badge bg-success-subtle text-success px-2 py-1">
                                        <i class="bi bi-people me-1"></i>{{ $pos->users_count }} คน
                                    </span>
                                </td>
                                <td class="text-end">
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
                                        <span class="badge bg-secondary-subtle text-muted" title="ไม่สามารถลบได้เนื่องจากมีพนักงานดำรงตำแหน่งนี้อยู่">
                                            <i class="bi bi-lock-fill"></i> ไม่ว่าง
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">ยังไม่มีข้อมูลตำแหน่งงาน</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
