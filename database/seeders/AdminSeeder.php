<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SecurityQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default admin user
     */
    public function run(): void
    {
        // Create default admin if doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'bdm@gmail.com'],
            [
                'name' => 'BDM Admin',
                'email' => 'bdm@gmail.com',
                'password' => Hash::make('bdm88889999'),
                'role' => 'admin',
                'created_by' => null, // Self-created
            ]
        );

        // Create security questions for this admin if they don't exist
        SecurityQuestion::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'user_id' => $admin->id,
                'question_1' => 'What is the name of your first business?',
                'answer_1' => 'bdm', // Will be hashed by the model
                'question_2' => 'What is your favorite number?',
                'answer_2' => '88889999', // Will be hashed by the model
            ]
        );

        $this->command->info('Default admin user created/verified: bdm@gmail.com');
    }
}
