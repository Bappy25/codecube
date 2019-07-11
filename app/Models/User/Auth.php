<?php

namespace App\Models\User; 

use Base\Authenticable;

class Auth extends User{

  /* Declaring all variables */

  protected $auth;

  public function __construct() 
	{
		$this->auth = new Authenticable;
    parent::__construct();
	}

    // Token setter getter
  function setToken($token){
    $this->token = $token;
  }
  function getToken(){
    return $this->token;
  }

    // Validating necesarry data
  public function validateData()
  {
    
    $errors = array();

    if(empty($this->getPassword())){
      $errors['password'] = "Please input password and make sure it matches with the password confirmation!";
    }

    setErrors($errors);   

    return $this;
  }


  /* All functions */

  public function getUser(){    
    $user = $this->db->table('users')->where('username', '=', $this->getUsername())->or('email', '=', $this->getEmail())->read();
    return $user[0];
  }
  
  public function signin(){   
      $auth = $this->auth->signin($this->getUsername(), $this->getPassword());
      if($auth){
        return true;
      }
      else{
        return false;
      }
  }
  
  public function storeLink(){   
    $auth = $this->auth->storeLink($this->getToken(), $this->getId());
    if($auth){
      return true;
    }
    else{
      return false;
    }
  }
  
  public function getLink(){   
    $link = $this->auth->getLink($this->getToken());
    return $link;
  }
  
  public function updateValidity(){  
    $reset = $this->auth->updateValidity($this->getToken());
    return $reset;
  }

  public function signout(){    
    $this->auth->signout();
  }

  public function passVerify(){  
    $user = $this->getUser();
    if(password_verify($this->getPassword(), $user['password'])){
      return TRUE;
    }
    else{
      return FALSE;
    }
  }

  public function updatePass(){ 
    if(empty(getErrors())){
      $update = $this->db->table('users')->set(['password' => empty($this->getPassword()) ? '' : password_hash($this->getPassword(), PASSWORD_BCRYPT)])->where('id', '=', $this->getId())->update();
      return $update;
    }
  }

}