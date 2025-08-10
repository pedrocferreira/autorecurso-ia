<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error('User already exists!');
            return 1;
        }

        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'credits' => 999,
        ]);

        $this->info('Admin user created successfully!');
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");

        return 0;
    }
}
