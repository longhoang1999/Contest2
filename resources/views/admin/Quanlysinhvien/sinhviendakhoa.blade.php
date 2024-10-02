@extends('layouts/default')
@section('title')
    Danh sách tài khoản đã khóa
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
    Danh sách tài khoản đã khóa
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
                <div class="col-xl-3 mt-3 mb-3 d-flex">
                    <input type="text" placeholder="Tìm kiếm sinh viên" class="form-control" id="input-search">

                    <button class="btn btn-success ms-3" id="btn-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                <div class="col-xl-12 mb-10">

                   {{-- Content --}}
                   <table class="table table-bordered table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên</th>
                                <th>Hình ảnh</th>
                                <th>Mã sinh viên</th>
                                <th>Email</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                   </table>
                </div>
            </div>

        </div>
    </div>


    {{-- Modal detail --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalDetailLabel">Thông tin chi tiết</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-3">
                                <img src="" alt="" class="image_users w-100" >
                            </div>
                            <div class="col-9">
                                <p>
                                    <span class="fw-bold">Họ và tên: </span>
                                    <span class="name_users"></span>
                                </p>
                                <p>
                                    <span class="fw-bold">Email: </span>
                                    <span class="email_users"></span>
                                </p>
                                <p>
                                    <span class="fw-bold">Giới tính: </span>
                                    <span class="gender_users"></span>
                                </p>
                                <p>
                                    <span class="fw-bold">Số điện thoại: </span>
                                    <span class="phone_users"></span>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <form action="{{ route('admin.quanlysinhvien.restoreStudent') }}" method="post">
                    @csrf
                    <input type="hidden" value="" id="input_restore" name="id_restore">
                    <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
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

        $(".taikhoandakhoa").addClass("active")
    </script>


    <script>
        let table = new DataTable('#myTable', {
            "language": {
                "emptyTable": "Bảng không có dữ liệu"
            },
            "order": [[ 1, 'asc' ]],
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.quanlysinhvien.listLockStudent') !!}',
            order:[],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                { data: 'name', name: 'name' },
                { data: 'user_image', name: 'user_image' },
                { data: 'ten_dangnhap', name: 'ten_dangnhap' },
                { data: 'email', name: 'email' },
                { data: 'action', name: 'action' }
            ]
        });




        // modal detail opened
        var modalDetail = document.getElementById('modalDetail')
        modalDetail.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var recipient = button.getAttribute('data-bs-id')

            let loadData = "{{ route('admin.quanlysinhvien.detailStudent') }}?id=" + recipient;

            fetch(loadData, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'GET',
                // body: JSON.stringify(dataSubmit)
            })
                .then((response) => response.json())
                .then((data) => {

                    $(".image_users").attr('src', '{{ asset("/") }}' + data.image)
                    $(".name_users").text(data.name)
                    $(".gender_users").text(data.gender)
                    $(".phone_users").text(data.phone)
                    $(".email_users").text(data.email)
                    $("#input_restore").val(recipient)
                })
        })



        $("#btn-search").click(function() {
            let seach = $("#input-search").val()
            table.search(seach).draw();
        })
    </script>
@stop
