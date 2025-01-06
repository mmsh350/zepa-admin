<?php

namespace App\Helpers;

class noncestrHelper
{
    public static function generateNonceStr($length = 32)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $nonceStr = '';

        // Generate random characters
        for ($i = 0; $i < $length; $i++) {
            $nonceStr .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $nonceStr;
    }
}
