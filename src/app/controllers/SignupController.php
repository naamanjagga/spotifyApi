<?php

declare(strict_types=1);

use Phalcon\Mvc\Controller;
use App\Handler\EventHandler;
use App\Components\MyEscaper;

class SignupController extends Controller
{

        public function indexAction()
        {
        }
        public function registerAction()
        {
               
                $checkbox = $this->request->getPost('remember-me');
                if ($checkbox == 'on') {
                        $email = $this->request->getPost('email');
                        $user = Users::findFirstByemail($email);
                        if ($user != null) {
                                echo 'Email id already exist';
                                die;
                        } else {

                                $user = new Users();
                                $escaper = new MyEscaper;
                                $inputdata = array(
                                        'name' => $escaper->sanitize($this->request->getPost('name')),
                                        'email' => $escaper->sanitize($this->request->getPost('email')),
                                        'password' => $escaper->sanitize($this->request->getPost('password')),
                                        'spotify_id' => 'null',
                                );
                                $user->assign(
                                        $inputdata,
                                        [
                                                'name',
                                                'email',
                                                'password',
                                                'spotify_id',
                                        ]
                                );
                                $success = $user->save();
                                $this->view->success = $success;

                                if ($success) {
                                        $user = Users::findFirstByemail($email);
                                        $id = $user->user_id;
                                        $this->session->set("id", $id);
                                        $message = "Thanks for registering!";
                                        $this->response->redirect('index/index');
                                } else {
                                        $message = "Sorry, the following problems were generated:<br>"
                                                . implode('<br>', $user->getMessages());
                                }
                        }
                } else {
                        $message = 'Please agree to our terms and conditions';
                }
                $this->view->message = $message;
        }
}
