<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Game;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
 

    //TEST API 

    public function test_register_new_player(): void

    {   $this->withoutExceptionHandling();
        //new user
        $response = $this->post('api/register', [
            'name' => 'Test',
            'nickname' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        //si se registra status de correcto
        $response->assertStatus(200);
    }
    public function test_login_required_credentials(): void
    {   
        $response = $this->post('api/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        //eliminar user al finalizar el test
        User::where('email', 'test@test.com')->delete();
        
    }
    public function test_login_unregistered_credentials(): void
    {   
        $response = $this->post('api/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response->assertStatus(401);
        
    }
    public function test_update_user_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        //update user
        $response = $this->putJson("api/players/{$user->id}", [
            'name' => 'New Name',
            'nickname' => 'New Nickname'
        ]);
        $response->assertStatus(200);
        $user->delete();   
    }
    public function test_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->postJson('api/logout');
        $response->assertStatus(200);
        //verificamos que no exista token
        $this->assertNull($user->tokens()->latest()->first());
        $user->delete(); 
    }
    public function test_create_game(): void
    {
        $user = User::factory()->create();
        $user->assignRole('player'); 
        $this->actingAs($user, 'api');
        $response = $this->postJson("api/players/{$user->id}/games");
        $response->assertStatus(201);
    }
    public function test_player_cannot_create_games_for_other_players(): void
    {
        //generamos 2 jugadores
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user1->assignRole('player');
        $user2->assignRole('player');
        $this->actingAs($user1, 'api');
        $response = $this->postJson("api/players/{$user2->id}/games");
        //unautorizado para crear juegos
        $response->assertStatus(403);
    }
    public function test_player_can_view_their_games(): void
    {
        $user = User::factory()->create();
        $user->assignRole('player'); 
        $this->actingAs($user, 'api');
        //crear juego
        Game::factory()->count(5)->create(['user_id' => $user->id]);
        $response = $this->getJson("api/players/{$user->id}/games");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [ 
                'id',
                'user_id',
                'dice1',
                'dice2',
                'won',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJsonCount(5); 
    }
    public function test_player_cannot_view_games_of_other_players(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user1->assignRole('player');
        $user2->assignRole('player');
        $this->actingAs($user1, 'api');
        Game::factory()->count(3)->create(['user_id' => $user2->id]);
        $response = $this->getJson("api/players/{$user2->id}/games");
        $response->assertStatus(403);
    }


    public function test_player_can_delete_games(): void
    {
        $user = User::factory()->create();
        $user->assignRole('player'); 
        $this->actingAs($user, 'api');
        Game::factory()->count(2)->create(['user_id' => $user->id]);
        $response = $this->deleteJson("api/players/{$user->id}/games");
        $response->assertStatus(200);
        //corroborar que no existan los juegos
        $this->assertEquals(0, Game::where('user_id', $user->id)->count());
    }
    public function test_admin_cannot_delete_other_players_games(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole('player');

        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);
        $this->actingAs($admin, 'api');
        Game::factory()->count(6)->create(['user_id' => $user1->id]);
        //intento de borrar los juegos siendo admin
        $response = $this->deleteJson("api/players/{$user1->id}/games");
        //imposible de borrar
        $response->assertStatus(403);
    }

    public function test_all_users_win_percentage_accesibility_by_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin'); 
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/players');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'users' => [
                '*' => [
                    'user' => [
                        'id',
                        'name',
                        'nickname',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ],
                    'win_percentage'
                ]
            ]
        ]);
    }
    public function test_admin_can_access_ranking_users_with_win_percentage(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin'); 
        $this->actingAs($user, 'api');
        //usamos la ruta par acceder al ranking de jugadores con sus porcentajes
        $response = $this->getJson('/api/players/ranking');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'users' => [
                '*' => [
                    'user' => [
                        'id',
                        'name',
                        'nickname',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ],
                    'win_percentage'
                ]
            ]
        ]);
    }
    public function test_admin_can_acces_winners(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin'); 
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/players/ranking/winner');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'winners' => [
                '*' => [
                    'id',
                    'name',
                    'nickname',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ],
            'win_percentage'
        ]);
    }
    public function test_admin_can_acces_losers(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin'); 
        $this->actingAs($user, 'api');
        //usamos la ruta par acceder a los perdedores
        $response = $this->getJson('/api/players/ranking/loser');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'losers' => [
                '*' => [
                    'id',
                    'name',
                    'nickname',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ],
            'win_percentage'
        ]);
    }
    public function test_player_cannot_access_admin_functions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('player'); 
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/players'); 
        //acceso denegado
        $response->assertStatus(403);
    }
    public function test_admin_cannot_access_player_functions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin'); 
        $this->actingAs($user, 'api');
        $response = $this->postJson("api/players/{$user->id}/games");
        //acceso denegado
        $response->assertStatus(403);
    }

        //test para el administrador que puede ser interesante
   /* 
    public function test_admin_can_view_games_of_player(): void
{
    $player = User::factory()->create();
    $player->assignRole('player');
    //creo el admin
    $admin = User::factory()->create();
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($adminRole);
    $this->actingAs($admin, 'api');

    Game::factory()->count(6)->create(['user_id' => $player->id]);
    $response = $this->getJson("api/players/{$player->id}/games");
    //Administrador accede sin restricciones
    $response->assertStatus(200);
}
*/
}
