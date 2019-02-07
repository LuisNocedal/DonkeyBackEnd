<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LoginModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
  }

  public function comprobarLogin($datosLogin){

    $this->db->select("*");
    $this->db->where('usuario', trim($datosLogin['usuario']));
    $this->db->where('password', trim($datosLogin['password']));
    $query = $this->db->get('UsuarioDonkey');

    return (count($query->result())>0)?$query->row_array():null;

  }



}
