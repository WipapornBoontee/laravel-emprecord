@extends('layouts.app')

@section('title', 'จัดการวันหยุดบริษัท & วันหยุดนักขัตฤกษ์')

@push('styles')
<style>
    .holiday-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
    }
    .form-control-custom, .filter-input {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-size: 0.92rem;
    }
    .form-control-custom:focus, .filter-input:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px var(--accent-glow);
        color: var(--text-main);
        outline: none;
    }
    .filter-input option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        color: white;
    }
    .modal-content-custom {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(20px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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
                    <div class="action-icon icon-amber">
                        <i class="bi bi-calendar-heart-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ปฏิทินวันหยุดนักขัตฤกษ์และวันหยุดบริษัท</h3>
                        <p class="text-muted mb-0 small">กำหนดวันหยุดทางการของบริษัท เพื่อใช้คำนวณวันลาจริงและหักวันลาโดยไม่นับวันหยุด</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('leaves.types.index', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-gear-fill me-1"></i> ตั้งค่าประเภทวันลา
                    </a>
                    <a href="{{ route('leaves.approvals', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-check2-square me-1 text-success"></i> พิจารณาอนุมัติคำขอลา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">วันหยุดประจำปี {{ $year }}</span>
                <h4 class="fw-bold mb-0 text-theme">{{ number_format($totalHolidaysCount) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-amber" style="width: 44px; height: 44px;">
                <i class="bi bi-calendar-event fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">วันหยุดประจำซ้ำทุกปี</span>
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($recurringCount) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-indigo" style="width: 44px; height: 44px;">
                <i class="bi bi-arrow-repeat fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">วันหยุดเฉพาะปี {{ $year }}</span>
                <h4 class="fw-bold mb-0 text-info">{{ number_format(max(0, $totalHolidaysCount - $recurringCount)) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-blue" style="width: 44px; height: 44px;">
                <i class="bi bi-calendar-check fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">กำลังแสดงในหน้านี้</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($holidays->count()) }} <span class="fs-6 text-muted fw-normal">รายการ</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-green" style="width: 44px; height: 44px;">
                <i class="bi bi-list-check fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 d-flex align-items-center gap-2 p-3 shadow-sm mb-0" 
                style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2) !important;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Form & Table Content -->
    <div class="col-lg-4">
        <div class="holiday-card p-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> เพิ่มวันหยุดใหม่
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

            <form action="{{ route('settings.holidays.store', [], false) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small text-theme">ชื่อวันหยุด <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control form-control-custom" 
                        placeholder="เช่น วันขึ้นปีใหม่, วันสงกรานต์" required value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label for="holiday_date" class="form-label fw-semibold small text-theme">วันที่หยุด <span class="text-danger">*</span></label>
                    <input type="date" name="holiday_date" id="holiday_date" class="form-control form-control-custom" 
                        required value="{{ old('holiday_date', date('Y-m-d')) }}">
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                    <label for="is_recurring" class="form-check-label small text-theme">
                        เป็นวันหยุดประจำซ้ำทุกปี (Recurring)
                    </label>
                </div>
                <button type="submit" class="btn btn-submit-custom w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i> บันทึกวันหยุด
                </button>
            </form>
        </div>
    </div>

    <!-- Holidays List Table -->
    <div class="col-lg-8">
        <div class="holiday-card overflow-hidden">
            <!-- Table Header Bar with Search & Pagination Controls -->
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-table me-1 text-primary"></i> รายการวันหยุดประจำปี {{ $year }} ({{ $holidays->total() }} รายการ)
                </div>

                <form method="GET" action="{{ route('settings.holidays.index', [], false) }}" class="d-flex flex-wrap align-items-center gap-2 m-0">
                    <div class="input-group input-group-sm" style="width: 170px;">
                        <span class="input-group-text bg-transparent border-theme text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control filter-input py-1" placeholder="ค้นหาวันหยุด..." value="{{ $search ?? '' }}">
                    </div>

                    <select name="year" class="form-select form-select-sm filter-input py-1" style="width: 110px;" onchange="this.form.submit()">
                        @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>ปี {{ $y }}</option>
                        @endfor
                    </select>

                    <div class="d-flex align-items-center gap-1 ms-1">
                        <label for="per_page_select" class="small text-muted mb-0 text-nowrap">แสดง:</label>
                        <select name="per_page" id="per_page_select" class="form-select form-select-sm filter-input py-1" style="width: 75px;" onchange="this.form.submit()">
                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    @if(!empty($search))
                        <a href="{{ route('settings.holidays.index', ['year' => $year, 'per_page' => $perPage], false) }}" class="btn btn-sm btn-outline-secondary" title="ล้างการค้นหา">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table Body -->
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>ชื่อวันหยุด</th>
                            <th>วันที่หยุด</th>
                            <th>ลักษณะวันหยุด</th>
                            <th class="text-end" style="width: 120px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $index => $holiday)
                            <tr>
                                <td class="text-muted">{{ $holidays->firstItem() + $index }}</td>
                                <td class="fw-bold text-theme">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.85rem; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <div>
                                            <span>{{ $holiday->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-theme">
                                        {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d/m/Y') }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">
                                        {{ \Carbon\Carbon::parse($holiday->holiday_date)->locale('th')->isoFormat('dddd') }}
                                    </span>
                                </td>
                                <td>
                                    @if($holiday->is_recurring)
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                            <i class="bi bi-arrow-repeat me-1"></i>ซ้ำทุกปี
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted px-2 py-1">
                                            เฉพาะปี {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" class="btn btn-outline-warning btn-sm rounded-3 py-1 px-2"
                                            data-bs-toggle="modal" data-bs-target="#editHolidayModal{{ $holiday->id }}" title="แก้ไข">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="{{ route('settings.holidays.destroy', $holiday, false) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('ยืนยันลบวันหยุด {{ $holiday->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 py-1 px-2" title="ลบ">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Holiday Modal -->
                                    <div class="modal fade text-start" id="editHolidayModal{{ $holiday->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content modal-content-custom p-3 border-0">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title fw-bold text-theme">
                                                        <i class="bi bi-pencil-square text-warning me-2"></i>แก้ไขข้อมูลวันหยุด
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('settings.holidays.update', $holiday, false) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold text-theme">ชื่อวันหยุด <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control form-control-custom" 
                                                                value="{{ $holiday->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold text-theme">วันที่หยุด <span class="text-danger">*</span></label>
                                                            <input type="date" name="holiday_date" class="form-control form-control-custom" 
                                                                value="{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" name="is_recurring" id="edit_recurring_{{ $holiday->id }}" 
                                                                class="form-check-input" value="1" {{ $holiday->is_recurring ? 'checked' : '' }}>
                                                            <label for="edit_recurring_{{ $holiday->id }}" class="form-check-label small text-theme">
                                                                เป็นวันหยุดประจำซ้ำทุกปี (Recurring)
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-secondary rounded-3 px-3" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <button type="submit" class="btn btn-submit-custom px-4">บันทึกการแก้ไข</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                                        <p class="mb-0 fw-semibold">ไม่พบรายการวันหยุด</p>
                                        <span class="small text-muted">ยังไม่มีวันหยุดสำหรับปี {{ $year }} หรือไม่ตรงกับคำค้นหา</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if($holidays->hasPages())
                <div class="p-3 px-4 border-top border-theme d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">
                        แสดง {{ $holidays->firstItem() ?? 0 }} ถึง {{ $holidays->lastItem() ?? 0 }} จาก {{ $holidays->total() }} วัน
                    </span>
                    <div>
                        {{ $holidays->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
