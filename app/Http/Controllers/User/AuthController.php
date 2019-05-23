<?php

namespace App\Http\Controllers\User; 

use App\Base\Request;
use App\Models\People\Auth; 
use App\Models\People\User; 
use App\Http\Controllers\Controller; 

class UserController extends Controller
{
    private $user; 
    private $auth;
    private $request; 

    public function __construct() {
        $this->auth = new Auth;
        $this->user = new User;
        $this->request = new Request();
    }

    public function login() 
    {
        $this->guard('CheckGuest'); 
        $curl = new Curl();
        try {
            $auth_api = $this->config('auth');
            $response = $curl->post($auth_api['url'], array(
                'key' => $auth_api['key']
            ));
            if($response->status == 'error'){
                $this->abort(402, $response->message);
            }
            else{
                $this->request->put('captcha', simple_php_captcha());   
                return $this->view('admin.auth.login');
            }    
        }
        catch (Exception $e){
            echo $e->getMessage();
            echo $curl->getError();
        }
    }

    public function register() 
    {
        $store = $this->user->setData($_POST)->validateData()->storeUser();
        if($store){
            $this->request->setFlash(array('success' => "Your user account has beed added!"));
            $this->request->setFlash($alerts);
            $this->redirect('user/show');
        }
        $this->redirect(back());
    }

    public function checkCaptcha() 
    {
        if($_POST['check'] == $this->request->show('captcha')->code){
          $this->auth->signout();
        }
        $this->redirect('admin/login');
    }

    public function signin() 
    {
        $this->auth->setUsername($_POST['username']);
        $this->auth->setPassword($_POST['password']);
        $signin = $this->auth->signin();
	    if($signin){
            $this->redirect('admin/dashboard');
	    }
	    else{
	    	$this->redirect('admin/login');
	    }
    }

    public function forgotPassword() 
    {
        $this->guard('CheckGuest'); 
        return $this->view('admin.auth.forgotpass');
    }

    public function sendResetInfo() 
    {
        $this->guard('CheckGuest'); 
        $this->auth->setUsername($_POST['credential']);
        $this->auth->setEmail($_POST['credential']);
        if($user){
            $user = $this->auth->getUser();
            $token = md5(uniqid());
            $this->auth->setId($user['id']);
            $this->auth->setToken($token);
            $this->auth->storeLink();
            $subject = 'Link For Password Reset!';
            $body = 'Please click the below link to reset your password-';
            $body .= '<br><a href="'.route("admin/password/reset", ["token" => $token]).'" target="_blank">Link to reset password!</a>';
            // $this->sendMail($user['email'], $subject, $body);
        }
        $msg = ['code' => 'success', 'message' => 'Pleace check your mail! You will get an email if your given credential is found in our database!', 'link' => route('admin/login')];
        return $this->view('admin.auth.message', compact('msg'));
    }

    public function resetPassword() 
    {
        $this->guard('CheckGuest'); 
        $this->auth->setToken($_GET['token']);
        $link = $this->auth->getLink(); 
        if($link['validity'] == 1 && ((strtotime($link['created_at'])+ 60*60) > time())){
            return $this->view('admin.auth.reset', compact('link'));
        }
        else{
            $msg = ['code' => 'error', 'message' => 'This link is expired!', 'link' => route('admin/login')];
            return $this->view('admin.auth.message', compact('msg'));
        }
    }

    public function updatePassword() 
    {
        $this->guard('CheckGuest'); 
        $this->auth->setUsername($_POST['username']);
        $this->auth->setPassword($_POST['password']);
        $update = $this->auth->updatePass();
        $this->auth->setToken($_POST['token']);
        $update = $this->auth->updateValidity();
        $msg = ['code' => 'success', 'message' => 'Your password has been updated!', 'link' => route('admin/login')];
        return $this->view('admin.auth.message', compact('msg'));
    }

    public function signout() 
    {
        $this->auth->signout();
        $this->redirect('admin/login');
    }

}