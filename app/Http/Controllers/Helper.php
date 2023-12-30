<?php

namespace App\Http\Controllers;

trait Helper
{
    public function basic_response($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ])->setStatusCode($code);
    }
}
