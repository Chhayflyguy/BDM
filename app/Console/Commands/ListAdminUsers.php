<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListAdminUsers extends Command
{
    protected $signature = 'user:list-admins';
    protected $description = 'List all admin users';

    public function handle()
    {
        $admins = User::where('is_admin', true)
            ->select('id', 'name', 'email', 'is_admin', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($admins->isEmpty()) {
            $this->info('No admin users found.');
            return 0;
        }

        $this->info("Found {$admins->count()} admin user(s):\n");

        $headers = ['ID', 'Name', 'Email', 'Created At'];
        $rows = [];

        foreach ($admins as $admin) {
            $rows[] = [
                $admin->id,
                $admin->name,
                $admin->email,
                $admin->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table($headers, $rows);

        return 0;
    }
}
