<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\GetJwtToken as GetJwtToken;

class UsersContactValidationTest extends TestCase
{
    use GetJwtToken;

    private $login = 'test_login_contact';
    private $password = 'test_pass';

    /**
     * Test of creating user
     *
     * @return void
     */
    public function testRegisterUser()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('POST', '/api/users/add', [
            'login' => $this->login,
            'password' => $this->password,
            'password_confirmation' => $this->password,
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
     * @depends testRegisterUser
     */
    public function testLoginJustCreatedUser()
    {
        $response = $this->json('POST', '/api/auth/login', ['login' => $this->login, 'password' => $this->password]);
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "message" => "please add your contacts and validate it"
        ]);
        return json_decode($response->getContent())->token;
    }

    /**
     * @depends testLoginJustCreatedUser
     */
    public function testAddContacts(string $token)
    {
        $response = $this->withHeaders(['Authorization' => "Bearer " . $token])->json('POST', '/api/auth/add_contacts',
            ['phone' => '00000000000']);

        echo $response->getContent().PHP_EOL;

        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }

    public function testCheckUserIsValidated(){
        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('GET', '/api/users/'. $this->login);
      //  echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJsonFragment([
            "validated" => 1
        ]);
    }

    /**
     * @depends testRegisterUser
     */
    public function testDeleteJustCreatedUser()
    {

        $response = $this->withHeaders(['Authorization' => "Bearer " . $this->getJwtToken(env('TEST_ROOT_LOGIN'))])->json('DELETE', '/api/users/delete/' . $this->login);
        echo $response->getContent() . PHP_EOL;
        $response->assertStatus(200)->assertJson([
            "status" => "success"
        ]);
    }
}
