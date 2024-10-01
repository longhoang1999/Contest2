<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->insert([
            [
                'type' => '1',
                'question_text' => 'What is Laravel?',
                'image_url' => null,
                'video_url' => null,
                'audio_url' => null,
                'difficulty' => 1,
                'focus_level' => 2,
                'average_time' => 30,
                'correct_percentage' => 75.0,
                'note' => 'Basic question about Laravel framework.',
                'is_active' => '1', // Chuỗi '1'
                'is_demo' => '0', // Chuỗi '0'
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => '0',
                'question_text' => 'What is PHP?',
                'image_url' => null,
                'video_url' => 'https://example.com/php_intro.mp4',
                'audio_url' => null,
                'difficulty' => 2,
                'focus_level' => 3,
                'average_time' => 40,
                'correct_percentage' => 65.0,
                'note' => 'Intermediate question about PHP.',
                'is_active' => '1', // Chuỗi '1'
                'is_demo' => '1', // Chuỗi '1'
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => '2',
                'question_text' => 'Explain RESTful API.',
                'image_url' => null,
                'video_url' => null,
                'audio_url' => 'https://example.com/restful_intro.mp3',
                'difficulty' => 3,
                'focus_level' => 4,
                'average_time' => 50,
                'correct_percentage' => 80.0,
                'note' => 'Advanced question about RESTful API.',
                'is_active' => '1', // Chuỗi '1'
                'is_demo' => '0', // Chuỗi '0'
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
