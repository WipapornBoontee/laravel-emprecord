@extends('layouts.app')

@section('title', 'ข้อมูลฐานเงินเดือน')

@push('styles')
<table>
    <tr>
        <td>รหัสพนักงาน</td>
        <td>{{ Auth::user()->emp_code ?? '-' }}</td>
    </tr>
    <tr>
        <td>ชื่อพนักงาน</td>
        <td>{{ Auth::user()->name }}</td>
    </tr>
    <tr>
        <td>วันที่</td>
        <td>{{ $date }}</td>
    </tr>
    <tr>
        <td>จำนวนชั่วโมง</td>
        <td>{{ $overtimeHours }}</td>
    </tr>
</table>
@endsection
