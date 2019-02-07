<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Login extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('LoginModel');
        $this->load->model('SeguridadModel');
    }

    function ingresar_post(){

      $version = $this->security->xss_clean($this->post('version'));
      if($version != VERSION){
        $respuesta = array('estatus' => false,
        'error' => 'La versión en la que estás es obsoleta.');
        $this->response($respuesta);
      }

      $datosLogin['usuario'] = $this->security->xss_clean($this->post('usuario'));
      $datosLogin['password'] = $this->security->xss_clean($this->post('password'));
      //$datosLogin['push_token'] = $this->security->xss_clean($this->post('push_token'));

      $log = $this->LoginModel->comprobarLogin($datosLogin);

      if(!$log){
        $respuesta = array('estatus' => false,
        'error' => 'La contraseña o usuario es incorrecto.');
        $this->response($respuesta);
      }

      $token = $this->SeguridadModel->crearSesionUsuario($log);

      $respuesta = array('token' => $token,
      'idUsuario' => $log['idUsuarioDonkey'],
      'estatus' => true);

      $this->response($respuesta);

    }
}
