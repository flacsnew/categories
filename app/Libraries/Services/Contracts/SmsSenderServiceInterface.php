<?php
/**
 * Created by PhpStorm.
 * User: vsaprykin
 * Date: 22.05.2019
 * Time: 16:27
 */

namespace App\Library\Services\Contracts;

Interface SmsSenderServiceInterface
{
    /**
     * @param $phone string
     * @param $message string
     * @return void
     * @throws \Exception
     */
    public function sendSMS($phone, $message);
}