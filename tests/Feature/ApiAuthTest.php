<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreaDatosDePrueba;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase, CreaDatosDePrueba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    public function test_login_retorna_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
            'device_name' => 'test',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_falla_con_credenciales_invalidas(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
            'device_name' => 'test',
        ]);

        $response->assertStatus(401);
    }

    public function test_me_retorna_usuario_autenticado(): void
    {
        $token = $this->adminUser->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('email', 'admin@test.com');
    }

    public function test_me_falla_sin_token(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    public function test_logout_revoca_token(): void
    {
        $token = $this->adminUser->createToken('test')->plainTextToken;

        $this->postJson('/api/v1/auth/logout', [], [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(200);

        // Refresh app to clear Sanctum's token cache
        $this->refreshApplication();

        $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertStatus(401);
    }
}
