<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    function blogs()
    {
        $blogs = [
            [
                'title' => 'บทความที่ 1',
                'content' => 'เนื้อหาบทความที่ 1',
                'status' => true,
            ],
            [
                'title' => 'บทความที่ 2',
                'content' => 'เนื้อหาบทความที่ 2',
                'status' => true,
            ],
            [
                'title' => 'บทความที่ 3',
                'content' => 'เนื้อหาบทความที่ 3',
                'status' => false,
            ],
            [
                'title' => 'บทความที่ 4',
                'content' => 'เนื้อหาบทความที่ 4',
                'status' => true,
            ],
            [
                'title' => 'บทความที่ 5',
                'content' => 'เนื้อหาบทความที่ 5',
                'status' => false,
            ],
        ];

        return view('blogs', compact('blogs'));
    }

    function abouts()
    {
        $name = "Wipaporn";
        $date = "6 กรกฎาคม 2026";
        return view("abouts",compact('name','date'));
    }   

    function form(){    
        return view("form");
    }

    function insert(Request $request) {
        $request->validate([
            'title' => 'required|max:30',
            'content' => 'required',
        ],
        ['title.required' => 'กรุณากรอกชื่อบทความ'],
        ['title.max' => 'ชื่อบทความต้องไม่เกิน 30 ตัวอักษร'],
        ['content.required' => 'กรุณากรอกเนื้อหาบทความ']
    );
    }
}
