<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {

        $user = User::factory()->create([
            'email' => 'pegawai@example.com',
            'password' => bcrypt('pegawai123'),
            'role' => 'pegawai',
        ]);

        $response = $this->post(route('login.process'), [
            'email' => 'pegawai@example.com',
            'password' => 'pegawai123',
        ]);

        $response->assertRedirect('/pegawai/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'pegawai@example.com',
            'password' => bcrypt('pegawai123'),
            'role' => 'pegawai',
        ]);

        $response = $this->from('/login')->post(route('login.process'), [
            'email' => 'pegawai@example.com',
            'password' => 'salahpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
