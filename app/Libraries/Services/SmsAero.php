<?php
/**
 * Created by PhpStorm.
 * User: vsaprykin
 * Date: 22.05.2019
 * Time: 15:21
 */

namespace App\Library\Services;

use App\Library\Services\Contracts\SmsSenderServiceInterface;

class SmsAero implements SmsSenderServiceInterface
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
        try {
            $params = [
                'query' => [
                    'number' => $phone,
                    'text' => urlencode($message),
                    'sign' => env('SMS_AERO_SIGN', 'SUPER_COMPANY'),
                    'channel' => env('SMS_AERO_CHANNEL', 'SUPER_CHANNEL')
                ],
                'auth' => [
                    env('SMS_AERO_EMAIL', 'email'),
                    env('SMS_AERO_KEY', 'key')
                ]
            ];
            $response = $this->client->request('GET', env('SMS_AERO_URL', 'https://sms.com'), $params);
            if (!(json_decode((string)$response->getBody())->success)) {
                throw new \Exception('SMS message service does not available1');
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \Exception('SMS message service does not available2');
        }
    }
}
