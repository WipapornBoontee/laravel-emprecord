@extends('layouts.app')

@section('title', 'เขียนบทความ')

@section('content')
    <h2 class="my-4">เขียนบทความใหม่</h2>
    <hr>

    <div class="card p-4 shadow-sm bg-dark text-light border-secondary">
        <form action="#" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">ชื่อบทความ</label>
                <input type="text" class="form-style form-control text-light border-0" id="title"
                    placeholder="กรอกชื่อบทความ">
            </div>
           
            <div class="mb-3">
                <label for="content" class="form-label">เนื้อหาบทความ</label>
                <textarea class="form-control text-light border-0" id="content" rows="5" placeholder="พิมพ์เนื้อหาที่นี่..."></textarea>
            </div>
          
            <button type="submit" class="btn btn-primary">บันทึกบทความ</button>
        </form>
    </div>
@endsection
