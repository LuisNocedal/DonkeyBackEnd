<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Pvp extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('PvpModel');
        $this->load->model('SeguridadModel');
    }

    function crearPartida_post(){

      $datosPartida['idUsuarioRetador'] = $this->security->xss_clean($this->post('idUsuarioRetador'));
      $datosPartida['idUsuarioRetado'] = $this->security->xss_clean($this->post('idUsuarioRetado'));

      $partida = $this->PvpModel->crearPartida($datosPartida);

      $respuesta = array('idPartidaPvp' => $partida['idPartidaPvp']);

      $this->response($respuesta);

    }

    function guardarGanadorPartida_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idPartida = $this->security->xss_clean($this->post('idPartida'));

      $this->PvpModel->guardarGanadorPartida($sesion['idUsuario'],$idPartida);

      $respuesta = array('sesion' => true);

      $this->response($respuesta);

    }
}
