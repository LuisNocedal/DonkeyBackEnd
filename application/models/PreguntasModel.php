<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PreguntasModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
  }

  public function obtnerPreguntas($categoria){

    $this->db->select('CASE WHEN imagen IS NOT NULL THEN '.
    'CONCAT("' . API_SERVER . 'imagenes/preguntas/", imagen , ".png") '.
    'ELSE null END as imagen',FALSE);
    $this->db->select('respuesta');
    $this->db->select('incorrecta1');
    $this->db->select('incorrecta2');
    $this->db->select('incorrecta3');
    $this->db->select('pregunta');
    $this->db->select('idPreguntaDonkey');
    $this->db->select('categoria');
    $this->db->from('PreguntaDonkey');
    $this->db->where('categoria',$categoria);
    return $this->db->get()->result();

  }

  public function obtenerPreguntasExamen(){

    $this->db->select('*');
    $this->db->from('PreguntasExamen');
    $this->db->order_by('numero','ASC');
    $query = $this->db->get()->result();

    $preguntas = array();

    foreach ($query as $pregunta) {
      $opciones = array();
      if(isset($pregunta->incorrecta1)){array_push($opciones,$pregunta->incorrecta1);}
      if(isset($pregunta->incorrecta2)){array_push($opciones,$pregunta->incorrecta2);}
      if(isset($pregunta->incorrecta3)){array_push($opciones,$pregunta->incorrecta3);}
      array_push($opciones,$pregunta->respuesta);
      shuffle($opciones);
       array_push($preguntas,array(
         'idPregunta' => $pregunta->idPreguntasExamen,
         'pregunta' => $pregunta->pregunta,
         'opciones' => $opciones,
         'numero' => $pregunta->numero
       ));
    }

    return $preguntas;
  }

  public function guardarPreguntaExamen($pregunta){

    $this->db->insert('PreguntasExamen',$pregunta);

  }

}
