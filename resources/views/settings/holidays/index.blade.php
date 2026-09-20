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
                        <i class="bi bi-check2-square me-1 text-success"></i> พิจารณาอนุมัติ
                    </a>
                </div>
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
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-calendar-week me-1 text-primary"></i> รายการวันหยุดประจำปี {{ $year }} ({{ $holidays->count() }} วัน)
                </div>
                <form method="GET" action="{{ route('settings.holidays.index', [], false) }}" class="d-flex align-items-center gap-2 m-0">
                    <select name="year" class="form-select form-select-sm filter-input py-1" style="width: 120px;" onchange="this.form.submit()">
                        @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>ปี {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>ชื่อวันหยุด</th>
                            <th>วันที่</th>
                            <th>ลักษณะวันหยุด</th>
                            <th class="text-end" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $index => $holiday)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-theme">
                                    <i class="bi bi-calendar-event text-danger me-2"></i>{{ $holiday->name }}
                                </td>
                                <td>
                                    <span class="fw-semibold text-theme">
                                        {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d/m/Y') }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">
                                        {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('l') }}
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
                                    <form action="{{ route('settings.holidays.destroy', $holiday, false) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('คุณต้องการลบวันหยุด {{ $holiday->name }} ใช่หรือไม่?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 py-1 px-2" title="ลบวันหยุด">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                                    ยังไม่มีรายการวันหยุดสำหรับปี {{ $year }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
