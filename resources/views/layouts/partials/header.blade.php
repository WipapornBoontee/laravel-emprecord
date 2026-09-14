<!-- Header / Navigation Bar -->
<nav class="navbar navbar-expand-lg custom-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand navbar-brand-custom d-flex align-items-center gap-2" href="{{ route('dashboard', [], false) }}">
            <div class="brand-icon d-flex align-items-center justify-content-center">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <span>WB-EMS</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="bi bi-list fs-2 text-theme"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- เมนูหลักตามสิทธิ์การใช้งาน -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                @auth
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard', [], false) }}">
                            <i class="bi bi-grid-1x2-fill me-1"></i> หน้าหลัก
                        </a>
                    </li>

                    {{-- เมนูสำหรับ Admin และ HR เท่านั้น --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isHr())
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-custom dropdown-toggle {{ Request::is('employees*') || Request::is('departments*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-people-fill me-1"></i> จัดการพนักงาน
                            </a>
                            <ul class="dropdown-menu custom-dropdown-menu border-0 shadow-lg">
                                <li><a class="dropdown-item py-2" href="{{ route('employees.index', [], false) }}"><i class="bi bi-list-ul me-2 text-primary"></i> รายชื่อพนักงานทั้งหมด</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('employees.create', [], false) }}"><i class="bi bi-person-plus-fill me-2 text-success"></i> เพิ่มพนักงานใหม่</a></li>
                                <li><hr class="dropdown-divider opacity-25"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('departments.index', [], false) }}"><i class="bi bi-diagram-3-fill me-2 text-warning"></i> จัดการแผนกและตำแหน่ง</a></li>
                            </ul>
                        </li>
                    @endif

                    <!-- เมนูระบบการลา -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle {{ Request::is('leaves*') || Request::is('apply-leave*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-calendar2-check-fill me-1"></i> การลา
                        </a>
                        <ul class="dropdown-menu custom-dropdown-menu border-0 shadow-lg">
                            <li><a class="dropdown-item py-2" href="{{ route('leaves.create', [], false) }}"><i class="bi bi-file-earmark-plus me-2 text-info"></i> ยื่นใบลา</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('leaves.index', [], false) }}"><i class="bi bi-clock-history me-2 text-primary"></i> ประวัติการลาของฉัน</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('leaves.balances', [], false) }}"><i class="bi bi-pie-chart-fill me-2 text-success"></i> สิทธิ์วันลาคงเหลือ</a></li>
                            @if(Auth::user()->isAdmin() || Auth::user()->isHr())
                                <li><hr class="dropdown-divider opacity-25"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('leaves.approvals', [], false) }}"><i class="bi bi-check2-square me-2 text-warning"></i> อนุมัติคำขอลา (HR/Admin)</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('leaves.types.index', [], false) }}"><i class="bi bi-gear-fill me-2 text-secondary"></i> จัดการประเภทวันลา</a></li>
                            @endif
                        </ul>
                    </li>

                    <!-- เมนูระบบลงเวลา -->
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#">
                            <i class="bi bi-stopwatch-fill me-1"></i> บันทึกเวลา
                        </a>
                    </li>
                @endauth
            </ul>

            <!-- ส่วนข้อมูลผู้ใช้, สลับโหมด Light/Dark & ออกจากระบบ -->
            <div class="d-flex align-items-center gap-2 gap-lg-3 mt-3 mt-lg-0">
                <!-- Theme Toggle Button -->
                <button type="button" class="btn btn-theme-toggle" id="themeToggleBtn" title="สลับโหมดสว่าง/มืด" aria-label="Toggle Theme">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                </button>

                @auth
                    <a href="{{ route('profile', [], false) }}" class="user-profile-badge d-flex align-items-center gap-2 p-1 pe-3 rounded-pill text-decoration-none" title="ดูโปรไฟล์ส่วนตัว">
                        <div class="avatar-circle">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="d-flex flex-column text-start">
                            <span class="fw-semibold user-name-text small leading-none">{{ Auth::user()->name }}</span>
                            <div class="d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                <span class="text-muted">{{ Auth::user()->emp_code ?? '-' }}</span>
                                <span class="text-muted opacity-50">•</span>
                                @if(Auth::user()->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1 py-0">Admin</span>
                                @elseif(Auth::user()->role === 'hr')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-1 py-0">HR</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1 py-0">Employee</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('logout', [], false) }}" class="btn btn-outline-danger btn-sm px-3 rounded-3 d-flex align-items-center gap-1"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">ออกจากระบบ</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout', [], false) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login', [], false) }}" class="btn btn-primary btn-sm px-4 rounded-3 d-flex align-items-center gap-2"
                        style="background: var(--primary-gradient); border: none; font-weight: 600;">
                        <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
