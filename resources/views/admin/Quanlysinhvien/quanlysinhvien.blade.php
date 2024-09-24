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
@stop

@section('footer_scripts')
    <script src="{{ asset('chart.js') }}"></script>
    <script>
        var dd = document.querySelector(".dashboard")
        dd.classList.add("show")
        $(".dashboard-navbar").addClass("here")
        $(".dashboard-navbar").addClass("show")
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
    </script>
@stop
