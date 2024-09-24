@extends('layouts/default')
@section('title')
    Danh mục
    @parent
@stop

@section('header_styles')
    <style>

    </style>
@stop

@section('title_page')
    Danh sách danh mục
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
                    <div class="container">

                        <div class="mb-3">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#addCategoryModal">
                                Thêm danh mục mới
                            </button>
                        </div>

                        @if ($categories->isEmpty())
                            <div class="alert alert-info">
                                Không có danh mục nào.
                            </div>
                        @else
                            <table class="table table-striped table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Ghi chú</th>
                                        <th>Thời gian tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->note }}</td>
                                            <td>{{ $category->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                <!-- Nút sửa -->
                                                <button type="button" class="btn btn-primary"
                                                    onclick="editCategory({{ $category->id }})">
                                                    Sửa
                                                </button>

                                                <!-- Nút xóa -->
                                                <form action="" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination (nếu bạn sử dụng phân trang) -->
                            {{-- {{ $categories->links() }} --}}
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal thêm mới danh mục -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Thêm danh mục mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="addCategoryForm">
                        @csrf

                        <!-- Tên danh mục -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary">Lưu danh mục</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal sửa danh mục -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Sửa danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="editCategoryForm">
                        @csrf
                        @method('PUT')

                        <!-- Tên danh mục -->
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-3">
                            <label for="edit_note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="edit_note" name="note" rows="3"></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary">Cập nhật danh mục</button>
                    </form>
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
            // Hàm để mở modal sửa danh mục và điền dữ liệu vào form
            function editCategory(id) {
                // Lấy thông tin danh mục từ server
                fetch(`admin/danh-muc/show/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        // Điền dữ liệu vào form
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_note').value = data.note;

                        // Cập nhật action của form sửa
                        document.getElementById('editCategoryForm').action = `/category/${id}`;

                        // Hiển thị modal sửa danh mục
                        var editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                        editCategoryModal.show();
                    });
            }
        </script>


    @stop
