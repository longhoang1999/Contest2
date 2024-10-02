@extends('layouts/default')
@section('title')
    Đăng ký tài khoản sinh viên
@parent
@stop

@section('header_styles')
    <style>
        div.dt-container .dt-paging .dt-paging-button:hover{
            border:  none !important;
            background: none !important;
        }
    </style>
@stop

@section('title_page')
    Đăng ký tài khoản sinh viên
@stop

@php
    use Cartalyst\Sentinel\Native\Facades\Sentinel;
    use Illuminate\Support\Facades\DB;
@endphp
@section('content')
    <div class="d-flex" id="main-wedsite">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                @if ($errors->any())
                    <div class="col-xl-12 mb-10 alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
            <form action="{{ route('admin.quanlysinhvien.registerPostUser') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row mb-10">
                    <div class="col-xl-6">
                        <div class="mt-5 mb-2">
                            <label for="">Mã sinh viên</label>
                            <span class="text-danger">*</span>
                            <input type="text" placeholder="Mã sinh viên" class="form-control" name="student_code" value="{{ old('student_code') }}">
                        </div>
                        <div class="mt-5 mb-2">
                            <label for="">Tên sinh viên</label>
                            <span class="text-danger">*</span>
                            <input type="text" placeholder="Tên sinh viên" class="form-control" name="student_name" value="{{ old('student_name') }}">
                        </div>
                        <div class="mt-5 mb-2">
                            <label for="">Tên đăng nhập</label>
                            <span class="text-danger">*</span>
                            <input type="text" placeholder="Tên đăng nhập" class="form-control" name="student_username" value="{{ old('student_username') }}">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <label for="" >Ảnh đại diện</label> <br>
                        <img class="mt-3 mb-3" id="imagePreview" alt="Image preview" style="max-width: 150px;" src="{{ asset('userinfo_backup\default\male.jpg') }}"/>
                        <input type="file" id="imageInput" accept="image/*" class="form-control" name="file">
                    </div>
                </div>
                <div class="row mb-10">
                    <div class="col-xl-6">
                        <label for="">Email sinh viên</label>
                        <input type="text" placeholder="Email sinh viên" class="form-control" name="student_email" value="{{ old('student_email') }}">
                    </div>
                    <div class="col-xl-6">
                        <label for="">Password</label>
                        <small class="text-info">(Mặc định là tên đăng nhập)</small>
                        <input type="password" placeholder="Password" class="form-control" name="student_password" value="{{ old('student_password') }}" >
                    </div>
                </div>
                <div class="row mb-10">
                    <div class="col-xl-6">
                        <label for="">Giới tính</label>
                        <select name="student_gender" id="" class="form-control" value="{{ old('student_gender') }}">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div class="col-xl-6">
                        <label for="">Số điện thoại</label>
                        <input type="text" placeholder="Số điện thoại" class="form-control" name="student_phone" value="{{ old('student_phone') }}">
                    </div>
                </div>
                <div class="row mb-10">
                    <div class="col-xl-3">
                        <button class="btn btn-primary">Đăng ký tài khoản</button>
                    </div>
                </div>
            </form>
        </div>
    </div>




@stop

@section('footer_scripts')
    <script src="{{ asset('chart.js') }}"></script>
    <script>
        var dd = document.querySelector(".quanlysinhvien")
        dd.classList.add("show")
        $(".dashboard-navbar").addClass("here")
        $(".dashboard-navbar").addClass("show")

        $(".dangkysinhvien").addClass("active")
    </script>


    <script>
        // Lấy input và img từ DOM
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');

        // Lắng nghe sự kiện thay đổi khi người dùng chọn ảnh
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0]; // Lấy file đầu tiên
            if (file) {
                // Tạo URL tạm thời cho file
                const imageURL = URL.createObjectURL(file);
                // Cập nhật src của thẻ img và hiển thị nó
                imagePreview.src = imageURL;
                imagePreview.style.display = 'block';
            } else {
                // Ẩn thẻ img nếu không có ảnh nào được chọn
                imagePreview.style.display = 'none';
            }
        });
    </script>
@stop
