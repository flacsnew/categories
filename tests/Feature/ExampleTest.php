<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
class ExampleTest extends TestCase
{
   // use AttachJwtToken;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->json('POST', '/api/auth/login', ['login' => 'root', 'password' => 'superroot']);
       // echo $response->getContent();
        $response->assertStatus(200);
    }

    public function testNew()
    {
        $user = User::whereLogin('root')->take(1)->get()[0];
        $token = Auth::guard()->login($user);
        $response =$this->withHeaders(['X-Authorization' => "Bearer $token"])->json('GET', '/api/users');
        echo $response->getContent();

    }
}
