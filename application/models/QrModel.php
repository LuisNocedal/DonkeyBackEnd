<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class QrModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();
  }

  public function obtenerQrs(){

    return $this->db->get('QrDonkey')->result();

  }

}
