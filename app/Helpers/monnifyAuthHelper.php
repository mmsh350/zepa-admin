<?php

namespace App\Helpers;

class monnifyAuthHelper
{
    public static function auth()
    {

        $AccessKey = env('MONNIFYAPI').':'.env('MONNIFYSECRET');
        $ApiKey = base64_encode($AccessKey);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => env('BASE_URL').'/v1/auth/login/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic {$ApiKey}",
            ],
        ]);

        $json = curl_exec($ch);
        $result = json_decode($json);
        curl_close($ch);

        $accessToken = $result->responseBody->accessToken;

        return $accessToken;

    }
}
