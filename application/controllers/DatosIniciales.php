<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class DatosIniciales extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('DatosInicialesModel');
        $this->load->model('SeguridadModel');
    }

    function datosMarcadores_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $this->load->model('MarcadoresModel');

      $marcadorGeneral = $this->MarcadoresModel->obtenerMarcadoresGenerles($sesion['idUsuario']);

      $respuesta = array('sesion' => true,
      'marcadorGeneral' => $marcadorGeneral);

      $this->response($respuesta);

    }

    function datosQr_post(){

      $this->load->model('QrModel');

      $qrs = $this->QrModel->obtenerQrs();

      $respuesta = array('estatus' => true,
      'qrs' => $qrs);

      $this->response($respuesta);

    }

    function datosAvatares_post(){

      $avatares = $this->DatosInicialesModel->obtenerAvatares();

      $respuesta = array('estatus' => true,
      'avatares' => $avatares);

      $this->response($respuesta);

    }

    function datosInicio_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $carreras = $this->DatosInicialesModel->obtenerCarreras();
      $this->load->model('UsuarioModel');
      $datosUsuario = $this->UsuarioModel->obtenerDatosUsuario($sesion['idUsuario']);

      $respuesta = array('sesion' => true,
      'datosUsuario' => $datosUsuario,
      'carreras' => $carreras);

      $this->response($respuesta);

    }

    function datosCuenta_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $this->load->model('UsuarioModel');
      $datosUsuario = $this->UsuarioModel->obtenerDatosUsuario($sesion['idUsuario']);

      $respuesta = array('sesion' => true,
      'datosUsuario' => $datosUsuario);

      $this->response($respuesta);

    }

    function datosAmigos_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $this->load->model('UsuarioModel');
      $solicitudes = $this->UsuarioModel->obtenerSolicitudesAmistad($sesion['idUsuario']);
      $amigos = $this->UsuarioModel->obtenerAmigos($sesion['idUsuario']);

      $respuesta = array('solicitudes' => $solicitudes,
      'amigos' => $amigos,
      'sesion' => true);

      $this->response($respuesta);

    }

    function datosInicioPvp_post(){
      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $this->load->model('UsuarioModel');
      $amigos = $this->UsuarioModel->obtenerAmigos($sesion['idUsuario']);
      $datosUsuario = $this->UsuarioModel->obtenerDatosUsuario($sesion['idUsuario']);

      $respuesta = array('amigos' => $amigos,
      'datosUsuario' => $datosUsuario,
      'sesion' => true);

      $this->response($respuesta);
    }

    function datosPartidaPvp_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idUsuarioContrincante = $this->security->xss_clean($this->post('idUsuarioContrincante'));
      $idPartida = $this->security->xss_clean($this->post('idPartida'));

      $this->load->model('UsuarioModel');
      $datosContrincante = $this->UsuarioModel->obtenerDatosUsuario($idUsuarioContrincante);
      $datosUsuario = $this->UsuarioModel->obtenerDatosUsuario($sesion['idUsuario']);

      $this->load->model('PvpModel');
      $preguntasPartida = $this->PvpModel->obtenerPreguntasPartida($idPartida);

      $respuesta = array('sesion' => true,
      'datosUsuario' => $datosUsuario,
      'datosContrincante' => $datosContrincante,
      'preguntasPartida' => $preguntasPartida);

      $this->response($respuesta);

    }

    function datosCategorias_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idCarrera = $this->security->xss_clean($this->post('idCarrera'));

      $categorias = $this->DatosInicialesModel->obtenerCategorias($idCarrera);

      $respuesta = array('sesion' => true,
      'categorias' => $categorias);

      $this->response($respuesta);

    }
}
