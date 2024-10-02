@extends('layouts/default')
@section('title')
    Danh sách sinh viên hệ thống
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
    Danh sách sinh viên hệ thống
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
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
        </div>
    </div>

    {{-- Model update --}}
    <div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalUpdateLabel">Chỉnh sửa chi tiết</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form action="{{ route('admin.quanlysinhvien.updateStudent') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-4">
                                    <img src="" alt="" class="image_users w-100" >
                                    <div class="mb-3 mt-3">
                                        <label for="formFile" class="form-label">Chọn tệp</label>
                                        <input class="form-control" type="file" id="formFile" name="image" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <input type="hidden" value="" class="id_users_input" name="id">
                                    <p>
                                        <span class="fw-bold">Họ và tên: </span>
                                        <input type="text" class="name_users_input form-control" name="name">
                                    </p>
                                    <p>
                                        <span class="fw-bold">Email: </span>
                                        <input type="text" class="email_users_input form-control" name="email">
                                    </p>
                                    <p>
                                        <span class="fw-bold">Giới tính: </span>
                                        <select name="gender" class="gender_users_input form-control">
                                            @foreach(['Nam', 'Nữ'] as $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </p>
                                    <p>
                                        <span class="fw-bold">Số điện thoại: </span>
                                        <input type="text" class="phone_users_input form-control" name="phone">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" >Chỉnh sửa</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </form>
        </div>
        </div>
    </div>


    {{-- Model Lock --}}
    <div class="modal fade" id="modalLock" tabindex="-1" aria-labelledby="modalLockLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLockLabel">Chú ý</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.quanlysinhvien.lockStudent') }}" method="post" class="form-lock-users">
                @method('delete')
                @csrf
                <input type="hidden" name="id" class="id-lock-users">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <span class="">
                                    Bạn có thực sự muốn khóa tài khoản
                                    "<span class="fw-bold text-danger name_users_lock"></span>"
                                    này ?
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger btn-sm">Xác nhận</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                </div>
            </form>
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

        $(".danhsachsinhvien").addClass("active")
    </script>


    <script>
        let table = new DataTable('#myTable', {
            "language": {
                "emptyTable": "Bảng không có dữ liệu"
            },
            "order": [[ 1, 'asc' ]],
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.quanlysinhvien.dataListStudent') !!}',
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
                })
        })

        var modalUpdate = document.getElementById('modalUpdate')
        modalUpdate.addEventListener('show.bs.modal', function (event) {
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
                    $(".name_users_input").val(data.name)
                    $(".gender_users_input").val(data.gender)
                    $(".phone_users_input").val(data.phone)
                    $(".email_users_input").val(data.email)
                    $(".id_users_input").val(recipient)
                })
        })


        var modalLock = document.getElementById('modalLock')
        modalLock.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var id = button.getAttribute('data-bs-id')
            var name = button.getAttribute('data-bs-name')

            $(".name_users_lock").text(name)
            $('.id-lock-users').val(id)
        })


        $("#btn-search").click(function() {
            let seach = $("#input-search").val()
            table.search(seach).draw();
        })
    </script>
@stop
