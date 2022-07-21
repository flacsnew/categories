<?php
/**
 * Created by PhpStorm.
 * User: vsaprykin
 * Date: 23.05.2019
 * Time: 16:39
 */

namespace Tests\Concerns;

use App\User;
use Illuminate\Support\Facades\Auth;

trait GetJwtToken
{

    /**
     * @param $login string
     * @return string
     */
    protected function getJwtToken($login)
    {
        $user = User::whereLogin($login)->take(1)->get()[0];
        return Auth::guard()->login($user);
    }

}