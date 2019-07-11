<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('errorResponse')) {

    /**
     * 錯誤回傳
     *
     * @return json
     */
    function errorResponse($msg, $e = null)
    {
    	if (!is_null($e)) {
    		\Log::error($e);
    	}

        return new JsonResponse([
            'result' => 'error',
            'msg' => $msg,
        ]);
    }
}