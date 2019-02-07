<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ChatModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();

      $this->load->helper('date');
      date_default_timezone_set('America/Mexico_City');
  }

  public function obtenerChat($idUsuario,$idAmigo){

    $this->db->select('idChat');
    $this->db->from('Chat');
    $this->db->where('(idUsuario1 = '.$idUsuario.' or idUsuario2 = '.$idUsuario.')');
    $this->db->where('(idUsuario1 = '.$idAmigo.' or idUsuario2 = '.$idAmigo.')');
    $chat = $this->db->get()->row_array();

    if($chat){

      $this->db->where('idChat',$chat['idChat']);
      $this->db->where('leido',0);
      $this->db->where('idUsuario !=',$idUsuario);
      $this->db->update('MensajeChat',array(
        'leido' => 1
      ));

      $this->db->select('mc.mensaje');
      $this->db->select('mc.fecha');
      $this->db->select('CASE WHEN mc.idUsuario = '.$idUsuario.' THEN "mio" ELSE "amigo" END enviado',FALSE);
      $this->db->from('Chat c');
      $this->db->join('MensajeChat mc','mc.idChat = c.idChat');
      $this->db->where('c.idChat',$chat['idChat']);
      $query = $this->db->get()->result();

      $mensajes = (count($query))?$query:null;

    }else{
      $mensajes = null;

      $nuevoChat = array(
        'idUsuario1' => $idUsuario,
        'idUsuario2' => $idAmigo
      );
      $this->db->insert('Chat',$nuevoChat);
    }

    return array(
      'mensajes' => $mensajes,
      'chat' => $chat
    );
  }

  public function guardarMensaje($datosMensaje,$idAmigo){

    $this->db->select('idChat');
    $this->db->from('Chat');
    $this->db->where('(idUsuario1 = '.$datosMensaje['idUsuario'].' or idUsuario2 = '.$datosMensaje['idUsuario'].')');
    $this->db->where('(idUsuario1 = '.$idAmigo.' or idUsuario2 = '.$idAmigo.')');
    $chat = $this->db->get()->row_array();

    $datosMensaje['idChat'] = $chat['idChat'];
    $datosMensaje['fecha'] = date('Y-m-d H-i-s');
    $datosMensaje['leido'] = 0;

    $this->db->insert('MensajeChat',$datosMensaje);

  }

}
