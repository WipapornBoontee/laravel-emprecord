@extends('layout')

@section('title', 'เขียนบทความใหม่')

@section('content')
    <h2 class="text text-center py-2">เขียนบทความใหม่</h2>
    <form method="post" action="/insert">
        @csrf
            <div class="form-group">
                <label for="title">ชื่อบทความ</label>
                <input type="text" name="title" class="form-control">
            </div>
            @error('title')
                <p class="text-danger">{{ $message }}</p>
            @enderror

            <div class="form-group">
                <label for="title">เนื้อหา</label>
                <textarea name="content" class="form-control" cols="30" rows="5"></textarea>
            </div>

            @error('content')
                <p class="text-danger">{{ $message }}</p>
            @enderror

            <input type="submit" value="บันทึกข้อมูล" class="btn btn-primary my-3">
            <a href="/blogs" class="btn btn-secondary ">บทความทั้งหมด</a>
        </form>

    @endsection
