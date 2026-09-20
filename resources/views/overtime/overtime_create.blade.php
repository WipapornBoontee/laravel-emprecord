@extends('layouts.app')

@section('title', 'ยื่นคำขอทำงานล่วงเวลา (OT)')

@push('styles')
<style>
    .ot-form-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 0.65rem 1rem;
    }
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
    }
    .calculation-box {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.05) 100%);
        border: 1px dashed rgba(99, 102, 241, 0.3);
        border-radius: 16px;
        padding: 1.25rem;
    }
    .attendance-badge-box {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
    }
    .quick-hour-btn {
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        transition: all 0.2s ease;
        border: 1px solid var(--surface-border);
        background: var(--badge-bg);
        color: var(--text-main);
    }
    .quick-hour-btn:hover {
        background: var(--primary-gradient);
        color: #ffffff;
        border-color: transparent;
    }
    .quick-hour-btn.active {
        background: var(--primary-gradient);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 10px var(--accent-glow);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="ot-form-card p-4 p-md-5">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between border-bottom border-theme pb-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="action-icon icon-amber" style="width: 44px; height: 44px;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-theme">ยื่นคำขอทำงานล่วงเวลา (OT)</h4>
                            <small class="text-muted">กรอกรายละเอียดช่วงเวลาทำงานล่วงเวลา ระบบจะคำนวณและสรุปชั่วโมงสุทธิให้อัตโนมัติ</small>
                        </div>
                    </div>
                    <a href="{{ route('overtime.show') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 border-0 small mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger rounded-3 border-0 small mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('overtime.request') }}" method="POST" id="otRequestForm">
                    @csrf
                    
                    <!-- 1. วันที่ปฏิบัติงาน OT -->
                    <div class="mb-4">
                        <label for="ot_date" class="form-label fw-bold text-theme small d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-calendar-event text-primary me-1"></i> วันที่ปฏิบัติงาน OT <span class="text-danger">*</span></span>
                            <span class="text-muted fw-normal" style="font-size: 0.8rem;">(ยื่นย้อนหลังได้ 7 วัน / ล่วงหน้า 7 วัน)</span>
                        </label>
                        <input type="date" name="date" id="ot_date" class="form-control form-control-custom" 
                            min="{{ $minDateStr }}" max="{{ $maxDateStr }}" 
                            value="{{ old('date', $selectedDate) }}" required>
                        
                        <!-- Attendance Reference Box -->
                        <div class="attendance-badge-box mt-2" id="attendanceInfoBox">
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">
                                    <i class="bi bi-fingerprint me-1"></i> บันทึกเวลาเข้า-ออกงานในวันนี้:
                                </span>
                                <span id="attendanceStatusText" class="fw-semibold text-theme">กำลังตรวจสอบ...</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. ประเภทการทำ OT -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-theme small">
                            <i class="bi bi-tag text-primary me-1"></i> ประเภทค่าล่วงเวลา (OT Type) <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check p-3 border border-theme rounded-3 h-100 cursor-pointer" style="background: var(--badge-bg);">
                                    <input class="form-check-input" type="radio" name="ot_type" id="ot_type_normal" value="normal" {{ old('ot_type', 'normal') === 'normal' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100 cursor-pointer" for="ot_type_normal">
                                        <div class="fw-bold text-theme small">วันทำงานปกติ</div>
                                        <div class="text-primary small fw-semibold">อัตรา 1.5 เท่า</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">ทำต่อหลัง 17:00 น.</div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check p-3 border border-theme rounded-3 h-100 cursor-pointer" style="background: var(--badge-bg);">
                                    <input class="form-check-input" type="radio" name="ot_type" id="ot_type_holiday" value="holiday" {{ old('ot_type') === 'holiday' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100 cursor-pointer" for="ot_type_holiday">
                                        <div class="fw-bold text-theme small">วันหยุด / นักขัตฤกษ์</div>
                                        <div class="text-success small fw-semibold">อัตรา 2.0 เท่า</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">ทำงานในวันหยุด</div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check p-3 border border-theme rounded-3 h-100 cursor-pointer" style="background: var(--badge-bg);">
                                    <input class="form-check-input" type="radio" name="ot_type" id="ot_type_holiday_ot" value="holiday_ot" {{ old('ot_type') === 'holiday_ot' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100 cursor-pointer" for="ot_type_holiday_ot">
                                        <div class="fw-bold text-theme small">วันหยุดล่วงเวลา</div>
                                        <div class="text-danger small fw-semibold">อัตรา 3.0 เท่า</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">ทำเกินเวลาในวันหยุด</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. ช่วงเวลาทำงาน & ปุ่มลัดคำนวณชั่วโมงด่วน (Quick Hour Presets) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-theme small mb-0">
                                <i class="bi bi-clock me-1 text-primary"></i> ช่วงเวลาทำงาน & การคำนวณชั่วโมง
                            </label>
                            <span class="text-muted small">เลือกเวลาเริ่มต้นและเลิกงาน หรือกดปุ่มชั่วโมงด่วน</span>
                        </div>

                        <!-- Quick Presets -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="small text-muted align-self-center me-1">เลือกชั่วโมงด่วน:</span>
                            <button type="button" class="btn quick-hour-btn" onclick="setQuickHours(1)">1.0 ชม.</button>
                            <button type="button" class="btn quick-hour-btn" onclick="setQuickHours(1.5)">1.5 ชม.</button>
                            <button type="button" class="btn quick-hour-btn" onclick="setQuickHours(2)">2.0 ชม.</button>
                            <button type="button" class="btn quick-hour-btn" onclick="setQuickHours(2.5)">2.5 ชม.</button>
                            <button type="button" class="btn quick-hour-btn active" onclick="setQuickHours(3)">3.0 ชม.</button>
                            <button type="button" class="btn quick-hour-btn" onclick="setQuickHours(4)">4.0 ชม.</button>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label for="start_time" class="form-label fw-semibold text-theme small">
                                    เวลาเริ่มต้น <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="start_time" id="start_time" class="form-control form-control-custom" 
                                    value="{{ old('start_time', '17:30') }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label for="end_time" class="form-label fw-semibold text-theme small">
                                    เวลาสิ้นสุด <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="end_time" id="end_time" class="form-control form-control-custom" 
                                    value="{{ old('end_time', '20:30') }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label for="break_minutes" class="form-label fw-semibold text-theme small">
                                    เวลาพัก (หักออก)
                                </label>
                                <select name="break_minutes" id="break_minutes" class="form-select form-control-custom">
                                    <option value="0" {{ old('break_minutes', '0') == '0' ? 'selected' : '' }}>ไม่มีเวลาพัก</option>
                                    <option value="30" {{ old('break_minutes') == '30' ? 'selected' : '' }}>พัก 30 นาที</option>
                                    <option value="60" {{ old('break_minutes') == '60' ? 'selected' : '' }}>พัก 1 ชั่วโมง</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. สรุปชั่วโมงคำนวณอัตโนมัติ (Sum & Overtime Cap Breakdown) -->
                    <div class="calculation-box mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-2 border-bottom border-theme">
                            <div>
                                <span class="text-muted small d-block mb-1">รวมชั่วโมงทำงานล่วงเวลาสุทธิ (Auto Sum):</span>
                                <h3 class="fw-bold text-primary mb-0" id="calculatedHoursDisplay">3.0 ชั่วโมง</h3>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-bold" id="calculatedTimeRange">
                                    17:30 - 20:30 น.
                                </span>
                                <div class="text-muted small mt-1" id="calculationFormulaText">คำนวณจาก 3 ชม. 0 นาที - พัก 0 นาที</div>
                            </div>
                        </div>

                        <!-- Weekly Cap Indicator -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="text-muted">
                                    <i class="bi bi-speedometer2 me-1"></i> OT สะสมสัปดาห์นี้: 
                                    <strong id="weeklyApprovedText">{{ number_format($weeklyApprovedHours ?? 0, 1) }}</strong> / 36.0 ชม.
                                </span>
                                <span class="fw-bold" id="weeklyTotalText">
                                    รวมคำขอนี้: {{ number_format(($weeklyApprovedHours ?? 0) + 3.0, 1) }} ชม.
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                @php
                                    $currentWeekly = (float) ($weeklyApprovedHours ?? 0);
                                    $initialPercent = min(100, (($currentWeekly + 3.0) / 36.0) * 100);
                                @endphp
                                <div class="progress-bar" id="weeklyProgressBar" role="progressbar" style="width: {{ $initialPercent }}%;"></div>
                            </div>
                            <small class="text-muted d-block mt-1" id="capWarningText" style="font-size: 0.75rem;">
                                กฎหมายแรงงานกำหนดชั่วโมง OT รวมไม่เกิน 36 ชม./สัปดาห์
                            </small>
                        </div>
                    </div>

                    <!-- 5. รายละเอียดงานที่ปฏิบัติ -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold text-theme small">
                            <i class="bi bi-pencil-square text-primary me-1"></i> รายละเอียดงานที่ทำล่วงเวลา <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-custom" id="description" name="description" rows="3" 
                            required placeholder="ระบุชื่องาน โครงการ หรือเหตุผลความจำเป็นในการทำ OT...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold flex-grow-1" id="submitBtn" style="background: var(--primary-gradient); border: none;">
                            <i class="bi bi-send-fill me-2"></i> ส่งคำขอทำงานล่วงเวลา
                        </button>
                        <a href="{{ route('overtime.show') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ข้อมูลการลงเวลาย้อนหลังของพนักงานส่งมาจาก Backend
    const attendancesData = @json($recentAttendances);
    const baseWeeklyHours = {{ (float) ($weeklyApprovedHours ?? 0) }};

    function updateAttendanceInfo() {
        const dateInput = document.getElementById('ot_date');
        const statusText = document.getElementById('attendanceStatusText');
        const selected = dateInput.value;

        if (attendancesData[selected]) {
            const att = attendancesData[selected];
            const checkIn = att.check_in ? att.check_in.substring(0, 5) + ' น.' : 'ไม่ได้ลงเวลา';
            const checkOut = att.check_out ? att.check_out.substring(0, 5) + ' น.' : 'ยังไม่ลงเวลา';
            statusText.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>เข้างาน: ${checkIn}</span> | <span class="text-warning"><i class="bi bi-box-arrow-right me-1"></i>ออกงาน: ${checkOut}</span>`;
        } else {
            statusText.innerHTML = `<span class="text-muted"><i class="bi bi-info-circle me-1"></i>ไม่มีบันทึกเวลาเข้า-ออกงานในระบบ</span>`;
        }
    }

    function calculateOtHours() {
        const startTimeInput = document.getElementById('start_time').value;
        const endTimeInput = document.getElementById('end_time').value;
        const breakMinutes = parseInt(document.getElementById('break_minutes').value || '0', 10);
        const hoursDisplay = document.getElementById('calculatedHoursDisplay');
        const rangeDisplay = document.getElementById('calculatedTimeRange');
        const formulaText = document.getElementById('calculationFormulaText');
        const weeklyTotalText = document.getElementById('weeklyTotalText');
        const progressBar = document.getElementById('weeklyProgressBar');
        const capWarningText = document.getElementById('capWarningText');
        const submitBtn = document.getElementById('submitBtn');

        if (!startTimeInput || !endTimeInput) {
            hoursDisplay.innerText = '0.0 ชั่วโมง';
            return;
        }

        const [startH, startM] = startTimeInput.split(':').map(Number);
        const [endH, endM] = endTimeInput.split(':').map(Number);

        let startMinutes = startH * 60 + startM;
        let endMinutes = endH * 60 + endM;

        // ข้ามวัน
        if (endMinutes <= startMinutes) {
            endMinutes += 24 * 60;
        }

        let totalMinutes = endMinutes - startMinutes;
        let grossH = Math.floor(totalMinutes / 60);
        let grossM = totalMinutes % 60;

        let netMinutes = Math.max(0, totalMinutes - breakMinutes);
        let calculatedHours = (netMinutes / 60).toFixed(1);

        hoursDisplay.innerText = `${calculatedHours} ชั่วโมง`;
        rangeDisplay.innerText = `${startTimeInput} - ${endTimeInput} น.`;
        formulaText.innerText = `คำนวณจาก ${grossH} ชม. ${grossM} นาที - หักพัก ${breakMinutes} นาที`;

        // Update Weekly Cap
        let numHours = parseFloat(calculatedHours) || 0;
        let totalWeekHours = baseWeeklyHours + numHours;
        weeklyTotalText.innerText = `รวมคำขอนี้: ${totalWeekHours.toFixed(1)} ชม.`;

        let percent = Math.min(100, (totalWeekHours / 36.0) * 100);
        progressBar.style.width = `${percent}%`;

        if (totalWeekHours > 36.0) {
            progressBar.className = 'progress-bar bg-danger';
            capWarningText.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> เกินขีดจำกัดกฎหมาย 36 ชม./สัปดาห์ (ปัจจุบันรวมได้ ${totalWeekHours.toFixed(1)} ชม.)</span>`;
        } else if (totalWeekHours >= 30.0) {
            progressBar.className = 'progress-bar bg-warning';
            capWarningText.innerHTML = `<span class="text-warning fw-semibold"><i class="bi bi-info-circle me-1"></i> ใกล้ถึงขีดจำกัด 36 ชม./สัปดาห์</span>`;
        } else {
            progressBar.className = 'progress-bar bg-primary';
            capWarningText.innerText = 'กฎหมายแรงงานกำหนดชั่วโมง OT รวมไม่เกิน 36 ชม./สัปดาห์';
        }
    }

    function setQuickHours(desiredHours) {
        // Highlight active button
        document.querySelectorAll('.quick-hour-btn').forEach(btn => {
            if (parseFloat(btn.innerText) === desiredHours) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        const startTimeInput = document.getElementById('start_time').value || '17:30';
        const breakMinutes = parseInt(document.getElementById('break_minutes').value || '0', 10);
        const [startH, startM] = startTimeInput.split(':').map(Number);

        let totalTargetMinutes = Math.round(desiredHours * 60) + breakMinutes;
        let startMinutes = startH * 60 + startM;
        let endMinutes = (startMinutes + totalTargetMinutes) % (24 * 60);

        let endH = Math.floor(endMinutes / 60);
        let endM = endMinutes % 60;

        let endHStr = endH.toString().padStart(2, '0');
        let endMStr = endM.toString().padStart(2, '0');

        document.getElementById('end_time').value = `${endHStr}:${endMStr}`;
        calculateOtHours();
    }

    document.getElementById('ot_date').addEventListener('change', updateAttendanceInfo);
    document.getElementById('start_time').addEventListener('input', calculateOtHours);
    document.getElementById('end_time').addEventListener('input', calculateOtHours);
    document.getElementById('break_minutes').addEventListener('change', calculateOtHours);

    // Run on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateAttendanceInfo();
        calculateOtHours();
    });
</script>
@endpush

@endsection
