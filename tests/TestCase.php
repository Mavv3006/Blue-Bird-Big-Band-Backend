<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function accept_header(): array
    {
        return ['Accept' => 'application/json'];
    }

    protected function login_header(): array
    {
        User::factory()->create(['name' => 'test', 'password' => Hash::make('test')]);
        $content = $this
            ->post('api/auth/login', ['name' => "test", 'password' => "test"], $this->accept_header())
            ->baseResponse
            ->content();
        $token = json_decode($content);
        return ['Authorization' => 'Bearer ' . $token->access_token];
    }

    protected function auth_header(): array
    {
        return array_merge($this->accept_header(), $this->login_header());
    }
}
