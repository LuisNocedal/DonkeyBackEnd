<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class RegistroModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();

      $this->load->helper('date');
      date_default_timezone_set('America/Mexico_City');
  }

  public function registrarUsuario($datosRegistro){

    $datosRegistro['fechaRegistro'] = date('Y-m-d H-i-s');

    $this->db->insert('UsuarioDonkey',$datosRegistro);

    $datosRegistro['idUsuarioDonkey'] = $this->db->insert_id();

    return $datosRegistro;

  }

  public function inicializarNivel($idUsuarioDonkey){

    $datosNivel = array(
      'idUsuario' => $idUsuarioDonkey,
      'nivel' => 1,
      'puntosNivel' => 0,
      'nivelSiguiente' => PrimerNivel
    );

    $this->db->insert('NivelGeneral',$datosNivel);

  }

  public function comprobarUsuarioExistente($usuario,$idUsuario = null){

    $this->db->select('*');
    $this->db->from('UsuarioDonkey');
    $this->db->where('usuario',$usuario);
    if($idUsuario){
      $this->db->where('idUsuarioDonkey !=',$idUsuario);
    }
    $query = $this->db->get()->result();

    return (count($query)>0)?true:false;

  }

  public function comprobarCodigo($codigo){

    $this->db->select('*');
    $this->db->from('CodigoRegistro');
    $this->db->where('codigo',trim($codigo));
    $this->db->where('activo',1);
    $query = $this->db->get();

    if(count($query->result())>0){
      $this->db->where('codigo',$codigo);
      $this->db->update('CodigoRegistro',array('activo' => false));
      return true;
    }else{
      return false;
    }

  }
}
