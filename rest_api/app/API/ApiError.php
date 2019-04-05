<?php

namespace App\API;


class ApiError 
{
    public static function erroMessage($message, $code)
    {
        return [
            'msg' => $message,
            'code' => $code
        ];
    }
}