<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Tests\Concerns\GetJwtToken as GetJwtToken;


class UserControllerTest extends TestCase
{

    use GetJwtToken;

    /**
     * Test of getting user list
     *
     * @return void
     */
    public function testGetAllUserList(){
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('GET', '/api/users');
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }
    /**
     * Test of getting user
     *
     * @return void
     */
    public function testGetUser(){
        $login = 'root';
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('GET', '/api/users/'. $login);
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }

    /**
     * Test of creating user
     *
     * @return void
     */
    public function testRegisterUser()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('POST', '/api/users/add', [
            'login' => 'test_login',
            'password' => 'test_pass',
            'password_confirmation' => 'test_pass',
            'enabled' => true,
            'role' => 'user',
            'note' => 'test user'
        ]);
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }

    /**
     * Test of update user
     *
     * @return void
     */
    public function testUpdateUser(){
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('PUT', '/api/users/update', [
            'login' => 'test_login',
            'role' => '2',
            'enabled' => false,
            'note' => 'some new note'
        ]);
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }

    /**
     * Test of deleting user
     *
     * @return void
     */
    public function testDeleteUser()
    {
        $login = 'test_login';
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('DELETE', '/api/users/delete/' . $login);
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }
}
