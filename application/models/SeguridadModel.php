<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SeguridadModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();

      $this->load->helper('date');
      date_default_timezone_set('America/Mexico_City');
  }

  public function crearSesionUsuario($datosUsuario){

    $sesion['fechaInicio'] = date('Y-m-d H-i-s');
    $sesion['token'] = md5($datosUsuario['usuario'].$datosUsuario['nombre'].$sesion['fechaInicio']);
    $sesion['idUsuario'] = $datosUsuario['idUsuarioDonkey'];

    $this->cerrarSesionUsuario($datosUsuario['idUsuarioDonkey']);

    $this->db->insert('SesionDonkey',$sesion);

    return $sesion['token'];

  }

  public function cerrarSesionUsuario($idUsuarioDonkey){

    $this->db->where('idUsuario',$idUsuarioDonkey);
    $this->db->where('fechaFin',null);
    $this->db->update('SesionDonkey',array('fechaFin' => date('Y-m-d H-i-s')));

  }

  public function revisarToken($token){

    $this->db->select('*');
    $this->db->from('SesionDonkey');
    $this->db->where('fechaFin',null);
    $this->db->where('token',$token);
    $query = $this->db->get();

    return (count($query->result())>0)?$query->row_array():false;

  }

}
