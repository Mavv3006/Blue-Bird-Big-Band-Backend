<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RegisterUserSeeder extends Seeder
{
    private readonly string $username;
    private readonly string $password;

    public function __construct()
    {
        $this->password = "XX";
        $this->username = "XX";
    }

    /**
     * Run the database seeds.
     *
     * Adds a user with the given credentials to the database.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()->create(['name' => $this->username, "password" => Hash::make($this->password)]);
    }
}
