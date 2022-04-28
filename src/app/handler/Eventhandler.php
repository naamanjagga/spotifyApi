<?php

declare(strict_types=1);


namespace App\Handler;


use Phalcon\Mvc\Controller;
use Tokens;

class Eventhandler extends Controller
{
    public function generateToken()
    {
        $token = Tokens::findFirstByuser_id(1);
        $refresh_token = $token->refresh_token;
        $client_id = 'c4f52e323fc14d3fac96be4451ebb8ac';
        $client_secret = 'cc9d097a717a44df92b80b8721c50dcf';
        $headers  = [
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),

        ];
        $param  = [
            'grant_type' => "refresh_token",
            "refresh_token" => $refresh_token,

        ];
        $url      = 'https://accounts.spotify.com/api/token';
        $options  = [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_POST           => TRUE,
                CURLOPT_POSTFIELDS     => http_build_query( $param ),
                CURLOPT_HTTPHEADER     => $headers,

        ];
        $credentials = $this->callSpotifyApi($options);

        $this->storeToken($credentials->access_token);
    }
    public function callSpotifyApi($options)
    {
        $curl  = curl_init();
        curl_setopt_array($curl, $options);
        $json  = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($error) {
            return [
                'error'   => TRUE,
                'message' => $error
            ];
        }
        $data  = json_decode($json);
        if (is_null($data)) {
            return [
                'error'   => TRUE,
                'message' => json_last_error_msg()
            ];
        }
        return $data;
    }
    public function storeToken($token){
        $token_update = Tokens::findFirstBytoken_id(1);
        $token_update->token = $token; 
        $token_update->save();
    }
}
