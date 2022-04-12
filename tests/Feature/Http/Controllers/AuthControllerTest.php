<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $login_route = 'api/auth/login';

    public function test_login_twice_same_user()
    {
        User::factory()->create(['name' => 'test', 'password' => Hash::make('test')]);
        $this->assertDatabaseCount('users', 1);

        $response_user_1 = $this->post($this->login_route, ['name' => 'test', 'password' => 'test']);
        $response_user_2 = $this->post($this->login_route, ['name' => 'test', 'password' => 'test']);

        var_dump('User 1', $response_user_1->baseResponse->content());
        var_dump('User 2', $response_user_2->baseResponse->content());

        $response_user_1->assertStatus(200);
        $response_user_2->assertStatus(200);
    }

    public function test_required_data()
    {
        $this
            ->post($this->login_route)
            ->assertStatus(400)
            ->assertJsonStructure(['error', 'message']);
    }

    public function test_login_with_wrong_credentials()
    {
        User::factory()->create(['name' => 'test', 'password' => Hash::make('test')]);

        $this
            ->post($this->login_route, ['name' => 'test', 'password' => 'bla bla'])
            ->assertStatus(401)
            ->assertJsonStructure(['error']);
    }

    public function test_login_successful()
    {
        User::factory()->create(['name' => 'test', 'password' => Hash::make('test')]);

        $this
            ->post($this->login_route, ['name' => "test", 'password' => "test"])
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }

    public function test_login_validation_error_password()
    {
        User::factory()->create(['name' => 'test', 'password' => Hash::make('test')]);

        $this
            ->post($this->login_route, ['name' => "test"])
            ->assertStatus(400)
            ->assertJsonStructure(['error', 'message']);
    }

    public function test_login_validation_error_name()
    {
        User::factory()->create(['name' => 'test', 'password' => Hash::make('test')]);

        $this
            ->post($this->login_route, ["password" => "test"])
            ->assertStatus(400)
            ->assertJsonStructure(['error', 'message']);
    }
}
