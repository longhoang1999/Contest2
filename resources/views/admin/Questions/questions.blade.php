@extends('layouts/default')
@section('title')
    @lang('question.title_page')
    @parent
@stop

@section('header_styles')
    <style>
        div.dt-container .dt-paging .dt-paging-button:hover {
            border: none !important;
            background: none !important;
        }

        .input-group-text {
            cursor: pointer;
        }

        .form-check-input.small-switch {
            width: 2rem;
            height: 1rem;
            transform: scale(0.75);
        }
    </style>
@stop

@section('title_page')
    @lang('question.title_page')
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
                        <button class="btn btn-info btn-sm addBtn">@lang('question.btn.add')</button>
                    </div>
                    <table class="table table-bordered table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>@lang('question.title_table.question_text')</th>
                                {{-- <th>@lang('question.title_table.image')</th> --}}
                                <th>@lang('question.title_table.type')</th>
                                <th>@lang('question.title_table.difficulty')</th>
                                <th>@lang('question.title_table.status')</th>
                                <th>@lang('question.title_table.action')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    {{-- Endcontent --}}
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Thêm Mới Câu Hỏi -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addQuestionModalLabel">@lang('question.title_form.title_add')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addQuestionForm">
                        <input type="hidden" id="addQuestionId" name="id">

                        <div class="mb-4">
                            <label for="addQuestionText" class="form-label">@lang('question.title_form.question_text'):</label>
                            <textarea class="form-control mt-2" id="addQuestionText" name="question_text" rows="4"
                                oninput="updateMath('addQuestionText','outputAdd'), autoCloseTag(event)"
                                placeholder="Ghi công thức toán học trong thẻ <latex>...</latex>>" required></textarea>
                            <span class="text-danger" id="addQuestionTextError"></span>

                            <div id="outputAdd"></div>
                        </div>
                        <!-- Trường hình ảnh -->
                        <div class="mb-4 row">
                            <label for="addImage" class="form-label">@lang('question.title_form.image'):</label>
                            <div class="col-6">
                                <input type="file" class="form-control" id="addImage" name="image" />
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"
                                        onclick="toggleUrlInput('addImageUrl')" id="basic-addon1">@lang('question.btn.url')</span>

                                    <input type="text" class="form-control url-input" id="addImageUrl" name="image_url"
                                        placeholder="Nhập URL hình ảnh" aria-describedby="basic-addon1" />
                                </div>
                            </div>
                            <span class="text-danger" id="addImageError"></span>
                        </div>
                        <!-- Trường video -->
                        <div class="mb-4 row">
                            <label for="addVideo" class="form-label">@lang('question.title_form.video'):</label>
                            <div class="col-6">
                                <input type="file" class="form-control" id="addVideo" name="video" />
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"
                                        onclick="toggleUrlInput('addVideoUrl')" id="basic-addon2">@lang('question.btn.url')</span>
                                    <input type="text" class="form-control url-input" id="addVideoUrl" name="video_url"
                                        placeholder="Nhập URL video" aria-describedby="basic-addon2" />
                                </div>
                            </div>
                            <span class="text-danger" id="addVideoError"></span>
                        </div>
                        <!-- Trường âm thanh -->
                        <div class="mb-4 row">
                            <label for="addAudio" class="form-label">@lang('question.title_form.audio'):</label>
                            <div class="col-6">
                                <input type="file" class="form-control" id="addAudio" name="audio" />
                            </div>
                            <div class="col-sm">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"
                                        onclick="toggleUrlInput('addAudioUrl')" id="basic-addon3">@lang('question.btn.url')</span>
                                    <input type="text" class="form-control url-input" id="addAudioUrl" name="audio_url"
                                        placeholder="Nhập URL âm thanh" aria-describedby="basic-addon3" />
                                </div>
                            </div>
                            <span class="text-danger" id="addAudioError"></span>
                        </div>

                        <div class="mb-4 row">
                            <div class="col-5">
                                <label for="addQuestionType" class="form-label">@lang('question.title_form.type'):</label>
                                <select class="form-select" id="addQuestionType" name="type">
                                    <option value="0">
                                        @lang('question.select_type.tracnghiem')
                                    </option>
                                    <option value="1">@lang('question.select_type.tuluan')</option>
                                    <option value="2">@lang('question.select_type.khac')</option>
                                </select>
                                <span class="text-danger" id="addQuestionTypeError"></span>

                            </div>
                            <div class="col-3">
                                <label for="addDifficulty" class="form-label">@lang('question.title_form.difficulty'):</label>
                                <input type="number" class="form-control" id="addDifficulty" name="difficulty"
                                    min="1" />

                            </div>
                            <div class="col-3">
                                <label class="form-label">@lang('question.title_form.status'):</label>
                                <div class="d-flex gap-5">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="addIsActive"
                                            name="is_active" />
                                        <label class="form-check-label" for="addIsActive">Active</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="addIsDemo" name="is_demo" />
                                        <label class="form-check-label" for="addIsDemo">Demo</label>
                                    </div>
                                </div>
                            </div>
                            <span class="text-danger" id="addDifficultyError"></span>
                        </div>
                        <div class="mb-4">
                            <label for="addNote" class="form-label">@lang('question.title_form.note'):</label>
                            <textarea class="form-control mt-2" id="addNote" name="note" rows="4"required></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('question.btn.close')</button>
                    <button type="button" class="btn btn-primary"
                        onclick="submitAddQuestion()">@lang('question.btn.add_submit')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sửa Câu Hỏi -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="editQuestionModalLabel">@lang('question.title_form.title_edit')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editQuestionForm">
                        <input type="hidden" id="editQuestionId" name="id">

                        <div class="mb-4">
                            <label for="editQuestionText" class="form-label">@lang('question.title_form.question_text'):</label>
                            <textarea class="form-control mt-2" id="editQuestionText" name="question_text" rows="4"
                                oninput="updateMath('editQuestionText','outputEdit'), autoCloseTag(event)"
                                placeholder="Ghi công thức toán học trong thẻ <latex>...</latex>>"></textarea>
                            <span class="text-danger" id="editQuestionTextError"></span>

                            <div id="outputEdit"></div>
                        </div>
                        <!-- Trường hình ảnh -->
                        <div class="mb-4 row">
                            <label for="editImage" class="form-label">@lang('question.title_form.image'):</label>
                            <div class="col-6">
                                <input type="file" class="form-control" id="editImage" name="image" />
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"
                                        onclick="toggleUrlInput('editImageUrl')"
                                        id="basic-addon1">@lang('question.btn.url')</span>
                                    <input type="text" class="form-control url-input" id="editImageUrl"
                                        name="image_url" placeholder="Nhập URL hình ảnh"
                                        aria-describedby="basic-addon1" />
                                </div>
                            </div>

                            <span class="text-danger" id="editImageError"></span>
                        </div>
                        <!-- Trường video -->
                        <div class="mb-4 row">
                            <label for="editVideo" class="form-label">@lang('question.title_form.video'):</label>
                            <div class="col-6">
                                <input type="file" class="form-control" id="editVideo" name="video" />
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"
                                        onclick="toggleUrlInput('editVideoUrl')"
                                        id="basic-addon2">@lang('question.btn.url')</span>
                                    <input type="text" class="form-control url-input" id="editVideoUrl"
                                        name="video_url" placeholder="Nhập URL video" aria-describedby="basic-addon2" />
                                </div>
                            </div>
                            <span class="text-danger" id="editVideoError"></span>
                        </div>
                        <!-- Trường âm thanh -->
                        <div class="mb-4 row">
                            <label for="editAudio" class="form-label">@lang('question.title_form.audio'):</label>
                            <div class="col-6">
                                <input type="file" class="form-control" id="editAudio" name="audio" />
                            </div>
                            <div class="col-sm">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white"
                                        onclick="toggleUrlInput('editAudioUrl')"
                                        id="basic-addon3">@lang('question.btn.url')</span>
                                    <input type="text" class="form-control url-input" id="editAudioUrl"
                                        name="audio_url" placeholder="Nhập URL âm thanh"
                                        aria-describedby="basic-addon3" />
                                </div>
                            </div>
                            <span class="text-danger" id="editAudioError"></span>
                        </div>

                        <div class="mb-4 row">
                            <div class="col-5">
                                <label for="type" class="form-label">@lang('question.title_form.type'):</label>
                                <select class="form-select" id="editQuestionType" name="type">
                                    <option value="0">
                                        @lang('question.select_type.tracnghiem')
                                    </option>
                                    <option value="1">@lang('question.select_type.tuluan')</option>
                                    <option value="2">@lang('question.select_type.khac')</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="editDifficulty" class="form-label">@lang('question.title_form.difficulty'):</label>
                                <input type="number" class="form-control" id="editDifficulty" name="difficulty"
                                    min="1" />
                            </div>
                            <div class="col-3">
                                <label class="form-label">@lang('question.title_form.status'):</label>
                                <div class="d-flex gap-5">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="editIsActive"
                                            name="is_active" checked />
                                        <label class="form-check-label" for="editIsActive">Active</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="editIsDemo"
                                            name="is_demo" />
                                        <label class="form-check-label" for="editIsDemo">Demo</label>
                                    </div>
                                </div>
                            </div>
                            <span class="text-danger" id="editDifficultyError"></span>

                        </div>

                        <div class="mb-4">
                            <label for="editNote" class="form-label">@lang('question.title_form.note')</label>
                            <textarea class="form-control mt-2" id="editNote" name="note" rows="4"></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-primary btnAnswer"
                        id="btnAnswerQuestion">@lang('question.btn.answer')</button>
                    <div>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">@lang('question.btn.close')</button>
                        <button type="button" class="btn btn-success"
                            onclick="submitEditQuestion()">@lang('question.btn.edit_submit')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal list Đáp Án -->
    <div class="modal" id="answersModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Đáp án</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body d-flex flex-column justify-content-center gap-3" id="modal-body">
                    <!-- Nội dung đáp án sẽ được chèn vào đây -->
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Quay lại</button>
                    <button type="button" class="btn btn-primary addAnswer">Thêm Đáp Án</button>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal add đáp án --}}
    <div class="modal fade" id="addAnswerModal" tabindex="-1" role="dialog" aria-labelledby="addAnswerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnswerModalLabel">Thêm mới Đáp Án</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="questionId" />
                    <div class="form-group mb-3">
                        <label class="form-label" for="addAnswerText">Nội Dung Đáp Án</label>
                        <textarea id="addAnswerText" class="form-control" name="answer_text"></textarea>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="addImageAnswer" class="form-label">@lang('question.title_form.image'):</label>
                        <div class="col-6">
                            <input type="file" class="form-control" id="addImageAnswer" name="image" />
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"
                                    id="basic-addon1">@lang('question.btn.url')</span>
                                <input type="text" class="form-control url-input" id="addImageAnswerUrl"
                                    name="image_url" placeholder="Nhập URL hình ảnh" aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="addVideoAnswer" class="form-label">@lang('question.title_form.video'):</label>
                        <div class="col-6">
                            <input type="file" class="form-control" id="addVideoAnswer" name="video" />
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"
                                    id="basic-addon1">@lang('question.btn.url')</span>
                                <input type="text" class="form-control url-input" id="addVideoAnswerUrl"
                                    name="video_url" placeholder="Nhập URL video" aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="addAudioAnswer" class="form-label">@lang('question.title_form.audio'):</label>
                        <div class="col-6">
                            <input type="file" class="form-control" id="addAudioAnswer" name="audio" />
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"
                                    id="basic-addon1">@lang('question.btn.url')</span>
                                <input type="text" class="form-control url-input" id="addAudioAnswerUrl"
                                    name="audio_url" placeholder="Nhập URL ân thanh" aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="addIsCorrect">Trạng Thái Đúng</label>
                        <select id="addIsCorrect" class="form-control" onchange="toggleCorrectAnswerField()">
                            <option value="0">Sai</option>
                            <option value="1">Đúng</option>
                            <option value="2">Khác</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="addCorrectAnswerField" style="display: none;">
                        <label class="form-label" for="addCorrectAnswer">Câu Trả Lời Đúng</label>
                        <input type="text" id="addCorrectAnswer" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="addNewAnswer()">Lưu Thay Đổi</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal edit đáp án --}}
    <div class="modal fade" id="editAnswerModal" tabindex="-1" role="dialog" aria-labelledby="editAnswerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnswerModalLabel">Chỉnh Sửa Đáp Án</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="answerId" />
                    <div class="form-group mb-3">
                        <label class="form-label" for="answerText">Nội Dung Đáp Án</label>
                        <textarea id="answerText" class="form-control" name=""></textarea>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="editImageAnswer" class="form-label">@lang('question.title_form.image'):</label>
                        <div class="col-6">
                            <input type="file" class="form-control" id="editImageAnswer" name="image" />
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"
                                    id="basic-addon1">@lang('question.btn.url')</span>
                                <input type="text" class="form-control url-input" id="editImageAnswerUrl"
                                    name="image_url" placeholder="Nhập URL hình ảnh" aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="editVideoAnswer" class="form-label">@lang('question.title_form.video'):</label>
                        <div class="col-6">
                            <input type="file" class="form-control" id="editVideoAnswer" name="video" />
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"
                                    id="basic-addon1">@lang('question.btn.url')</span>
                                <input type="text" class="form-control url-input" id="editVideoAnswerUrl"
                                    name="video_url" placeholder="Nhập URL video" aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3 row">
                        <label for="editAudioAnswer" class="form-label">@lang('question.title_form.audio'):</label>
                        <div class="col-6">
                            <input type="file" class="form-control" id="editAudioAnswer" name="audio" />
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white"
                                    id="basic-addon1">@lang('question.btn.url')</span>
                                <input type="text" class="form-control url-input" id="editAudioAnswerUrl"
                                    name="audio_url" placeholder="Nhập URL ân thanh" aria-describedby="basic-addon1" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="isCorrect">Trạng Thái Đúng</label>
                        <select id="isCorrect" class="form-control" onchange="toggleCorrectAnswerField()">
                            <option value="0">Sai</option>
                            <option value="1">Đúng</option>
                            <option value="2">Khác</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="correctAnswerField" style="display: none;">
                        <label class="form-label" for="correctAnswer">Câu Trả Lời Đúng</label>
                        <input type="text" id="correctAnswer" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveAnswer()">Lưu Thay Đổi</button>
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
    {{-- <script>
        function toggleUrlInput(urlInputId) {
            var urlInput = document.getElementById(urlInputId);
            if (
                urlInput.style.display === "none" ||
                urlInput.style.display === ""
            ) {
                urlInput.style.display = "block";
            } else {
                urlInput.style.display = "none";
            }
        }
    </script> --}}
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        function updateMath(inputID, outputID) {
            // console.log(input);

            const input = document.getElementById(inputID).value;
            const formattedInput = input
                .replace(/<latex>/g, "\\(")
                .replace(/<\/latex>/g, "\\)")
                .replace(/<latex-b>/g, "\\[")
                .replace(/<\/latex-b>/g, "\\]");
            document.getElementById(outputID).innerHTML = formattedInput;
            MathJax.typeset();
        }

        function autoCloseTag(event) {
            const textarea = event.target;
            const value = textarea.value;
            const cursorPosition = textarea.selectionStart;
            const openTags = ["<latex>", "<latex-b>"];
            const closeTags = ["</latex>", "</latex-b>"];

            openTags.forEach((tag, index) => {
                const closeTag = closeTags[index];
                const tagLength = tag.length;
                const closeTagLength = closeTag.length;

                // Kiểm tra xem thẻ đóng đã tồn tại hay chưa
                if (
                    value.substring(cursorPosition - tagLength, cursorPosition) === tag
                ) {
                    const afterCursor = value.substring(cursorPosition);
                    if (!afterCursor.includes(closeTag)) {
                        const newValue =
                            value.substring(0, cursorPosition) +
                            closeTag +
                            value.substring(cursorPosition);
                        textarea.value = newValue;
                        textarea.selectionStart = cursorPosition;
                        textarea.selectionEnd = cursorPosition;
                    }
                }
            });
        }
    </script>


    <script>
        $(document).ready(function() {
            // Khởi tạo DataTables
            $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.questions.dataListQuestion') !!}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'question_text',
                        name: 'question_text'
                    },
                    // {
                    //     data: 'image_url',
                    //     name: 'image_url'
                    // },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'difficulty',
                        name: 'difficulty'
                    },
                    {
                        data: 'is_active',
                        name: 'status',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });
    </script>

    {{-- Add --}}
    <script>
        document.querySelector('.addBtn').addEventListener('click', function() {
            $('#addQuestionModal').modal('show');
        });

        function submitAddQuestion() {
            let formData = new FormData();

            formData.append('question_text', document.getElementById('addQuestionText').value);
            formData.append('image_url', document.getElementById('addImageUrl').value);
            formData.append('video_url', document.getElementById('addVideoUrl').value);
            formData.append('audio_url', document.getElementById('addAudioUrl').value);
            formData.append('type', document.getElementById('addQuestionType').value);
            formData.append('difficulty', document.getElementById('addDifficulty').value);
            formData.append('is_active', document.getElementById('addIsActive').checked ? '1' : '0');
            formData.append('is_demo', document.getElementById('addIsDemo').checked ? '1' : '0');
            formData.append('note', document.getElementById('addNote').value);
            formData.append('_token', '{{ csrf_token() }}');

            // Thêm các trường file (hình ảnh, video, audio)
            let imageFile = document.getElementById('addImage').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            let videoFile = document.getElementById('addVideo').files[0];
            if (videoFile) {
                formData.append('video', videoFile);
            }

            let audioFile = document.getElementById('addAudio').files[0];
            if (audioFile) {
                formData.append('audio', audioFile);
            }

            fetch('{{ route('admin.questions.store') }}', {
                    method: 'POST',
                    body: formData, // Không cần 'Content-Type' khi sử dụng FormData
                })
                .then(response => response.json()) // Chuyển đổi phản hồi thành JSON
                .then(data => {

                    if (data.success) {
                        showAlert(data.success, 'success');
                        $('#addQuestionModal').modal('hide');
                        $('#myTable').DataTable().ajax.reload(); // Reload lại bảng nếu có

                        // Reset form
                        document.getElementById('addQuestionText').value = '';
                        document.getElementById('addImage').value = '';
                        document.getElementById('addImageUrl').value = '';
                        document.getElementById('addVideo').value = '';
                        document.getElementById('addVideoUrl').value = '';
                        document.getElementById('addAudio').value = '';
                        document.getElementById('addAudioUrl').value = '';
                        document.getElementById('addQuestionType').value = '1';
                        document.getElementById('addDifficulty').value = '';
                        document.getElementById('addIsActive').checked = false;
                        document.getElementById('addIsDemo').checked = false;
                        document.getElementById('addNote').value = '';
                    } else {
                        // Hiển thị lỗi nếu có
                        if (data.errors.question_text) {
                            document.getElementById('addQuestionTextError').textContent = data.errors.question_text[0];
                        }
                        if (data.errors.type) {
                            document.getElementById('addQuestionTypeError').textContent = data.errors.type[0];
                        }
                        if (data.errors.difficulty) {
                            document.getElementById('addDifficultyError').textContent = data.errors.difficulty[0];
                        }
                        if (data.errors.image) {
                            document.getElementById('addImageError').textContent = data.errors.image[0];
                        }
                        if (data.errors.video) {
                            document.getElementById('addVideoError').textContent = data.errors.video[0];
                        }
                        if (data.errors.audio) {
                            document.getElementById('addAudioError').textContent = data.errors.audio[0];
                        }
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                    console.error('Error:', error);
                });
        }
    </script>

    {{-- Edit --}}
    <script>
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('editBtn')) {
                var id = e.target.getAttribute('data-id');


                fetch(`{{ route('admin.questions.edit', ':id') }}`.replace(':id', id), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editQuestionId').value = data.id;
                        document.getElementById('editQuestionText').value = data.question_text;
                        document.getElementById('editImageUrl').value = data.image_url;
                        document.getElementById('editVideoUrl').value = data.video_url;
                        document.getElementById('editAudioUrl').value = data.audio_url;
                        document.getElementById('editQuestionType').value = data.type;
                        document.getElementById('editDifficulty').value = data.difficulty;
                        document.getElementById('editIsActive').checked = data.is_active == 1 ? true : false;
                        document.getElementById('editIsDemo').checked = data.is_demo == 1 ? true : false;
                        document.getElementById('editNote').value = data.note;
                        document.getElementById('btnAnswerQuestion').setAttribute('data-id', data.id);
                        $('#editQuestionModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                    });
            }
        });

        function submitEditQuestion() {
            let formData = new FormData();

            // Thêm các trường text
            formData.append('question_text', document.getElementById('editQuestionText').value);
            formData.append('image_url', document.getElementById('editImageUrl').value);
            formData.append('video_url', document.getElementById('editVideoUrl').value);
            formData.append('audio_url', document.getElementById('editAudioUrl').value);
            formData.append('type', document.getElementById('editQuestionType').value);
            formData.append('difficulty', document.getElementById('editDifficulty').value);
            formData.append('is_active', document.getElementById('editIsActive').checked ? '1' : '0');
            formData.append('is_demo', document.getElementById('editIsDemo').checked ? '1' : '0');
            formData.append('note', document.getElementById('editNote').value);
            formData.append('_token', '{{ csrf_token() }}');

            // Thêm các trường file (hình ảnh, video, audio)
            let imageFile = document.getElementById('editImage').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            let videoFile = document.getElementById('editVideo').files[0];
            if (videoFile) {
                formData.append('video', videoFile);
            }

            let audioFile = document.getElementById('editAudio').files[0];
            if (audioFile) {
                formData.append('audio', audioFile);
            }
            formData.append('_method', 'PUT');

            var id = document.getElementById('editQuestionId').value;

            fetch(`{{ route('admin.questions.update', ':id') }}`.replace(':id', id), {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.success, 'success');
                        $('#editQuestionModal').modal('hide');
                        $('#myTable').DataTable().ajax.reload();
                    } else {
                        // Hiển thị lỗi nếu có
                        if (data.errors.question_text) {
                            document.getElementById('editQuestionTextError').textContent = data.errors.question_text[0];
                        }
                        if (data.errors.type) {
                            document.getElementById('editQuestionTypeError').textContent = data.errors.type[0];
                        }
                        if (data.errors.difficulty) {
                            document.getElementById('editDifficultyError').textContent = data.errors.difficulty[0];
                        }
                        if (data.errors.image) {
                            document.getElementById('editImageError').textContent = data.errors.image[0];
                        }
                        if (data.errors.video) {
                            document.getElementById('editVideoError').textContent = data.errors.video[0];
                        }
                        if (data.errors.audio) {
                            document.getElementById('editAudioError').textContent = data.errors.audio[0];
                        }
                    }


                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                });
        };
    </script>

    {{-- Delete --}}
    <script>
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('deleteBtn')) {
                var id = event.target.dataset.id;

                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa câu hỏi này?',
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
                        fetch(`{{ route('admin.questions.destroy', ':id') }}`.replace(':id', id), {
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

    {{-- Answer --}}
    <script>
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btnAnswer')) {
                // console.log(e.target.getAttribute('data-id'));
                var id = e.target.dataset.id;
                document.getElementById('questionId').setAttribute('data-id', id);
                var modalBody = document.getElementById('modal-body');;
                modalBody.innerHTML = ''; // Xóa nội dung cũ

                // Lấy dữ liệu từ server
                fetch(`{{ route('admin.questions.answers.index', ':id') }}`.replace(':id', id))
                    .then(response => response.json())
                    .then(answers => {
                        // console.log(answers);

                        answers.forEach(answer => {
                            var answerHtml = `
                                <div
                                style="box-shadow: 0 6px 6px -5px rgba(0, 0, 0, 0.1)"
                                id="answer-${answer.id}"
                                class="answer mb-6 p-4 bg-white rounded"
                                >
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="title-answer d-flex gap-2 align-items-center">
                                    ${answer.is_correct == 0 ? '<i class="bi bi-x-circle text-danger fs-4"></i>' : '<i class="bi bi-check-circle text-success fs-4"></i>'}
                                    
                                    <h6 class="question-text mb-0 fs-5">${answer.answer_text}</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="flexSwitchCheckChecked"
                                    ${answer.is_correct == 0 ? '' : 'checked'}

                                    data-answer-id="${ answer.id }" 
                                    
                                    onchange="toggleIsCorrect(this)"
                                    />
                                </div>
                                </div>
                            `;

                            if (answer.image_url) {
                                answerHtml +=
                                    `<div class="mt-2">
                                    <img
                                        src="${answer.image_url}"
                                        alt=""
                                        class="w-100"
                                    />
                                    </div>`;
                            }
                            if (answer.video_url) {
                                answerHtml += `<div class="mt-3">
                                <video
                                    src="${answer.video_url}"
                                    controls
                                    class="w-100 rounded-md"
                                >
                                    Your browser does not support the video tag.
                                </video>
                                </div>`;
                            }
                            if (answer.audio_url) {
                                answerHtml += `<div class="mt-2">
                                <audio
                                    src="${answer.audio_url}"
                                    controls
                                    class="w-100"
                                >
                                    Your browser does not support the audio tag.
                                </audio>
                                </div>`;
                            }
                            if (answer.is_correct == 2 && answer.correct_answer) {
                                answerHtml +=
                                    `<p class="mt-2">Đáp án đúng: ${answer.correct_answer}</p>`;
                            }

                            answerHtml += `
                                <div class="d-flex justify-content-end mt-4 gap-2">
                                <button class="btn rounded btn-sm btn-warning editAnswerBtn" data-id="${answer.id}">Edit</button>
                                <button class="btn rounded btn-sm btn-danger" onclick="deleteAnswer(${answer.id})">Delete</button>
                                </div>
                            </div>
                            </div>
                            `
                            modalBody.insertAdjacentHTML('beforeend', answerHtml);
                        });


                        $('#answersModal').modal('show');
                        $('#editQuestionModal').modal('hide');
                    });
            }
        });

        function toggleIsCorrect(checkbox) {
            const answerId = checkbox.dataset.answerId;
            const isChecked = checkbox.checked ? 1 : 0;

            fetch(`{{ route('admin.questions.answers.toggle', ':id') }}`.replace(':id', answerId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        is_correct: isChecked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Lấy icon trong cùng một dòng
                    const icon = checkbox.closest('.d-flex').querySelector('.title-answer i');

                    if (isChecked === 1) {
                        icon.classList.remove('bi-x-circle', 'text-danger');
                        icon.classList.add('bi-check-circle', 'text-success');
                    } else {
                        icon.classList.remove('bi-check-circle', 'text-success');
                        icon.classList.add('bi-x-circle', 'text-danger')
                    }

                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Show store
        document.querySelector('.addAnswer').addEventListener('click', function() {
            // Hiển thị modal thêm mới danh mục
            $('#addAnswerModal').modal('show');
        });

        function addNewAnswer() {

            let formData = new FormData();
            formData.append('question_id', document.getElementById('questionId').getAttribute('data-id'));
            formData.append('answer_text', document.getElementById('addAnswerText').value);
            formData.append('image_url', document.getElementById('addImageAnswerUrl').value);
            formData.append('video_url', document.getElementById('addVideoAnswerUrl').value);
            formData.append('audio_url', document.getElementById('addAudioAnswerUrl').value);
            formData.append('is_correct', document.getElementById('addIsCorrect').value);
            formData.append('correct_answer', document.getElementById('addCorrectAnswer').value);
            formData.append('_token', '{{ csrf_token() }}');


            // Thêm các trường file (hình ảnh, video, audio)
            let imageFile = document.getElementById('addImageAnswer').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            let videoFile = document.getElementById('addVideoAnswer').files[0];
            if (videoFile) {
                formData.append('video', videoFile);
            }

            let audioFile = document.getElementById('addAudioAnswer').files[0];
            if (audioFile) {
                formData.append('audio', audioFile);
            }

            fetch(`{{ route('admin.questions.answers.store') }}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message ||
                                'Failed to add new answer.'); // Lấy thông báo lỗi từ server
                        });
                    }
                    return response.json(); // Chờ phản hồi JSON
                })
                .then(data => {
                    console.log(data);

                    if (data.success) {
                        var answerHtml = `
                                <div
                                style="box-shadow: 0 6px 6px -5px rgba(0, 0, 0, 0.1)"
                                id="answer-${data.answer.id}"
                                class="answer mb-6 p-4 bg-white rounded"
                                >
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="title-answer d-flex gap-2 align-items-center">
                                    ${data.answer.is_correct == 0 ? '<i class="bi bi-x-circle text-danger fs-4"></i>' : '<i class="bi bi-check-circle text-success fs-4"></i>'}
                                    
                                    <h6 class="question-text mb-0 fs-5">${data.answer.answer_text}</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="flexSwitchCheckChecked"
                                    ${data.answer.is_correct == 0 ? '' : 'checked'}

                                    data-answer-id="${ data.answer.id }" 
                                    
                                    onchange="toggleIsCorrect(this)"
                                    />
                                </div>
                                </div>
                            `;

                        if (data.answer.image_url) {
                            answerHtml +=
                                `<div class="mt-2">
                                    <img
                                        src="${data.answer.image_url}"
                                        alt=""
                                        class="w-100"
                                    />
                                    </div>`;
                        }
                        if (data.answer.video_url) {
                            answerHtml += `<div class="mt-3">
                                <video
                                    src="${data.answer.video_url}"
                                    controls
                                    class="w-100 rounded-md"
                                >
                                    Your browser does not support the video tag.
                                </video>
                                </div>`;
                        }
                        if (data.answer.audio_url) {
                            answerHtml += `<div class="mt-2">
                                <audio
                                    src="${data.answer.audio_url}"
                                    controls
                                    class="w-100"
                                >
                                    Your browser does not support the audio tag.
                                </audio>
                                </div>`;
                        }
                        if (data.answer.is_correct == 2 && data.answer.correct_answer) {
                            answerHtml +=
                                `<p class="mt-2">Đáp án đúng: ${data.answer.correct_answer}</p>`;
                        }

                        answerHtml += `
                                <div class="d-flex justify-content-end mt-4 gap-2">
                                <button class="btn rounded btn-sm btn-warning editAnswerBtn" data-id="${data.answer.id}">Edit</button>
                                <button class="btn rounded btn-sm btn-danger" onclick="deleteAnswer(${data.answer.id})">Delete</button>
                                </div>
                            </div>
                            </div>
                            `

                        document.getElementById('modal-body').insertAdjacentHTML('beforeend', answerHtml);


                        // Đóng modal
                        $('#addAnswerModal').modal('hide'); // Đóng modal
                        alert('Câu trả lời đã được thêm thành công!'); // Thông báo thành công
                    } else {
                        alert('Đã xảy ra lỗi trong quá trình thêm câu trả lời.'); // Thông báo lỗi
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi trong quá trình thêm câu trả lời: ' + error.message); // Thông báo lỗi
                });
        }
        // Show Edit
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('editAnswerBtn')) {
                var id = e.target.getAttribute('data-id');
                fetch(`{{ route('admin.questions.answers.edit', ':id') }}`.replace(':id', id), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(answer => {
                        const modal = document.getElementById('editAnswerModal');
                        modal.querySelector('#answerId').value = answer.id;
                        modal.querySelector('#answerText').value = answer.answer_text;
                        modal.querySelector('#editImageAnswerUrl').value = answer.image_url;
                        modal.querySelector('#editVideoAnswerUrl').value = answer.video_url;
                        modal.querySelector('#editAudioAnswerUrl').value = answer.audio_url;
                        modal.querySelector('#isCorrect').value = answer.is_correct;
                        modal.querySelector('#correctAnswer').value = answer.correct_answer;

                        toggleCorrectAnswerField(); // Gọi hàm để hiển thị hoặc ẩn trường correct_answer

                        $('#editAnswerModal').modal('show'); // Mở modal
                    })
                    .catch(error => {
                        console.error('Error fetching answer:', error);
                        alert('An error occurred while fetching the answer data.');
                    });
            }
        });

        function saveAnswer() {
            let formData = new FormData();
            formData.append('answer_text', document.getElementById('answerText').value);
            formData.append('image_url', document.getElementById('editImageAnswerUrl').value);
            formData.append('video_url', document.getElementById('editVideoAnswerUrl').value);
            formData.append('audio_url', document.getElementById('editAudioAnswerUrl').value);
            formData.append('is_correct', document.getElementById('isCorrect').value);
            formData.append('correct_answer', document.getElementById('correctAnswer').value);
            formData.append('_token', '{{ csrf_token() }}');

            const answerId = document.getElementById('answerId').value;
            // Thêm các trường file (hình ảnh, video, audio)
            let imageFile = document.getElementById('editImageAnswer').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            let videoFile = document.getElementById('editVideoAnswer').files[0];
            if (videoFile) {
                formData.append('video', videoFile);
            }

            let audioFile = document.getElementById('editAudioAnswer').files[0];
            if (audioFile) {
                formData.append('audio', audioFile);
            }
            formData.append('_method', 'PUT');
            // Gửi yêu cầu cập nhật đến server
            fetch(`{{ route('admin.questions.answers.update', ':id') }}`.replace(':id', answerId), {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message ||
                                'Failed to update the answer.'); // Lấy thông báo lỗi từ server
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const answerDiv = document.querySelector(
                            `#answer-${data.answer.id}`);

                        answerDiv.querySelector('.question-text').innerText = data.answer
                            .answer_text; // Cập nhật nội dung đáp án

                        const checkbox = answerDiv.querySelector('input[type="checkbox"]');
                        const icon = answerDiv.querySelector('.title-answer i');

                        if (data.answer.is_correct == 1) {
                            checkbox.checked = true;
                            icon.classList.remove('bi-x-circle', 'text-danger');
                            icon.classList.add('bi-check-circle', 'text-success');
                        } else {
                            checkbox.checked = false;
                            icon.classList.remove('bi-check-circle', 'text-success');
                            icon.classList.add('bi-x-circle', 'text-danger');
                        }

                        const imageElement = answerDiv.querySelector('img');
                        if (imageElement) {
                            imageElement.src = data.answer.image_url;
                        }

                        const videoElement = answerDiv.querySelector('video');
                        if (videoElement) {
                            videoElement.src = data.answer.video_url;
                            videoElement.load();
                        }

                        const audioElement = answerDiv.querySelector('audio');
                        if (audioElement) {
                            audioElement.src = data.answer.audio_url;
                            audioElement.load();
                        }

                        $('#editAnswerModal').modal('hide');
                        // alert('Đáp án đã được cập nhật thành công!'); 
                    } else {
                        alert('Đã xảy ra lỗi trong quá trình cập nhật đáp án.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi trong quá trình cập nhật đáp án: ' + error.message); // Thông báo lỗi
                });
        }

        // Xóa
        function deleteAnswer(answerId) {
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa đáp án này?',
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
                    fetch(`{{ route('admin.questions.answers.destroy', ':id') }}`.replace(':id', answerId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Nếu bạn sử dụng Laravel
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => {
                            document.getElementById(`answer-${answerId}`).remove();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the answer.');
                        });
                }
            });
        }
    </script>

    <script>
        function toggleCorrectAnswerField() {
            const isCorrectSelect = document.getElementById('isCorrect');
            const correctAnswerField = document.getElementById('correctAnswerField');
            const addIsCorrectSelect = document.getElementById('addIsCorrect');
            const addCorrectAnswerField = document.getElementById('addCorrectAnswerField');

            if (isCorrectSelect.value === '2') {
                correctAnswerField.style.display = 'block';
            } else {
                correctAnswerField.style.display = 'none';
                document.getElementById('correctAnswer').value = '';
            }
            if (addIsCorrectSelect.value === '2') {
                addCorrectAnswerField.style.display = 'block';
            } else {
                addCorrectAnswerField.style.display = 'none';
                document.getElementById('correctAnswer').value = '';
            }
        }
    </script>
@stop
