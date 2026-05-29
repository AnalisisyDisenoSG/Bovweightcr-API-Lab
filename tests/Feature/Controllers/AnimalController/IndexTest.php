<?php

namespace Tests\Feature\Controllers\AnimalController;

use App\Models\User;
use Database\Seeders\TipoUsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TipoUsuarioSeeder::class);
    }

    public function test_usuario_autenticado_puede_listar_animales(): void
    {
        $usuario = User::factory()->ganadero()->create();

        $response = $this->actingAs($usuario)
            ->getJson('/api/ganado');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    public function test_usuario_no_autenticado_no_puede_listar_animales(): void
    {
        $this->getJson('/api/ganado')->assertStatus(401);
    }
}
