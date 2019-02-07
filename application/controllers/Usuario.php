<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Usuario extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('UsuarioModel');
        $this->load->model('SeguridadModel');
    }

    function datosUsuario_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $datosUsuario = $this->UsuarioModel->obtenerDatosUsuario($sesion['idUsuario']);

      $respuesta = array('datosUsuario' => $datosUsuario,
      'sesion' => true);

      $this->response($respuesta);

    }

    function guardarPuntos_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $puntos = $this->security->xss_clean($this->post('puntos'));
      $idCategoria = $this->security->xss_clean($this->post('idCategoria'));

      $this->UsuarioModel->registrarPartida($sesion['idUsuario'],$puntos,$idCategoria);

      $this->UsuarioModel->guardarPuntos($sesion['idUsuario'],$puntos,$idCategoria);

      $respuesta = array('sesion' => true);

      $this->response($respuesta);
    }

    function guardarExamen_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $preguntas = $this->security->xss_clean($this->post('preguntas'));
      $tiempo = $this->security->xss_clean($this->post('tiempo'));

      $datosExamen = array(
        'idUsuario' => $sesion['idUsuario'],
        'tiempo' => $tiempo);

      $datosExamen = $this->UsuarioModel->guardarExamen($datosExamen,$preguntas);

      $examen = $this->UsuarioModel->obtenerResultadosExamen($datosExamen['idExamen']);

      $respuesta = array('sesion' => true,
      'examen' => $examen);

      $this->response($respuesta);
    }

    function cargarHistorialExamenes_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $examenes = $this->UsuarioModel->obtenerHistorialExamenes($sesion['idUsuario']);

      $respuesta = array('sesion' => true,
      'examenes' => $examenes);

      $this->response($respuesta);

    }

    function cargarDetalleExamen_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idExamen = $this->security->xss_clean($this->post('idExamen'));

      $examen = $this->UsuarioModel->obtenerResultadosExamen($idExamen);

      $respuesta = array('sesion' => true,
      'examen' => $examen);

      $this->response($respuesta);

    }

    function cambiosCuenta_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $datosUsuario = $this->security->xss_clean($this->post('datosUsuario'));
      if($this->security->xss_clean($this->post('password'))){
        $datosUsuario['password'] = $this->security->xss_clean($this->post('password'));
      }
      if($this->security->xss_clean($this->post('idAvatar'))){
        $datosUsuario['idAvatar'] = $this->security->xss_clean($this->post('idAvatar'));
      }
      $this->load->model('RegistroModel');
      $usuarioExistente = $this->RegistroModel->comprobarUsuarioExistente($datosUsuario['usuario'],$sesion['idUsuario']);
      if($usuarioExistente){
        $respuesta = array('estatus' => false,
        'error' => 'El usuario que quieres registrar ya está siendo utilizado.');
        $this->response($respuesta);
      }

      $this->UsuarioModel->actualizarCuentaUsuario($sesion['idUsuario'],$datosUsuario);

      $respuesta = array('estatus' => true,
      'sesion' => true,
      'mensaje' => 'Los datos han sido actualizados');
      $this->response($respuesta);

    }

    function buscarUsuario_post(){
      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $busqueda = $this->security->xss_clean($this->post('busqueda'));

      if(strlen($busqueda)<=3){
        $respuesta = array('usuarios' => null,
        'sesion' => true);

        $this->response($respuesta);
      }

      $usuarios = $this->UsuarioModel->buscarUsuario($busqueda,$sesion['idUsuario']);

      $respuesta = array('usuarios' => $usuarios,
      'sesion' => true);

      $this->response($respuesta);

    }

    function solicitudAdmistad_post(){
      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idUsuario = $this->security->xss_clean($this->post('idUsuario'));
      $cancelada = $this->security->xss_clean($this->post('cancelada'));

      if(!$cancelada){
        $this->UsuarioModel->solicitudAdmistad($idUsuario,$sesion['idUsuario']);
      }else{
        $this->UsuarioModel->solicitudAdmistad($idUsuario,$sesion['idUsuario'],$cancelada);
      }

      $mensaje = ($cancelada)?'La solicitud ha sido cancelada.':'La solicitud ha sido enviada.';

      $respuesta = array('mensaje' => $mensaje,
      'sesion' => true);

      $this->response($respuesta);
    }

    function responderSolicitud_post(){
      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $idSolicitud = $this->security->xss_clean($this->post('idSolicitud'));
      $respuesta = $this->security->xss_clean($this->post('respuesta'));

      $this->UsuarioModel->responderSolicitud($idSolicitud,$respuesta);

      $solicitudes = $this->UsuarioModel->obtenerSolicitudesAmistad($sesion['idUsuario']);
      $amigos = $this->UsuarioModel->obtenerAmigos($sesion['idUsuario']);

      $respuesta = array('solicitudes' => $solicitudes,
      'amigos' => $amigos,
      'sesion' => true);

      $this->response($respuesta);
    }
}
