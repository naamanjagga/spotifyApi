<?php

declare(strict_types=1);

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {
    }
    public function loginAction()
    {
        $client_id = 'c4f52e323fc14d3fac96be4451ebb8ac';
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = Users::findFirstByemail($email);
        if ($user != null) {
            if ($user->password == $password) {
                $id = $user->user_id;
                $this->session->set("id", $id);
                if ($user->spotify_id == 'on') {
                    $this->response->redirect('index');
                } else {
                    $callback = "http://localhost:8080/login/getAccessCode";
                    $scopes = array("playlist-modify-public playlist-read-private playlist-modify-private");
                    $scopes = implode("+", $scopes);
                    $uri =  urlencode($callback);
                    $pattern = "https://accounts.spotify.com/authorize?client_id=$client_id&redirect_uri=$uri&state=success&show_dialog=true&scope=$scopes&response_type=code";
                    $this->response->redirect($pattern);
                }
            } else {
                echo ('Something went wrong');
                die();
            }
        } else {
            echo ('email not found');
            die();
        }
    }
    public function getAccessCodeAction()
    {
        $code = $this->request->getQuery('code');
        $param = array(
            "redirect_uri"  => "http://localhost:8080/login/getAccessCode",
            'grant_type' => 'authorization_code',
            "code"       => $code,
        );
        $client_id = 'c4f52e323fc14d3fac96be4451ebb8ac';
        $client_secret = 'cc9d097a717a44df92b80b8721c50dcf';
        $headers  = [
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),

        ];
        $url      = 'https://accounts.spotify.com/api/token';
        $options  = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => http_build_query($param),
            CURLOPT_HTTPHEADER     => $headers,
        ];
        $credentials = $this->callSpotifyApi($options);
        $id = $this->session->get('id');
        $token = Tokens::findFirstByuser_id($id);
        $token->access_token = $credentials->access_token;
        $token->refresh_token = $credentials->refresh_token;
        $token->save();
        $id = $this->session->get('id');
        $user = Users::findFirstByuser_id($id);
        $user->spotify_id = 'on';
        $user->save();
        $this->response->redirect('index');
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
    public function storeToken($token)
    {
        $token_update = Tokens::findFirstBytoken_id(1);
        $token_update->token = $token;
        $token_update->save();
    }

    public function logoutAction()
    {
        $this->session->destroy();
        $this->response->redirect('login/index');
    }
}
