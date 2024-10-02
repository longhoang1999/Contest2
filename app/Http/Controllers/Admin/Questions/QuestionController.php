<?php

namespace App\Http\Controllers\Admin\Questions;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class QuestionController extends Controller
{
    public function index()
    {
        return view('admin/Questions/questions');
    }

    public function dataListQuestion()
    {
        $questions = Question::select(['id', 'type', 'question_text', 'image_url', 'difficulty', 'is_active', 'is_demo']);

        return DataTables::of($questions)
            ->addIndexColumn()
        // ->addColumn(
        //     'image_url',
        //     function ($question) {
        //         return "<img src='" . $question->image_url . "' width='100'>";
        //     }
        // )
            ->addColumn(
                'type', function ($question) {
                    if ($question->type == 0) {
                        return trans('question.select_type.tracnghiem');
                    } else if ($question->type == 1) {
                        return trans('question.select_type.tuluan');
                    } else {
                        return trans('question.select_type.khac');
                    }
                }
            )
            ->addColumn(
                'is_active',
                function ($question) {
                    $span = '';
                    if ($question->is_active == 1) {
                        $span .= '<span class="badge rounded-pill bg-success mb-2 text-white">Active</span><br>';
                    } else {
                        $span .= '<span class="badge rounded-pill bg-warning text-dark mb-2">Deactive</span><br>';
                    }

                    if ($question->is_demo == 1) {
                        // dd($question->is_demo);
                        $span .= '<span class="badge rounded-pill bg-primary text-white">Demo</span>';
                    }

                    return $span;
                }
            )
            ->addColumn('action', function ($question) {
                $btn = "<div class='btn-group'>
                             <button class='btn btn-primary btn-sm btnAnswer' data-id='" . $question->id . "'>" . trans('question.btn.answer') . "</button>
                            <button class='btn btn-sm btn-warning editBtn' data-id='" . $question->id . "'>" . trans('question.btn.edit') . "</button>
                            <button class='btn btn-danger btn-sm deleteBtn' data-id='" . $question->id . "'>" . trans('question.btn.delete') . "</button>
                        </div>";
                return $btn;
            })
            ->rawColumns(['action', 'is_active', 'image_url'])
            ->make(true);
    }

    // QuestionController.php
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'type' => 'required|in:0,1,2',
            'difficulty' => 'required|integer|min:1',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:10240',
            'audio' => 'nullable|file|mimes:mp3|max:5120',
            'image_url' => 'nullable|string|url',
            'video_url' => 'nullable|string|url',
            'audio_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = new Question();
        $question->question_text = $request->input('question_text');
        $question->type = $request->input('type');
        $question->difficulty = $request->input('difficulty');
        $question->is_active = $request->input('is_active');
        $question->is_demo = $request->input('is_demo');
        $question->note = $request->input('note');

        $question->image_url = $this->handleFileUpdate($request, 'image', null, 'images', 'public');
        $question->video_url = $this->handleFileUpdate($request, 'video', null, 'videos', 'public');
        $question->audio_url = $this->handleFileUpdate($request, 'audio', null, 'audios', 'public');

        $question->save();

        return response()->json([
            'success' => true,
            'message' => 'Câu hỏi đã được thêm thành công!',
            'qs' => $question,
        ]);
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question);
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'type' => 'required|in:0,1,2',
            'difficulty' => 'required|integer|min:1',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:10240',
            'audio' => 'nullable|file|mimes:mp3|max:5120',
            'image_url' => 'nullable|string',
            'video_url' => 'nullable|string',
            'audio_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = Question::findOrFail($id);
        $question->question_text = $request->input('question_text');
        $question->type = $request->input('type');
        $question->difficulty = $request->input('difficulty');
        $question->is_active = $request->input('is_active');
        $question->is_demo = $request->input('is_demo');
        $question->note = $request->input('note');

        // Xử lý các file: image, video, audio
        $question->image_url = $this->handleFileUpdate($request, 'image', $question->image_url, 'images', 'public');
        $question->video_url = $this->handleFileUpdate($request, 'video', $question->video_url, 'videos', 'public');
        $question->audio_url = $this->handleFileUpdate($request, 'audio', $question->audio_url, 'audios', 'public');

        $question->save();

        return response()->json(['success' => 'Cập nhật thành công!']);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['success' => 'Câu hỏi đã được xóa thành công!']);
    }

    private function handleFileUpdate($request, $fileKey, $currentFileUrl, $folder, $disk)
    {
        if ($request->hasFile($fileKey)) {
            // Xóa file cũ nếu có
            if ($currentFileUrl) {
                $oldFilePath = str_replace('/storage/', 'public/', $currentFileUrl);
                if (Storage::exists($oldFilePath)) {
                    Storage::delete($oldFilePath);
                }
            }
            // Lưu file mới
            $filePath = $request->file($fileKey)->store($folder, $disk);
            return Storage::url($filePath);
        }

        // Giữ nguyên URL cũ hoặc URL từ input
        return $request->input($fileKey . '_url', $currentFileUrl);
    }

}