<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Support\Facades\Auth;

class AuthTest extends TestCase
{

    /**
     * Login test
     *
     * @return array
     */
    public function testLoginTest()
    {
        $tokenAndLoginList = array();
        $authTestData = $this->authDataProvider();
        foreach ($authTestData as $auth) {
            $response = $this->json('POST', '/api/auth/login', ['login' => $auth[0], 'password' => $auth[1]]);
            if (json_decode($response->getContent())->status !== 'success' AND $auth[2]) {
                echo 'Login failed for login = ' . $auth[0];
                $this->assertEquals(true, false);
            }
            if (json_decode($response->getContent())->status === 'success') {
                $tokenAndLoginList[] = array("token" => json_decode($response->getContent())->token, "login" => $auth[0]);
            }
        }
        $this->assertEquals(true, true);
        return $tokenAndLoginList;
    }

    /**
     * Good tokens validation test
     *
     * @depends testLoginTest
     * @param $tokenAndLoginList array
     * @return void
     */
    public function testGoodTokenValidation($tokenAndLoginList)
    {
        foreach ($tokenAndLoginList as $tokenAndLogin) {
            $response = $this->withHeaders(['Authorization' => "Bearer " . $tokenAndLogin["token"]])->json('GET', '/api/auth/check_token');
            // echo $response->getContent(). PHP_EOL;
            if (json_decode($response->getContent())->status !== 'success') {
                echo 'Login failed for login ' . $tokenAndLogin["login"];
                $this->assertEquals(true, false);
            }
        }
        $this->assertEquals(true, true);
    }

    /**
     * Bad tokens validation test
     *
     * @return void
     * @throws \Exception
     */
    public function testBadTokenValidation()
    {
        for ($i = 0; $i < 80; $i++) {
            $response = $this->withHeaders(['Authorization' => "Bearer " . bin2hex(random_bytes(40))])->json('GET', '/api/auth/check_token');
            if (json_decode($response->getContent())->message !== "Unauthorized") {
                $this->assertEquals(true, false);
            }
        }
        $this->assertEquals(true, true);
    }

    /**
     * Refresh token validation test
     *
     * @depends testLoginTest
     * @param $tokenAndLoginList array
     * @return array
     */
    public function testRefreshToken($tokenAndLoginList)
    {
        $refreshedTokenAndLoginList = array();
        foreach ($tokenAndLoginList as $tokenAndLogin) {
            $response = $this->withHeaders(['Authorization' => "Bearer " . $tokenAndLogin["token"]])->json('GET', '/api/auth/refresh_token');
            $refreshedTokenAndLoginList[] = array("token" => json_decode($response->getContent())->token, "login" => $tokenAndLogin["login"]);

        }

        foreach ($refreshedTokenAndLoginList as $refreshedTokenAndLogin){
            $response = $this->withHeaders(['Authorization' => "Bearer " . $refreshedTokenAndLogin["token"]])->json('GET', '/api/auth/check_token');
            if (json_decode($response->getContent())->status !== 'success') {
                echo 'Refresh Token failed for login ' . $refreshedTokenAndLogin["login"];
                $this->assertEquals(true, false);
            }
        }
        $this->assertEquals(true, true);

        return $refreshedTokenAndLoginList;
    }

    /**
     * Logout user test
     *
     * @depends testRefreshToken
     * @param $tokenAndLoginList array
     * @return void
     */
    public function testLogoutUser($tokenAndLoginList)
    {
        foreach ($tokenAndLoginList as $tokenAndLogin) {
            $response = $this->withHeaders(['Authorization' => "Bearer ".$tokenAndLogin["token"]])->json('GET', '/api/auth/logout');

            $jsonResponse = json_decode($response->getContent());

            var_dump($jsonResponse);
            if (($jsonResponse->status !== "success") or ($jsonResponse->message !== "logged out successfully")) {
                $this->assertEquals(true, false);
            }
            $this->assertEquals(true, true);
        }
    }

    public function authDataProvider()
    {
        return array(
            array(env('TEST_ROOT_LOGIN'), env('TEST_ROOT_PASSWORD'), true),
            array('hacker', 'superhack', false),
        );
    }
}
