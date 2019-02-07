<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class RegistroModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();

      $this->load->helper('date');
      date_default_timezone_set('America/Mexico_City');
  }

}
