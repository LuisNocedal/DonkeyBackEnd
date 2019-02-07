<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Chat extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('ChatModel');
        $this->load->model('SeguridadModel');
    }

    function cargarMensajes_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idAmigo = $this->security->xss_clean($this->post('idAmigo'));

      $chat = $this->ChatModel->obtenerChat($sesion['idUsuario'],$idAmigo);

      $respuesta = array(
        'chat' => $chat,
        'sesion' => true);

      $this->response($respuesta);
    }

    function enviarMensaje_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idAmigo = $this->security->xss_clean($this->post('idAmigo'));
      $datosMensaje['mensaje'] = $this->security->xss_clean($this->post('mensaje'));
      $datosMensaje['idUsuario'] = $sesion['idUsuario'];

      $this->ChatModel->guardarMensaje($datosMensaje,$idAmigo);

      $chat = $this->ChatModel->obtenerChat($sesion['idUsuario'],$idAmigo);

      $respuesta = array(
        'chat' => $chat,
        'sesion' => true);

      $this->response($respuesta);
    }
}
