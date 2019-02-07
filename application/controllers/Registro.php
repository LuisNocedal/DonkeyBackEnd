<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Registro extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('RegistroModel');
        $this->load->model('SeguridadModel');
    }

    function registrar_post(){

      $version = $this->security->xss_clean($this->post('version'));
      if($version != VERSION){
        $respuesta = array('estatus' => false,
        'error' => 'La versión en la que estás es obsoleta.');
        $this->response($respuesta);
      }

      $datosRegistro = $this->security->xss_clean($this->post('datosRegistro'));
      //$codigo = $this->security->xss_clean($this->post('codigo'));

      /*$codigoValido = $this->RegistroModel->comprobarCodigo($codigo);
      if(!$codigoValido){
        $respuesta = array('estatus' => false,
        'error' => 'El código es invalido.');
        $this->response($respuesta);
      }*/

      $usuarioExistente = $this->RegistroModel->comprobarUsuarioExistente($datosRegistro['usuario']);
      if($usuarioExistente){
        $respuesta = array('estatus' => false,
        'error' => 'El usuario que quieres registrar ya está siendo utilizado.');
        $this->response($respuesta);
      }

      $datosRegistro = $this->RegistroModel->registrarUsuario($datosRegistro);
      $this->RegistroModel->inicializarNivel($datosRegistro['idUsuarioDonkey']);

      $token = $this->SeguridadModel->crearSesionUsuario($datosRegistro);

      $respuesta = array('token' => $token,
      'idUsuario' => $datosRegistro['idUsuarioDonkey'],
      'estatus' => true);

      $this->response($respuesta);
    }

}
