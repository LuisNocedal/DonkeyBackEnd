<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NotificationModel extends CI_Model {

  public function sendAndroid($title, $message, $token) {

      $msg = array
          (
          'message' => $message,
          'title' => $title,
          'subtitle' => '',
          'tickerText' => '',
          'vibrate' => 1,
          'sound' => 1
      );

      $fields = array(
          'registration_ids' => array(
              $token

          ),
          'data' => $msg
      );
      $fields = json_encode($fields);

      $headers = array(
          'Authorization: key=' . 'AIzaSyBnS0W9nVdxfHgNN_x3oJ5LiQ0YcwXz7BM',
          'Content-Type: application/json'
      );

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

      $result = curl_exec($ch);
      curl_close($ch);

      return $result;
  }

}
