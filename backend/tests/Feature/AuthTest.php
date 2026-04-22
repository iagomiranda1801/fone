<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_com_credenciais_validas_retorna_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'senha123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ]);
    }

    public function test_login_com_senha_errada_retorna_401(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correta')]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'errada',
        ]);

        $response->assertUnauthorized();
    }

    public function test_login_com_email_inexistente_retorna_401(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'naoexiste@test.com',
            'password' => 'qualquer',
        ]);

        $response->assertUnauthorized();
    }

    public function test_rota_protegida_sem_token_retorna_401(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }

    public function test_me_retorna_usuario_autenticado(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('email', $user->email);
    }

    public function test_logout_invalida_o_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Verifica que existe 1 token antes do logout
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        // Após logout, o token deve ter sido removido do banco
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
