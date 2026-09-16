@extends('layouts.app')

@section('title', 'ข้อมูลเงินเดือน')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i> ข้อมูลเงินเดือน (อยู่ระหว่างการพัฒนา)</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4 mt-3">
                        <i class="bi bi-person-circle text-secondary" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">พนักงาน: {{ Auth::user()->name }}</h4>
                        <p class="text-muted">รหัสพนักงาน: {{ Auth::user()->emp_code ?? '-' }}</p>
                    </div>
                    
                    <hr>

                    <div class="row text-center mt-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">ฐานเงินเดือน</p>
                                <h5 class="mb-0 text-primary">รอการอัปเดต</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">ค่าล่วงเวลา (OT)</p>
                                <h5 class="mb-0 text-success">รอการอัปเดต</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">สลิปเงินเดือน</p>
                                <h5 class="mb-0 text-info">รอการอัปเดต</h5>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4 text-center">
                        <i class="bi bi-tools me-2"></i> ระบบแสดงผลเงินเดือนเต็มรูปแบบจะเปิดใช้งานในเร็วๆ นี้
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
