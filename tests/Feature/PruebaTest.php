<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;

class PruebaTest extends TestCase
{
    
    public function testPlayerRegisterSuccess()
  {
      $response = $this->json('POST', 'api/players', [
          'name' => 'Carlos',
          'email' => 'Carlos@gmail.com',
          'password' => 'Carlos123456',
      ]);
  
      $response->assertStatus(201);
      
      $this->assertCredentials([
          'name' => 'Carlos',
          'email' => 'Carlos@gmail.com',
          'password' => 'Carlos123456'
      ]);

        $user = User::where('email', 'Carlos@gmail.com')->first();
        $user->delete();
  }
    


}
