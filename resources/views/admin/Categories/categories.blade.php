@extends('layouts/default')
@section('title')
    @lang('category.title_page')
    @parent
@stop

@section('header_styles')
    <style>
        div.dt-container .dt-paging .dt-paging-button:hover {
            border: none !important;
            background: none !important;
        }
    </style>
@stop

@section('title_page')
    @lang('category.title_page')
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
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-info btn-sm addBtn">@lang('category.btn.add')</button>
                    </div>
                    <table class="table table-bordered table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>@lang('category.title_table.name')</th>
                                <th>@lang('category.title_table.note')</th>
                                <th>@lang('category.title_table.action')</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    {{-- Endcontent --}}
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Thêm Mới Danh Mục -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">@lang('category.title_form.title_add')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        @csrf
                        <div class="mb-3">
                            <label for="addCategoryName" class="form-label">@lang('category.title_form.name')</label>
                            <input type="text" class="form-control" id="addCategoryName" name="name" required>
                            <span class="text-danger" id="addCategoryNameError"></span>
                        </div>
                        <div class="mb-3">
                            <label for="addCategoryNote" class="form-label">@lang('category.title_form.note')</label>
                            <textarea class="form-control" id="addCategoryNote" name="note"></textarea>
                            <span class="text-danger" id="addCategoryNoteError"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('category.btn.close')</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddCategory()">@lang('category.btn.add_submit')</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Sửa Danh Mục -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">@lang('category.title_form.title_edit')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">@lang('category.title_form.name')</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                            <span class="text-danger" id="editCategoryNameError"></span>
                        </div>
                        <div class="mb-3">
                            <label for="categoryNote" class="form-label">@lang('category.title_form.note')</label>
                            <textarea class="form-control" id="categoryNote" name="note"></textarea>
                            <span class="text-danger" id="editCategoryNoteError"></span>
                        </div>
                        <input type="hidden" id="categoryId" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('category.btn.close')</button>
                    <button type="button" class="btn btn-primary"
                        onclick="submitEditCategory()">@lang('category.btn.edit_submit')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi Tiết Danh Mục -->
    <div class="modal fade" id="showCategoryModal" tabindex="-1" aria-labelledby="showCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showCategoryModalLabel">@lang('category.title_form.title_detail')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>@lang('category.title_form.name'):</strong> <span id="categoryNameDetail"></span></p>
                    <p><strong>@lang('category.title_form.note'):</strong> <span id="categoryNoteDetail"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('category.btn.close')</button>
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
            "order": [
                [1, 'asc']
            ],
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.categories.dataListCategory') !!}',
            order: [],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'note',
                    name: 'note'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    </script>

    {{-- Add --}}
    <script>
        document.querySelector('.addBtn').addEventListener('click', function() {
            // Hiển thị modal thêm mới danh mục
            $('#addCategoryModal').modal('show');
        });

        function submitAddCategory() {
            var formData = {
                name: document.getElementById('addCategoryName').value,
                note: document.getElementById('addCategoryNote').value,
                _token: '{{ csrf_token() }}'
            };

            fetch('{{ route('admin.categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {

                        showAlert(response.success, 'success');
                        $('#addCategoryModal').modal('hide');
                        $('#myTable').DataTable().ajax.reload(); // Reload lại bảng để cập nhật danh mục mới
                    } else {
                        // Hiển thị lỗi nếu có
                        if (response.errors.name) {
                            document.getElementById('addCategoryNameError').textContent = response.errors.name[0];
                        }
                        if (response.errors.note) {
                            document.getElementById('addCategoryNoteError').textContent = response.errors.note[0];
                        }
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                    console.error('Error:', error);
                });
        }
    </script>

    {{-- edit --}}
    <script>
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('editBtn')) {
                var id = event.target.dataset.id;

                fetch(`{{ route('admin.categories.edit', ':id') }}`.replace(':id', id), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('categoryName').value = data.name;
                        document.getElementById('categoryNote').value = data.note;
                        document.getElementById('categoryId').value = data.id;
                        $('#editCategoryModal').modal('show');
                    })
                    .catch(error => {
                        if (error.status === 422) {
                            // Nếu có lỗi validation, hiển thị lỗi
                            var errors = error.responseJSON.errors;
                            if (errors.name) {
                                document.getElementById('editCategoryNameError').textContent = errors.name[0];
                            }
                            if (errors.note) {
                                document.getElementById('editCategoryNoteError').textContent = errors.note[0];
                            }
                        } else {
                            alert('Có lỗi xảy ra, vui lòng thử lại!');
                        }
                    });
            }
        });

        function submitEditCategory() {
            var id = document.getElementById('categoryId').value;
            var formData = {
                name: document.getElementById('categoryName').value,
                note: document.getElementById('categoryNote').value,
                _token: '{{ csrf_token() }}'
            };

            fetch(`{{ route('admin.categories.update', ':id') }}`.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(response => {
                    // alert('Danh mục đã được cập nhật!');
                    showAlert(response.success, 'success');
                    $('#editCategoryModal').modal('hide');
                    $('#myTable').DataTable().ajax.reload();
                })
                .catch(() => {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                });
        }
    </script>


    {{-- show --}}
    <script>
        // Xử lý khi nhấn nút "Chi tiết"
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('showBtn')) {
                var id = event.target.dataset.id;

                fetch(`{{ route('admin.categories.show', ':id') }}`.replace(':id', id), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Hiển thị dữ liệu trong modal
                        document.getElementById('categoryNameDetail').textContent = data.name;
                        document.getElementById('categoryNoteDetail').textContent = data.note;
                        $('#showCategoryModal').modal('show'); // Hiển thị modal
                    })
                    .catch(() => {
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                    });
            }
        });
    </script>


    {{-- Delete --}}
    <script>
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('deleteBtn')) {
                var id = event.target.dataset.id;

                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa danh mục này?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ trans('category.btn.delete') }}',
                    cancelButtonText: '{{ trans('category.btn.close') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Gọi API để xóa danh mục
                        fetch(`{{ route('admin.categories.destroy', ':id') }}`.replace(':id', id), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(response => {
                                if (response.success) {
                                    showAlert(response.success, 'success');
                                    $('#myTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Lỗi!',
                                        'Có lỗi xảy ra, vui lòng thử lại.',
                                        'error'
                                    );
                                }
                            })
                            .catch(() => {
                                Swal.fire(
                                    'Lỗi!',
                                    'Có lỗi xảy ra, vui lòng thử lại.',
                                    'error'
                                );
                            });
                    }
                });




            }
        });
    </script>

@stop
