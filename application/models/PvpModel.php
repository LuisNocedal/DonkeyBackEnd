<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PvpModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
      $this->load->helper('date');
      date_default_timezone_set('America/Mexico_City');
  }

  public function crearPartida($datosPartida){

    $datosPartida['fecha'] = date('Y-m-d H-i-s');

    $this->db->insert('PartidaPvp',$datosPartida);
    $datosPartida['idPartidaPvp'] = $this->db->insert_id();

    $this->db->select('*');
    $this->db->from('PreguntaDonkey');
    $this->db->where('categoria',9);
    $preguntas = $this->db->get()->result();

    for ($i=0; $i < 5; $i++) {

      $pregunta = $preguntas[rand(0, count($preguntas)-1)];

      $preguntaPartidaPvp = array(
        'idPartida' => $datosPartida['idPartidaPvp'],
        'idPregunta' => $pregunta->idPreguntaDonkey,
        'numero' => $i+1
      );

      $this->db->insert('PreguntaPartidaPvp',$preguntaPartidaPvp);

    }

    return $datosPartida;
  }

  public function obtenerPreguntasPartida($idPartida){

    $this->db->select('CASE WHEN imagen IS NOT NULL THEN '.
    'CONCAT("' . API_SERVER . 'imagenes/preguntas/", pd.imagen , ".png") '.
    'ELSE null END as imagen',FALSE);
    $this->db->select('pd.respuesta');
    $this->db->select('pd.incorrecta1');
    $this->db->select('pd.incorrecta2');
    $this->db->select('pd.incorrecta3');
    $this->db->select('pd.pregunta');
    $this->db->select('pd.idPreguntaDonkey');
    $this->db->select('pd.categoria');
    $this->db->from('PreguntaPartidaPvp ppp');
    $this->db->join('PreguntaDonkey pd','pd.idPreguntaDonkey = ppp.idPregunta');
    $this->db->where('ppp.idPartida',$idPartida);
    return $this->db->get()->result();

  }

  public function guardarGanadorPartida($idUsuario,$idPartida){

    $ganador['idGanador'] = $idUsuario;

    $this->db->where('idPartidaPvp',$idPartida);
    $this->db->update('PartidaPvp',$ganador);

  }
}
