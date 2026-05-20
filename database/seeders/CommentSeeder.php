<?php
// database/seeders/CommentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskComment;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah model TaskComment ada, jika tidak skip
        if (!class_exists('App\Models\TaskComment')) {
            $this->command->info('Model TaskComment tidak ditemukan, skip CommentSeeder');
            return;
        }

        $tasks = Task::all();
        $users = User::all();

        if ($tasks->isEmpty() || $users->isEmpty()) {
            return;
        }

        $comments = [
            'Task ini sudah saya kerjakan',
            'Mohon bantuannya untuk task ini',
            'Ada kendala teknis',
            'Sudah saya revisi sesuai feedback',
            'Butuh update dari tim lain',
            'Saya sedang menunggu approval',
            'Dokumentasi sudah saya upload',
            'Task ini butuh diskusi lebih lanjut',
            'Sudah selesai, tolong dicek',
            'Ada perubahan requirement'
        ];

        foreach ($tasks as $task) {
            // Random jumlah komentar 0-5
            $numComments = rand(0, 5);
            
            for ($i = 0; $i < $numComments; $i++) {
                $commentUser = $users->random();
                
                TaskComment::create([
                    'content' => $comments[array_rand($comments)],
                    'task_id' => $task->id,
                    'user_id' => $commentUser->id,
                    'created_at' => Carbon::now()->subHours(rand(1, 72)),
                    'updated_at' => Carbon::now()->subHours(rand(1, 72))
                ]);
            }
        }

        $this->command->info('CommentSeeder berhasil dijalankan!');
    }
}