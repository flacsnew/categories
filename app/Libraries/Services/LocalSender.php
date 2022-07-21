<?php
/**
 * Created by PhpStorm.
 * User: vsaprykin
 * Date: 11.06.2019
 * Time: 10:59
 */

namespace App\Library\Services;

use App\Library\Services\Contracts\SmsSenderServiceInterface;
use Illuminate\Support\Facades\Auth;

class LocalSender implements SmsSenderServiceInterface
{
    private $client;

    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $phone string
     * @param $message string
     * @return void
     * @throws \Exception
     */
    public function sendSMS($phone, $message)
    {
        $user = Auth::user();
        $user->validation_code = null;
        $user->validated = true;
        $user->validated_at = now();
        $user->save();
    }
}
