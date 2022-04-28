<?php

declare(strict_types=1);

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        $response = $this->api->getUser();
        $this->view->response = $response;
        if (isset($_POST['refresh'])) {
            $eventHandler = $this->di->get('EventsManager');
            $eventHandler->fire('token:generateToken', $this);
        }
        if (isset($_POST['btn'])) {
            $track = $_POST['search'];

            if ($_POST['Artist'] == 'on') {

                $response = $this->api->getSearch($track);
                $artistId = $response['tracks']['items'][0]['album']['artists'][0]['id'];
                $response = $this->api->getArtist($artistId);
                $this->view->artistCheckbox = $_POST['Artist'];
                $artist = array();
                foreach ($response['items'] as $key => $value) {
                    $array = array($value['artists'][0]['name'], $value['images'][0]['url']);
                    array_push($artist, $array);
                }
                $this->view->artist = $artist;
            }
            if ($_POST['Album'] == 'on') {
                $response = $this->api->getSearch($track);
                $albumId = $response['tracks']['items'][0]['album']['id'];
                $response = $this->api->getAlbum($albumId);
                $this->view->albumCheckbox = $_POST['Album'];
                $album = array();
                foreach ($response['items'] as $key => $value) {
                    $array = array($value['name'], $value['artists'][0]['name']);
                    array_push($album, $array);
                }
                $this->view->album = $album;
            }
            if ($_POST['Playlist'] == 'on') {
                $response = $this->api->getPlaylists();
                $this->view->playlistCheckbox = $_POST['Playlist'];
                $this->view->playlist = $response['items'];
            }
            if ($_POST['Track'] == 'on') {
                $_POST['Track'];

                $response = $this->api->getSearch($track);
                $this->view->trackon =  $_POST['Track'];
                $this->view->name = $_POST['search'];
                $this->view->tracks = $response['tracks']['items'];
            }
        } else {
            $recommendations = $this->api->getRecommendations();
            $this->view->recommendations = $recommendations['tracks'];
        }
    }
    public function inputAction()
    {
    }
    public function createAction()
    {
        $playlist = $_POST['createPlaylist'];
        $response = $this->api->createPlaylist($playlist);
        $this->response->redirect('index/seeplaylist');
    }
    public function addToPlaylistAction()
    {
        $playlist = $_POST['addToPlaylist'];
        $response = $this->api->addToPlaylist($playlist);
        $this->response->redirect('index/getItems');
    }
    public function seeplaylistAction()
    {
        $response = $this->api->getPlaylists();
        $this->view->playlist = $response['items'];
    }
    public function getItemsAction()
    {
        $playlistId = $_POST['viewPlaylist'];
        $response = $this->api->getPlaylistItems($playlistId);
        $this->view->tracks = $response['items'];
    }
    public function removeFromPlaylistAction()
    {
        $remove = $_POST['removeFromPlaylist'];
        $response = $this->api->removeItem($remove);
        $this->response->redirect('index/getItems');
    }
}
