<?php

namespace App\Http\Controllers\Admin\Questions;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::all();
        return view('admin/Questions/questions', compact('questions'));
    }

}