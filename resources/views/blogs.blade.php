@extends('layouts.app')

@section('title', 'บทความ')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center gap-3 mb-5">
                <div class="p-3 rounded-circle d-flex align-items-center justify-content-center"
                    style="background: rgba(99, 102, 241, 0.15); width: 60px; height: 60px;">
                    <i class="bi bi-journal-text text-primary fs-3" style="color: #818cf8 !important;"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-white mb-0">บทความทั้งหมด</h2>
                    <p class="text-muted mb-0" style="color: #94a3b8 !important;">คลังสาระและเรื่องราวดีๆ ที่เราอยากแบ่งปัน
                    </p>
                    <hr>
                    <a href="{{ route('create') }}" class="btn btn-primary">เพิ่มบทความ</a>
                </div>
            </div>

            <div class="row g-4">
                @foreach ($blogs as $item)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm"
                            style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge px-3 py-2"
                                        style="border-radius: 30px; font-size: 0.8rem; 
                                    background: {{ $item['status'] ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }};
                                    color: {{ $item['status'] ? '#34d399' : '#f87171' }};
                                    border: 1px solid {{ $item['status'] ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }};">
                                        <i
                                            class="bi {{ $item['status'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                        {{ $item['status'] ? 'เผยแพร่แล้ว' : 'แบบร่าง' }}
                                    </span>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> 5 mins read</span>
                                </div>

                                <h4 class="fw-bold text-white mb-3">{{ $item['title'] }}</h4>
                                <p class="text-muted-custom flex-grow-1"
                                    style="color: #cbd5e1; line-height: 1.7; font-size: 0.95rem;">
                                    {{ $item['content'] }}
                                </p>

                                <hr style="border-color: rgba(255, 255, 255, 0.08);">
                                <div class="d-flex align-items-center justify-content-between mt-auto">
                                    <a href="#"
                                        class="btn btn-link p-0 text-decoration-none fw-semibold d-inline-flex align-items-center"
                                        style="color: #818cf8;">
                                        อ่านเพิ่มเติม <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
