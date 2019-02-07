<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DatosInicialesModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
  }

  public function obtenerAvatares(){
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", nombreImagen , ".png") as imagen');
    $this->db->select('nombre');
    $this->db->select('idAvatar');
    $this->db->from('Avatar');
    return $this->db->get()->result();
  }

  public function obtenerCategorias($idCarrera){
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/categorias/", nombreImagen , ".png") as imagen');
    $this->db->select('nombre');
    $this->db->select('idCategoria');
    $this->db->select('color');
    $this->db->from('Categoria');
    $this->db->where('idCarrera',$idCarrera);
    return $this->db->get()->result();
  }

  public function obtenerCarreras(){
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/carreras/", imagen , ".png") as imagen');
    $this->db->select('nombre');
    $this->db->select('idCarrera');
    //$this->db->select('colorBoton');
    //$this->db->select('nombreColor');
    $this->db->from('Carrera');
    return $this->db->get()->result();
  }

}
