<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Preguntas extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('PreguntasModel');
        $this->load->model('SeguridadModel');
    }

    function cargarPreguntas_post(){
      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $categoria = $this->security->xss_clean($this->post('categoria'));

      $preguntas = $this->PreguntasModel->obtnerPreguntas($categoria);

      $respuesta = array('preguntas' => $preguntas,
      'sesion' => true);

      $this->response($respuesta);
    }

    function cargarPreguntasExamen_post(){

      $token = $this->security->xss_clean($this->post('token'));
      $sesion = $this->SeguridadModel->revisarToken($token);
      if(!$sesion){
        $respuesta = array('sesion' => false);
        $this->response($respuesta);
      }

      $preguntas = $this->PreguntasModel->obtenerPreguntasExamen();

      $respuesta = array('sesion' => true,
      'preguntas' => $preguntas);
      $this->response($respuesta);
    }

    function guardarPreguntaExamen_post(){

      $pregunta = $this->security->xss_clean($this->post('pregunta'));

      $this->PreguntasModel->guardarPreguntaExamen($pregunta);

      $respuesta = array('mensaje' => 'Pregunta gurdada',
      'estatus' => true);

      $this->response($respuesta);
    }

}
