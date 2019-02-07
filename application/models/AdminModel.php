<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdminModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
  }

  public function obtenerQrs(){

    return $this->db->get('QrDonkey')->result();

  }

  public function guardarQr($datosQr){

    $this->db->insert('QrDonkey',$datosQr);

  }

  public function eliminarQr($idQrDonkey){

    $this->db->where('idQrDonkey',$idQrDonkey);
    $this->db->delete('QrDonkey');

  }

  public function guardarCodigo($codigo){

    $datosCodigo = array(
      'codigo' => $codigo,
      'activo' => true
    );

    $this->db->insert('CodigoRegistro',$datosCodigo);

  }

  public function obtenerUsuarios(){

    $this->db->select('ud.usuario');
    $this->db->select('ud.nombre');
    $this->db->select('ud.avatar');
    $this->db->select('ud.idUsuarioDonkey');
    $this->db->select('count(e.idExamen) as intentos');
    $this->db->from('UsuarioDonkey ud');
    $this->db->join('Examen e','e.idUsuario = ud.idUsuarioDonkey');
    $this->db->group_by('e.idUsuario');
    $this->db->order_by('ud.nombre');
    return $this->db->get()->result();

  }

  public function obtenerExamenes($idUsuario){

    $this->db->select('e.fecha');
    $this->db->select('e.intento');
    $this->db->select('e.idExamen');
    $this->db->select('CONCAT( '.
    ' CASE WHEN (e.tiempo/60) >= 10 THEN ROUND(e.tiempo/60) ELSE CONCAT("0",ROUND(e.tiempo/60)) END , ":" , '.
    ' CASE WHEN (e.tiempo%60) >= 10 THEN (e.tiempo%60) ELSE CONCAT("0",(e.tiempo%60)) END ) as tiempo',FALSE);
    $this->db->select('( SELECT COUNT(re.idRespuestaExamen) FROM RespuestaExamen re '.
    'JOIN PreguntasExamen pe ON pe.idPreguntasExamen = re.idPregunta WHERE re.idExamen = e.idExamen '.
    'AND re.respuesta = pe.respuesta) as correctas');
    $this->db->select('( SELECT COUNT(re.idRespuestaExamen) FROM RespuestaExamen re '.
    'JOIN PreguntasExamen pe ON pe.idPreguntasExamen = re.idPregunta WHERE re.idExamen = e.idExamen '.
    'AND re.respuesta != pe.respuesta) as incorrectas');
    $this->db->from('Examen e');
    $this->db->where('e.idUsuario',$idUsuario);
    $this->db->order_by('e.intento');
    return $this->db->get()->result();

  }

  public function obtenerDetalleExamen($idExamen){

    $this->db->select('e.fecha');
    $this->db->select('e.intento');
    $this->db->select('CONCAT( '.
    ' CASE WHEN (e.tiempo/60) >= 10 THEN ROUND(e.tiempo/60) ELSE CONCAT("0",ROUND(e.tiempo/60)) END , ":" , '.
    ' CASE WHEN (e.tiempo%60) >= 10 THEN (e.tiempo%60) ELSE CONCAT("0",(e.tiempo%60)) END ) as tiempo',FALSE);
    $this->db->select('( SELECT COUNT(re.idRespuestaExamen) FROM RespuestaExamen re '.
    'JOIN PreguntasExamen pe ON pe.idPreguntasExamen = re.idPregunta WHERE re.idExamen = e.idExamen '.
    'AND re.respuesta = pe.respuesta) as correctas');
    $this->db->select('( SELECT COUNT(re.idRespuestaExamen) FROM RespuestaExamen re '.
    'JOIN PreguntasExamen pe ON pe.idPreguntasExamen = re.idPregunta WHERE re.idExamen = e.idExamen '.
    'AND re.respuesta != pe.respuesta) as incorrectas');
    $this->db->from('Examen e');
    $this->db->where('e.idExamen',$idExamen);
    $this->db->order_by('e.intento');
    $datosExamen = $this->db->get()->row();

    $this->db->select('pe.pregunta');
    $this->db->select('re.respuesta as respuestaUsuario');
    $this->db->select('pe.respuesta');
    $this->db->select('pe.incorrecta1');
    $this->db->select('pe.incorrecta2');
    $this->db->select('pe.incorrecta3');
    $this->db->select('CASE WHEN re.respuesta = pe.respuesta THEN true ELSE false END as correcta',false);
    $this->db->from('Examen e');
    $this->db->join('RespuestaExamen re','re.idExamen = e.idExamen');
    $this->db->join('PreguntasExamen pe','pe.idPreguntasExamen = re.idPregunta');
    $this->db->where('e.idExamen',$idExamen);
    $this->db->order_by('pe.numero','ASC');
    $query = $this->db->get()->result();

    $respuestas = array();

    foreach ($query as $respuesta) {
      $opciones = array();
      if(isset($respuesta->incorrecta1)){array_push($opciones,$respuesta->incorrecta1);}
      if(isset($respuesta->incorrecta2)){array_push($opciones,$respuesta->incorrecta2);}
      if(isset($respuesta->incorrecta3)){array_push($opciones,$respuesta->incorrecta3);}
      array_push($opciones,$respuesta->respuesta);
      shuffle($opciones);
       array_push($respuestas,array(
         'pregunta' => $respuesta->pregunta,
         'opciones' => $opciones,
         'respuesta' => $respuesta->respuestaUsuario,
         'correcta' => ($respuesta->correcta == 1)?true:false,
       ));
    }

    return array(
      "datosExamen" => $datosExamen,
      "respuestas" => $respuestas
    );

  }

  public function guardarAvatar($datosAvatar){
    $this->db->insert('Avatar',$datosAvatar);
  }

  public function obtenerAvatares(){

    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", nombreImagen , ".png") as imagen');
    $this->db->select('nombre');
    $this->db->select('idAvatar');
    $this->db->from('Avatar');
    return $this->db->get()->result();

  }

  public function guardarCambiosAvatar($datosAvatar,$idAvatar){

    $this->db->where('idAvatar',$idAvatar);
    $this->db->update('Avatar',$datosAvatar);

  }

  public function eliminarAvatar($idAvatar){

    $this->db->where('idAvatar',$idAvatar);
    $this->db->delete('Avatar');

  }

  public function guardarCategoria($datosCategoria){
    $this->db->insert('Categoria',$datosCategoria);
  }

  public function obtenerCategorias(){

    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/categorias/", nombreImagen , ".png") as imagen');
    $this->db->select('nombre');
    $this->db->select('idCategoria');
    $this->db->select('idCarrera');
    $this->db->from('Categoria');
    return $this->db->get()->result();

  }

  public function guardarCambiosCategoria($datosCategoria,$idCategoria){

    $this->db->where('idCategoria',$idCategoria);
    $this->db->update('Categoria',$datosCategoria);

  }

  public function eliminarCategoria($idCategoria){

    $this->db->where('idCategoria',$idCategoria);
    $this->db->delete('Categoria');

  }

  public function subirPregunta($datosPregunta){

    $this->db->insert('PreguntaDonkey',$datosPregunta);

  }

  public function obtenerPreguntasPorId($idCategoria){

    $this->db->select('CASE WHEN imagen IS NOT NULL THEN '
    .'CONCAT("' . API_SERVER . 'imagenes/preguntas/", imagen , ".png") ELSE null END as imagen',FALSE);
    $this->db->select('pregunta');
    $this->db->select('respuesta');
    $this->db->select('incorrecta1');
    $this->db->select('incorrecta2');
    $this->db->select('incorrecta3');
    $this->db->select('categoria');
    $this->db->select('idPreguntaDonkey');
    $this->db->from('PreguntaDonkey');
    $this->db->where('categoria',$idCategoria);
    return $this->db->get()->result();

  }

  public function eliminarPregunta($idPregunta){

    $this->db->where('idPreguntaDonkey',$idPregunta);
    $this->db->delete('PreguntaDonkey');

  }

  public function guardarCambiosPregunta($idPregunta,$datosPregunta){

    $this->db->where('idPreguntaDonkey',$idPregunta);
    $this->db->update('PreguntaDonkey',$datosPregunta);

  }

  public function guardarCarrera($datosCarrera){
    $this->db->insert('Carrera',$datosCarrera);
  }

  public function obtenerCarreras(){

    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/carreras/", imagen , ".png") as imagen');
    $this->db->select('nombre');
    $this->db->select('idCarrera');
    $this->db->from('Carrera');
    return $this->db->get()->result();

  }

  public function guardarCambiosCarrera($datosCarrera,$idCarrera){

    $this->db->where('idCarrera',$idCarrera);
    $this->db->update('Carrera',$datosCarrera);

  }

  public function eliminarCarrera($idCarrera){

    $this->db->where('idCarrera',$idCarrera);
    $this->db->delete('Carrera');

  }
}
