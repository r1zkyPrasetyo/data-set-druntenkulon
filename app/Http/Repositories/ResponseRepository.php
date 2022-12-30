<?php

namespace App\Http\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * ResponseRepository
 * 
 * @author    Andy Primawan <andy@primawan.com>
 * @author    Rizky Prasetyo <rizkyprasetyo185@gmail.com>
 * @copyright 2021 Yayasan Bakti
 */
class ResponseRepository
{
    /**
     * ResponseError
     * 
     * Returns the errors data if there is any error
     *
     * @param object $errors
     * @return Response
     */
    public static function ResponseError($errors, $message = 'Data is invalid', $status_code = JsonResponse::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ], $status_code);
    }


    /**
     * ResponseNotFound
     * 
     * Returns the ResponseNotFound data if there is any ResponseNotFound
     *
     * @param object $ResponseNotFound
     * @return Response
     */
    public static function ResponseNotFound($errors, $message = 'Data is notfound', $status_code = JsonResponse::HTTP_NOT_FOUND )
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ], $status_code);
    }

    /**
     * ResponseSuccess
     * 
     * Returns the success data and message if there is any error
     *
     * @param object $data
     * @param string $message
     * @param integer $status_code
     * @return Response
     */
    public static function ResponseSuccess($data, $message = "Successfull", $status_code = JsonResponse::HTTP_OK)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'errors' => null,
            'data' => $data,
        ], $status_code);
    }
}
