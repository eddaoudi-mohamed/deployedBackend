<?php

namespace App\Traits;



trait GeneraleTrait
{


    public function errorResponse($errorsMessage, $status)
    {
        return response()->json($errorsMessage, $status);
    }

    public function successfulResponse($data)
    {
        return response()->json($data, 200);
    }
}
