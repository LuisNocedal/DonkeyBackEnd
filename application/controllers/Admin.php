<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Admin extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }

        $this->load->model('AdminModel');
    }

    function datosQr_post(){

      $qrs = $this->AdminModel->obtenerQrs();

      $respuesta = array(
        'qrs' => $qrs,
        'estatus' => true);

      $this->response($respuesta);

    }

    function guardarQr_post(){

      $datosQr['descripcion'] = $this->security->xss_clean($this->post('descripcion'));
      $datosQr['latitud'] = $this->security->xss_clean($this->post('latitud'));
      $datosQr['longitud'] = $this->security->xss_clean($this->post('longitud'));

      $this->AdminModel->guardarQr($datosQr);

      $qrs = $this->AdminModel->obtenerQrs();

      $respuesta = array(
        'qrs' => $qrs,
        'estatus' => true);

      $this->response($respuesta);

    }

    function eliminarQr_post(){

      $idQrDonkey = $this->security->xss_clean($this->post('idQrDonkey'));

      $this->AdminModel->eliminarQr($idQrDonkey);

      $qrs = $this->AdminModel->obtenerQrs();

      $respuesta = array(
        'qrs' => $qrs,
        'estatus' => true);

      $this->response($respuesta);

    }

    function generarCodigo_post(){

      $codigo = "";
      $pattern = "1234567890QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm";
      for ($i = 0; $i < 8; $i++) {
          $codigo .= $pattern{rand(0, 62)};
      }

      $this->AdminModel->guardarCodigo($codigo);

      $respuesta = array('estatus' => true,
      'codigo' => $codigo);

      $this->response($respuesta);
    }

    function cargarUsuarios_post(){

      $usuarios = $this->AdminModel->obtenerUsuarios();

      $respuesta = array('estatus' => true,
      'usuarios' => $usuarios);

      $this->response($respuesta);

    }

    function cargarExamenes_post(){

      $idUsuario = $this->security->xss_clean($this->post('idUsuario'));

      $examenes = $this->AdminModel->obtenerExamenes($idUsuario);

      $respuesta = array('estatus' => true,
      'examenes' => $examenes);

      $this->response($respuesta);
    }

    function cargarDetalleExamen_post(){

      $idExamen = $this->security->xss_clean($this->post('idExamen'));

      $detalleExamen = $this->AdminModel->obtenerDetalleExamen($idExamen);

      $respuesta = array('estatus' => true,
      'detalleExamen' => $detalleExamen);

      $this->response($respuesta);
    }

    function guardarAvatar_post(){

      $imagen = $this->security->xss_clean($this->post('imagen'));
      $datosAvatar['nombre'] = $this->security->xss_clean($this->post('nombre'));

      $decodedImage = base64_decode($imagen);

      if ($decodedImage != null) {
          $this->load->helper('file');
          $this->load->helper('date');
          $datosAvatar['nombreImagen'] = Md5(date('Y-m-d H-i-s'));
          write_file("./imagenes/avatares/" . $datosAvatar['nombreImagen'] . ".png", $decodedImage);
      }

      $this->AdminModel->guardarAvatar($datosAvatar);
      $avatares = $this->AdminModel->obtenerAvatares();

      $respuesta = array('estatus' => true,
      'avatares' => $avatares);

      $this->response($respuesta);
    }

    function cargarAvatares_post(){

      $avatares = $this->AdminModel->obtenerAvatares();

      $respuesta = array('estatus' => true,
      'avatares' => $avatares);

      $this->response($respuesta);
    }

    function guardarCambiosAvatar_post(){

      $datosAvatar['nombre'] = $this->security->xss_clean($this->post('nombre'));
      $idAvatar = $this->security->xss_clean($this->post('idAvatar'));

      $this->AdminModel->guardarCambiosAvatar($datosAvatar,$idAvatar);

      $avatares = $this->AdminModel->obtenerAvatares();

      $respuesta = array('estatus' => true,
      'avatares' => $avatares);

      $this->response($respuesta);

    }

    function eliminarAvatar_post(){
      $idAvatar = $this->security->xss_clean($this->post('idAvatar'));

      $this->AdminModel->eliminarAvatar($idAvatar);

      $avatares = $this->AdminModel->obtenerAvatares();

      $respuesta = array('estatus' => true,
      'avatares' => $avatares);

      $this->response($respuesta);
    }

    function guardarCategoria_post(){

      $imagen = $this->security->xss_clean($this->post('imagen'));
      $datosCategoria['nombre'] = $this->security->xss_clean($this->post('nombre'));
      $datosCategoria['idCarrera'] = $this->security->xss_clean($this->post('idCarrera'));

      $decodedImage = base64_decode($imagen);

      if ($decodedImage != null) {
          $this->load->helper('file');
          $this->load->helper('date');
          $datosCategoria['nombreImagen'] = Md5(date('Y-m-d H-i-s'));
          write_file("./imagenes/categorias/" . $datosCategoria['nombreImagen'] . ".png", $decodedImage);
      }

      $this->AdminModel->guardarCategoria($datosCategoria);
      $categorias = $this->AdminModel->obtenerCategorias();

      $respuesta = array('estatus' => true,
      'categorias' => $categorias);

      $this->response($respuesta);
    }

    function cargarCategorias_post(){

      $categorias = $this->AdminModel->obtenerCategorias();
      $carreras = $this->AdminModel->obtenerCarreras();

      $respuesta = array('estatus' => true,
      'carreras' => $carreras,
      'categorias' => $categorias);

      $this->response($respuesta);
    }

    function guardarCambiosCategoria_post(){

      $datosCategoria['nombre'] = $this->security->xss_clean($this->post('nombre'));
      $datosCategoria['idCarrera'] = $this->security->xss_clean($this->post('idCarrera'));
      $idCategoria = $this->security->xss_clean($this->post('idCategoria'));
      if($this->security->xss_clean($this->post('imagen'))){
        $imagen = $this->security->xss_clean($this->post('imagen'));
        $decodedImage = base64_decode($imagen);

        if ($decodedImage != null) {
            $this->load->helper('file');
            $this->load->helper('date');
            $datosCategoria['nombreImagen'] = Md5(date('Y-m-d H-i-s'));
            write_file("./imagenes/categorias/" . $datosCategoria['nombreImagen'] . ".png", $decodedImage);
        }

      }

      $this->AdminModel->guardarCambiosCategoria($datosCategoria,$idCategoria);

      $categorias = $this->AdminModel->obtenerCategorias();

      $respuesta = array('estatus' => true,
      'categorias' => $categorias);

      $this->response($respuesta);

    }

    function eliminarCategoria_post(){
      $idCategoria = $this->security->xss_clean($this->post('idCategoria'));

      $this->AdminModel->eliminarCategoria($idCategoria);

      $categorias = $this->AdminModel->obtenerCategorias();

      $respuesta = array('estatus' => true,
      'categorias' => $categorias);

      $this->response($respuesta);
    }

    function subirPregunta_post(){

      $datosPregunta = $this->security->xss_clean($this->post('datosPregunta'));

      if($this->security->xss_clean($this->post('imagen'))){
        $imagen = $this->security->xss_clean($this->post('imagen'));
        $decodedImage = base64_decode($imagen);

        if ($decodedImage != null) {
            $this->load->helper('file');
            $this->load->helper('date');
            $datosPregunta['imagen'] = Md5(date('Y-m-d H-i-s'));
            write_file("./imagenes/preguntas/" . $datosPregunta['imagen'] . ".png", $decodedImage);
        }

      }

      $this->AdminModel->subirPregunta($datosPregunta);

      $respuesta = array('mensaje' => 'Pregunta guardada',
      'estatus' => true);

      $this->response($respuesta);
    }

    function cargarPreguntasPorId_post(){

      $idCategoria = $this->security->xss_clean($this->post('idCategoria'));

      $preguntas = $this->AdminModel->obtenerPreguntasPorId($idCategoria);

      $respuesta = array('estatus' => true,
      'preguntas' => $preguntas);

      $this->response($respuesta);

    }

    function eliminarPregunta_post(){

      $idCategoria = $this->security->xss_clean($this->post('idCategoria'));
      $idPregunta = $this->security->xss_clean($this->post('idPregunta'));

      $this->AdminModel->eliminarPregunta($idPregunta);

      $preguntas = $this->AdminModel->obtenerPreguntasPorId($idCategoria);

      $respuesta = array('estatus' => true,
      'preguntas' => $preguntas);

      $this->response($respuesta);

    }

    function guardarCambiosPregunta_post(){

      $datosPregunta = $this->security->xss_clean($this->post('datosPregunta'));
      $idPregunta = $this->security->xss_clean($this->post('idPregunta'));
      $imagen = $this->security->xss_clean($this->post('imagen'));

      if($this->security->xss_clean($this->post('imagen'))){
        $imagen = $this->security->xss_clean($this->post('imagen'));
        $decodedImage = base64_decode($imagen);

        if ($decodedImage != null) {
            $this->load->helper('file');
            $this->load->helper('date');
            $datosPregunta['imagen'] = Md5(date('Y-m-d H-i-s'));
            write_file("./imagenes/preguntas/" . $datosPregunta['imagen'] . ".png", $decodedImage);
        }

      }

      $this->AdminModel->guardarCambiosPregunta($idPregunta,$datosPregunta);

      $respuesta = array('estatus' => true);

      $this->response($respuesta);

    }

    function guardarCarrera_post(){

      $imagen = $this->security->xss_clean($this->post('imagen'));
      $datosCarrera['nombre'] = $this->security->xss_clean($this->post('nombre'));

      $decodedImage = base64_decode($imagen);

      if ($decodedImage != null) {
          $this->load->helper('file');
          $this->load->helper('date');
          $datosCarrera['imagen'] = Md5(date('Y-m-d H-i-s').$datosCarrera['nombre']);
          write_file("./imagenes/carreras/" . $datosCarrera['imagen'] . ".png", $decodedImage);
      }

      $this->AdminModel->guardarCarrera($datosCarrera);
      $carreras = $this->AdminModel->obtenerCarreras();

      $respuesta = array('estatus' => true,
      'carreras' => $carreras);

      $this->response($respuesta);
    }

    function cargarCarreras_post(){

      $carreras = $this->AdminModel->obtenerCarreras();

      $respuesta = array('estatus' => true,
      'carreras' => $carreras);

      $this->response($respuesta);
    }

    function guardarCambiosCarrera_post(){

      $datosCarrera['nombre'] = $this->security->xss_clean($this->post('nombre'));
      $idCarrera = $this->security->xss_clean($this->post('idCarrera'));

      $this->AdminModel->guardarCambiosCarrera($datosCarrera,$idCarrera);

      $carreras = $this->AdminModel->obtenerCarreras();

      $respuesta = array('estatus' => true,
      'carreras' => $carreras);

      $this->response($respuesta);

    }

    function eliminarCarrera_post(){
      $idCarrera = $this->security->xss_clean($this->post('idCarrera'));

      $this->AdminModel->eliminarCarrera($idCarrera);

      $carreras = $this->AdminModel->obtenerCarreras();

      $respuesta = array('estatus' => true,
      'carreras' => $carreras);

      $this->response($respuesta);
    }
}
