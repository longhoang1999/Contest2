<?php

namespace App\Http\Controllers\Admin\Answers;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
    public function getAnswers($questionId)
    {
        $answers = Answer::where('question_id', $questionId)->get();
        return response()->json($answers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answer_text' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:10240',
            'audio' => 'nullable|file|mimes:mp3|max:5120',
            'image_url' => 'nullable|string|max:255',
            'video_url' => 'nullable|string|max:255',
            'audio_url' => 'nullable|string|max:255',
            'is_correct' => 'required|in:0,1,2',
            'correct_answer' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $answer = new Answer();
        $answer->question_id = $request->question_id;
        $answer->answer_text = $request->answer_text;
        $answer->is_correct = $request->is_correct;
        $answer->correct_answer = $request->correct_answer;

        $answer->image_url = $this->handleFileUpdate($request, 'image', null, 'images', 'public');
        $answer->video_url = $this->handleFileUpdate($request, 'video', null, 'videos', 'public');
        $answer->audio_url = $this->handleFileUpdate($request, 'audio', null, 'audios', 'public');

        $answer->save();

        // Trả về phản hồi thành công
        return response()->json(['success' => true, 'answer' => $answer]);
    }

    public function edit($id)
    {
        $answer = Answer::findOrFail($id);
        return response()->json($answer);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'answer_text' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:10240',
            'audio' => 'nullable|file|mimes:mp3|max:5120',
            'image_url' => 'nullable|string|max:255',
            'video_url' => 'nullable|string|max:255',
            'audio_url' => 'nullable|string|max:255',
            'is_correct' => 'required|in:0,1,2',
            'correct_answer' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm câu trả lời theo ID
        $answer = Answer::findOrFail($id);

        $answer->answer_text = $request->answer_text;
        $answer->is_correct = $request->is_correct;
        $answer->correct_answer = $request->correct_answer;

        $answer->image_url = $this->handleFileUpdate($request, 'image', $answer->image_url, 'images', 'public');
        $answer->video_url = $this->handleFileUpdate($request, 'video', $answer->video_url, 'videos', 'public');
        $answer->audio_url = $this->handleFileUpdate($request, 'audio', $answer->audio_url, 'audios', 'public');

        // Lưu thay đổi
        $answer->save();

        return response()->json(['success' => 'Cập nhật thành công!', 'answer' => $answer]);

    }

    public function destroy($id)
    {
        $answer = Answer::findOrFail($id);
        $answer->delete();

        return response()->json(['success' => true]);
    }

    public function toggleIsCorrect(Request $request, $id)
    {
        $request->validate([
            'is_correct' => 'required|integer|in:0,1',
        ]);

        $answer = Answer::with(['question'])->where('id', $id)->first();
        if ($request->is_correct == '1') {
            if ($answer->question->type == '2') {
                $answer->is_correct = '2';
            } else {
                $answer->is_correct = '1';
            }
        } else {
            $answer->is_correct = '0';
        }
        $answer->save(); // Lưu thay đổi

        return response()->json(['success' => true, 'answer' => $answer]);
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
