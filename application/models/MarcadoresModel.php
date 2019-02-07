<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MarcadoresModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
  }

  public function obtenerMarcadoresGenerles($idUsuario){

    $this->db->select('ud.usuario');
    $this->db->select('ud.carrera');
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", a.nombreImagen , ".png") as avatar');
    $this->db->select('ud.idUsuarioDonkey');
    $this->db->select('SUM(p.puntos) as puntos');
    $this->db->select('CASE WHEN ud.idUsuarioDonkey = "'.$idUsuario.'" THEN true ELSE false END as usuarioActual',false);
    $this->db->from('PuntosUsuario p');
    $this->db->join('UsuarioDonkey ud','ud.idUsuarioDonkey = p.idUsuarioDonkey');
    $this->db->join('Avatar a','a.idAvatar = ud.idAvatar');
    $this->db->order_by('puntos','DESC');
    $this->db->group_by('idUsuarioDonkey');
    return $this->db->get()->result();

  }

}
