<?php
/**
 * Copyright 2018-2019 Appster Information Pvt. Ltd.
 * All rights reserved.
 * File: PushNotificationUtility.php
 * CodeLibrary/Project: BAZAR
 * @author Harshit Gaur
 * CreatedOn: 10/08/2018
 */
namespace App\Utility;
use Illuminate\Support\Facades\Log;
use App\Model\DeviceTokens;
class PushNotificationUtility {
    /**
     * Method used to Send Push Notification
     * @param Array $ntf_data, Array $device_info, Integer $badge, Array $additional_notification_data
     */
    public static function sendPushNotification($ntf_data, $device_info, $badge, $additional_notification_data = array()) {
        //Push Payload for Android
        // $push_payload = [
        //     'title' => $ntf_data['title'],
        //     'sender_user_id' => $ntf_data['sender_user_id'],
        //     'receiver_user_id' => $ntf_data['receiver_user_id'],
        //     'type' => $ntf_data['type'],
        //     'id' => $ntf_data['push_notification_id'],
        //     'sourceId' => (int)$ntf_data['source_id'],
        //     'additional_source_id' => $ntf_data['additional_source_id'] == NULL ? $ntf_data['additional_source_id'] : (int)$ntf_data['additional_source_id'],
        //     'badge'=> $badge
        // ];
        $push_payload = [
            'title' => $ntf_data['title'],
            'badge'=> $badge,
            'body'=> $ntf_data['message'],
            'message'=> $ntf_data['message'],
            'id'=> $ntf_data['push_notification_id'],
            'type'=> $ntf_data['type'],
            'sourceId'=> (int)$ntf_data['source_id'],
        ];
        //Used for Additional Push Payload Data which we do not want to add into the DB.
        if (!empty($additional_notification_data)) {
            $push_payload = array_merge($push_payload, $additional_notification_data);
        }
        //Send Push Notification
        if ($device_info['device_token']) {
            if ($device_info['device_type'] == DeviceTokens::DEVICE_TYPE_ANDROID) {
                self::sendPushAndroidNew($device_info['device_token'], $ntf_data['message'], $push_payload);
            } else if ($device_info['device_type'] == DeviceTokens::DEVICE_TYPE_IOS) {
                $params['badge'] = $badge;
                $params['data'] = $push_payload;
                self::sendPushIOS($device_info['device_token'], $ntf_data['message'], $params);
            }
        }
    }
    /**
     * Method used to Send Push Notification --- Apple iOS
     * @param Alpha-Numeric $device_token, String $message, Array $params
     * @return boolean
     */
    public static function sendPushIOS($device_token, $message, $params = false) {
        return;
        if (!$device_token || strlen($device_token) < 40) {
            return;
        }
        // $device_token = "902493724d5df995701a5fcef8f2730bb6b2809dd4bac17b389cd4a1ced55855";
        $kid      = "5V958P3RBJ";
        $teamId   = "3KS5PMAA98";
        $app_bundle_id = "com.copebros.bazar-development";
        // $base_url = "https://api.development.push.apple.com";
        $base_url = "https://api.push.apple.com";
        $header = ["alg" => "ES256", "kid" => $kid];
        $header = base64_encode(json_encode($header));
        $claim = ["iss" => $teamId, "iat" => time()];
        $claim = base64_encode(json_encode($claim));
        $token = $header.".".$claim;
        // key in same folder as the script
        // $filename = "AuthKey_5V958P3RBJ.p8";
        if (env('PUSH_ENV') == 'local') {
            $config = config('push_notification.apple.sandbox');
        } else {
            $config = config('push_notification.apple.production');
        }
        $filename = $config['pem_file'];
        $pkey     = openssl_pkey_get_private("file://{$filename}");
        $signature;
        openssl_sign($token, $signature, $pkey, 'sha256');
        $sign = base64_encode($signature);
        $jws = $token.".".$sign;
        $body['aps'] = array(
            'alert' => array($message),
            'badge' => 2,
            'sound' => 'oven.caf',
        );
        $message = json_encode($body);
        // open connection
        $curl = curl_init();
        self::sendHTTP2Push($curl, $base_url, $app_bundle_id, $message, $device_token, $jws);
        // Log::info("deviceToken = $device_token");
        // Log::info($payload);
    }
    public static function sendHTTP2Push($curl, $base_url, $app_bundle_id, $message, $device_token, $jws) {
            $url = "{$base_url}/3/device/{$device_token}";
            // headers
            $headers = array(
                "apns-topic: {$app_bundle_id}",
                'Authorization: bearer ' . $jws
            );
            // other curl options
            curl_setopt_array($curl, array(
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_URL => $url,
                CURLOPT_PORT => 443,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $message,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_HEADER => 1
            ));
            // go...
            $result = curl_exec($curl);
            if ($result === FALSE) {
                throw new Exception("Curl failed: " .  curl_error($curl));
            }
            print_r($result."\n");
            // get response
            $status = curl_getinfo($curl);
            return $status;
        }
    /**
     * Method used to Send Push Notification --- Android
     * @param Alpha-Numeric $device_token, String $message, Array $params
     * @return boolean
     */
    public static function sendPushAndroid($device_token, $message, $params = false) {
        if (!$device_token) {
            return;
        }
        $config = config('push_notification.android');
        $notification = ['text' => $message];
        if ($params) {
            $data = $params;
        } else {
            $data = [];
        }
        $data['message'] = $message;
        $data['title'] = $params['title'];
        $fields = array
            (
            'data' => $data,
            //'notification' => $notification,
            'to' => $device_token
        );
        // dd($config['server_key']);
        $headers = array
            (
            'Authorization: key=AAAAdf5Ht8o:APA91bHJ96o2OM0gg5M7VxWctNz2ys6V92o9jG1Zuv7a2OSYXlLyV4t4E4ycsH-Q-Ilx0gww5xxucHaA48Dsd01SV2b_OB8nj8Sk6SZyfTotYZ3G45bxh7EMys661uOxCyOzbCspQUKY',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        Log::info(json_encode($fields));
        var_dump($device_token);
        var_dump($result);
    }
    /**
     * Method used to Send Push Notification --- Android
     * @param Alpha-Numeric $device_token, String $message, Array $params
     * @return boolean
     */
    public static function sendPushAndroidNew($device_token, $message, $params = false) {
        if (!$device_token) {
            return;
        }
        $config = config('push_notification.android');
        $notification = ['body' => $message,'title' => $params['title']];
        if ($params) {
            $data = $params;
        } else {
            $data = [];
        }
        $data['body'] = $message;
        $data['title'] = $params['title'];
        $data['message'] = $params['message'];
        $data['id'] = $params['id'];
        $data['type'] = $params['type'];
        $data['badge'] = $params['badge'];
        $data['sourceId'] = $params['sourceId'];
        $fields = array
            (
            'data' => $data,
            'notification' => $notification,
            'to' => $device_token
        );
        // print_r($fields);
        // die('a');
        // dd($config['server_key']);
        $headers = array
            (
            'Authorization: key=AAAArX5O-is:APA91bHJXbsI_SKWkHnEqvRrDMhbuezIdirxvqFLS1CGiN07ayqisPGGpC-rpzuZlQdu4OnYl2GbHbWJBawafZjVugux-sMg6iFIZZICSH9bBEI7ea_SRhnpc8ZpTsokpsUopYLdMQxw',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        Log::info(json_encode($fields));
    }
}
