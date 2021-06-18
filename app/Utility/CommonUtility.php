<?php
namespace App\Utility;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CommonUtility {

    const SUCCESS = 'Success';
    const FAILURE = 'Failure';
    const ERROR = 'Error';
    const SUCCESS_CODE = 200;
    const ERROR_CODE = 102;
    const FAILURE_CODE = 103;
    const INVALID_JSON = 400;
    const NULL = 0;
    const ACTIVE = 1;
    const CAPACITY_MAX = 100;
    const DEFAULT_RATING = 1;

    public static function renderJson($responseCode, $message = false, $data = array()) {
        $response['message'] = (!empty($message)) ? $message : (($responseCode == CommonUtility::SUCCESS_CODE) ? trans('messages.success') : trans('messages.error.exception'));
        $response['statusCode'] = $responseCode;
        $response['status'] = CommonUtility::ERROR;
        if($responseCode == CommonUtility::SUCCESS_CODE) {
            $response['status'] = CommonUtility::SUCCESS;
        } elseif ($responseCode == CommonUtility::FAILURE_CODE) {
            $response['status'] = CommonUtility::FAILURE;
        }
        $response['result'] = $data;
        if (empty($response['result'])) {
            $response['result'] = (object)$response['result'];
        }
        
        return Response()->json($response,Response::HTTP_OK)->header('Content-Type', "application/json");
    }

    // protected static function convertToCamelCase($array) {
    //     // dd($array);
    //     $convertedArray = [];
    //     foreach ($array as $oldKey => $value) {
    //         if (is_array($value)) {
    //             $value = self::convertToCamelCase($value);
    //         } else if (is_object($value)) {
    //             if ($value instanceof Model || method_exists($value, 'toArray')) {
    //                 $value = $value->toArray();
    //             } else {
    //                 $value = (array) $value;
    //             }
    //             $value = self::convertToCamelCase($value);
    //         }
    //         if($oldKey === strtoupper($oldKey)) {
    //             $convertedArray[$oldKey] = $value;
    //         } else {
    //             $convertedArray[camel_case($oldKey)] = $value;
    //         }
    //     }

    //     return $convertedArray;
    // }
    public static function logException($method, $file, $line, $message) {
        Log::error(['method' => $method, 'error' => ['file' => $file, 'line' => $line, 'message' => $message], 'created_at' => date("Y-m-d H:i:s")]);
    }
}