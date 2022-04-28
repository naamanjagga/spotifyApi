<?php

namespace App\Components;
use Phalcon\Events\Manager;
use Tokens;

class Curlapi
{
  public function getSearch($track)
  {
    $track = str_replace(' ', '%20', $track);
    $url = "https://api.spotify.com/v1/search?q=$track&type=track&limit=3";
    return $response = $this->getUrl($url);
  }
  public function getArtist($id)
  {
    $id = str_replace(' ', '%20', $id);
    $url = "https://api.spotify.com/v1/artists/$id/albums?limit=3";
    return $response = $this->getUrl($url);
  }
  public function getAlbum($id)
  {
    $id = str_replace(' ', '%20', $id);
    $url = "https://api.spotify.com/v1/albums/$id/tracks?limit=3";
    return $response = $this->getUrl($url);
  }
  public function getPlaylistItems($id)
  {

    $id = str_replace(' ', '%20', $id);
    $url = "https://api.spotify.com/v1/playlists/$id/tracks";
    return $response = $this->getUrl($url);
  }

  public function getUser()
  {
    $url = "https://api.spotify.com/v1/me";
    return $response = $this->getUrl($url);
  }
  public function getRecommendations()
  {
    $url = "https://api.spotify.com/v1/recommendations?limit=5&seed_artists=53XhwfbYqKCa1cC15pYq2q&seed_genres=classical%2Ccountry&seed_tracks=0pqnGHJpmpxLKifKRmU6WP";
    return $response = $this->getUrl($url);
  }
  public function removeItem($id)
  {
    $url = "https://api.spotify.com/v1/playlists/3qs5ncvOHMgJoTT7MkgqLa/tracks";
    $ch = curl_init($url);
    $data = array(
      'tracks' => array(
        0 => array(
          'uri' => $id,
        ),
      ),
    );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    return $response = $this->postDataRequest($ch, $data);
  }
  public function addToPlaylist($uri)
  {
    $url = "https://api.spotify.com/v1/playlists/3qs5ncvOHMgJoTT7MkgqLa/tracks?uris=$uri";
    $ch = curl_init($url);
    $data = array(
      "uris" =>  $uri,
    );
    return $response = $this->postDataRequest($ch, $data);
  }
  public function getPlaylists()
  {
    $url = "https://api.spotify.com/v1/me/playlists";
    return $response = $this->getUrl($url);
  }
  public function createPlaylist($playlist)
  {
    $id = str_replace(' ', '%20',  $playlist);
    $url = "https://api.spotify.com/v1/users/31nsbrnajdvf4xu7nsfuce3qor6u/playlists";
    $ch = curl_init($url);
    $data = array(
      "name" =>  $playlist,
      "description" => "party songs",
      "public" => "false"
    );
    return $response = $this->postDataRequest($ch, $data);
  }

  public function getUrl($url)
  {
    $token = new Tokens();
    $getToken = Tokens::findFirstByuser_id(1);
    $token = $getToken->access_token;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
      "Accept: application/json",
      "Authorization: Bearer $token",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    $response =  json_decode($resp, true);
    if ($response['error']['message'] == 'Invalid access token') {
      $eventsManager = new Manager();
      $eventsManager->attach('token', new \App\Handler\Eventhandler());
      $eventsManager->fire('token:generateToken', $this);
    } else {
      return $response;
    }
  }
  public function postDataRequest($ch, $data)
  {
    $token = new Tokens();
    $getToken = Tokens::findFirstByuser_id(1);
    $token = $getToken->access_token;
    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array(
      "Accept: application/json",
      "Authorization: Bearer $token",
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    return $response =  json_decode($result, true);
    curl_close($ch);
  }
}
