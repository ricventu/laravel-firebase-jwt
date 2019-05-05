<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRegister()
    {
        $user = factory(\App\User::class)->make();
        
        $response = $this->json('POST', route('api.auth.register'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'token' => true
        ]);
    }

    public function testLogin()
    {
        $user = factory(\App\User::class)->create(['password' => bcrypt('secret')]);
        
        $response = $this->json('POST', route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'token' => true
        ]);
    }

    public function testNoAccess()
    {
        $response = $this->json('GET', route('api.user'));

//        fwrite(STDERR, print_r($response->getContent(), TRUE));
        $response->assertStatus(401);
    }

    public function testAccess()
    {
        $user = factory(\App\User::class)->create(['password' => bcrypt('secret')]);

        $response = $this->json('POST', route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $token = json_decode($response->getContent(), true)['token'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token
        ])->json('GET', route('api.user'));

        //fwrite(STDERR, print_r($response->getContent(), TRUE));
        $response->assertStatus(200);
    }
}
