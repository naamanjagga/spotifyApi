<?php

use Phalcon\Mvc\Model;

class Tokens extends Model
{
    public $token_id;
    public $token;

    // public function getAccessToken()
    // {
        // $id = $this->session->get('id');
        // $getToken = Tokens::findFirstByuser_id($id);
        // $token = $getToken->access_token;
        // echo $token; die;
    // }
}
