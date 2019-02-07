<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioModel extends CI_Model {

  function __construct() {
      parent::__construct();

      $this->load->database();

      $this->load->helper('date');
      date_default_timezone_set('America/Mexico_City');
  }

  public function obtenerDatosUsuario($idUsuarioDonkey){

    $this->db->select('ud.usuario');
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", a.nombreImagen , ".png") as avatar');
    $this->db->select('ng.nivel');
    $this->db->select('ng.puntosNivel');
    $this->db->select('ng.nivelSiguiente');
    $this->db->from('UsuarioDonkey ud');
    $this->db->join('Avatar a','ud.idAvatar = a.idAvatar');
    $this->db->join('NivelGeneral ng','ng.idUsuario = ud.idUsuarioDonkey');
    $this->db->where('ud.idUsuarioDonkey',$idUsuarioDonkey);
    return $this->db->get()->row();

  }

  public function registrarPartida($idUsuarioDonkey,$puntos,$idCategoria){

    $datosPartida = array(
      'puntos' => $puntos,
      'idCategoria' => $idCategoria,
      'idUsuario' => $idUsuarioDonkey,
      'fecha' => date('Y-m-d H-i-s')
    );

    $this->db->insert('PartidaSolo',$datosPartida);

  }

  public function guardarPuntos($idUsuarioDonkey,$puntos,$idCategoria){

    $this->db->select('idPuntosUsuario');
    $this->db->select('puntos');
    $this->db->from('PuntosUsuario');
    $this->db->where('idUsuarioDonkey',$idUsuarioDonkey);
    $this->db->where('idCategoria',$idCategoria);
    $query = $this->db->get();

    $puntosUsuario = (count($query->result())>0)?$query->row_array():null;

    if($puntosUsuario){
      $puntosGanados['puntos'] = $puntosUsuario['puntos'] + $puntos;
      $this->db->where('idPuntosUsuario',$puntosUsuario['idPuntosUsuario']);
      $this->db->update('PuntosUsuario',$puntosGanados);
    }else{
      $datosPuntos = array(
        'puntos' => $puntos,
        'idCategoria' => $idCategoria,
        'idUsuarioDonkey' => $idUsuarioDonkey,
      );
      $this->db->insert('PuntosUsuario',$datosPuntos);
    }

    $this->calcularNivelUsuario($idUsuarioDonkey);

  }

  function calcularNivelUsuario($idUsuarioDonkey){

    $this->db->select('SUM(puntos) as puntos');
    $this->db->from('PuntosUsuario');
    $this->db->where('idUsuarioDonkey',$idUsuarioDonkey);
    $this->db->group_by('idUsuarioDonkey');
    $puntos = $this->db->get()->row()->puntos;

    $sobrantes = $puntos;
    $puntosTotales = 0;
    $nivel = 1;
    $puntosUltimoNivel = 0;
    $calculado = false;
    while(!$calculado){
        $puntosNivelActual = ceil($puntosUltimoNivel+($puntosUltimoNivel/Porcentaje));
        if($puntosNivelActual<PrimerNivel){
            $puntosNivelActual = PrimerNivel;
        }
        if($sobrantes<$puntosNivelActual){
            $calculado = true;
        }else{
            $nivel = ($sobrantes>=$puntosNivelActual)?$nivel+1:$nivel;
            $sobrantes -= $puntosNivelActual;
            $puntosTotales += $puntosNivelActual;
            $puntosUltimoNivel = $puntosNivelActual;
        }
    }

    $nivelAlcanzado = array(
      'nivel' => $nivel,
      'puntosNivel' => $sobrantes,
      'nivelSiguiente' => $puntosNivelActual
    );

    $this->db->where('idUsuario',$idUsuarioDonkey);
    $this->db->update('NivelGeneral',$nivelAlcanzado);

  }

  public function guardarExamen($datosExamen,$preguntas){

    $this->db->select('Max(intento) as intento');
    $this->db->from('Examen');
    $this->db->where('idUsuario',$datosExamen['idUsuario']);
    $query = $this->db->get();

    $ultimoIntento = (count($query->result())>0)?$query->row()->intento:0;

    $datosExamen['intento'] = $ultimoIntento + 1;
    $datosExamen['fecha'] = date('Y-m-d H-i-s');

    $this->db->insert('Examen',$datosExamen);
    $datosExamen['idExamen'] = $this->db->insert_id();

    $respuestasExamen = array();

    foreach ($preguntas as $pregunta) {
      array_push($respuestasExamen,
        array(
        'idPregunta' => $pregunta['idPregunta'],
        'respuesta' => (isset($pregunta['eleccion']))?$pregunta['eleccion']:null,
        'idExamen' => $datosExamen['idExamen']
        )
      );
    }

    $this->db->insert_batch('RespuestaExamen',$respuestasExamen);

    return $datosExamen;

  }

  public function obtenerResultadosExamen($idExamen){

    $this->db->select('pe.pregunta');
    $this->db->select('re.respuesta as respuestaUsuario');
    $this->db->select('pe.respuesta');
    $this->db->select('pe.incorrecta1');
    $this->db->select('pe.incorrecta2');
    $this->db->select('pe.incorrecta3');
    $this->db->select('CASE WHEN re.respuesta = pe.respuesta THEN true ELSE false END as correcta',false);
    $this->db->select('e.tiempo');
    $this->db->select('e.intento');
    $this->db->select('e.fecha');
    $this->db->from('Examen e');
    $this->db->join('RespuestaExamen re','re.idExamen = e.idExamen');
    $this->db->join('PreguntasExamen pe','pe.idPreguntasExamen = re.idPregunta');
    $this->db->where('e.idExamen',$idExamen);
    $this->db->order_by('pe.numero','ASC');
    $query = $this->db->get()->result();

    $respuestas = array();
    $correctas = 0;
    $incorrectas = 0;

    foreach ($query as $respuesta) {
      $opciones = array();
      if(isset($respuesta->incorrecta1)){array_push($opciones,$respuesta->incorrecta1);}
      if(isset($respuesta->incorrecta2)){array_push($opciones,$respuesta->incorrecta2);}
      if(isset($respuesta->incorrecta3)){array_push($opciones,$respuesta->incorrecta3);}
      array_push($opciones,$respuesta->respuesta);
      shuffle($opciones);
      if($respuesta->correcta == 1){
        $correctas ++;
      }else{
        $incorrectas ++;
      }
       array_push($respuestas,array(
         'pregunta' => $respuesta->pregunta,
         'opciones' => $opciones,
         'respuesta' => $respuesta->respuestaUsuario,
         'correcta' => ($respuesta->correcta == 1)?true:false,
       ));
    }

    $mintuos = ($respuesta->tiempo/60>=10)?floor($respuesta->tiempo/60):'0'.floor($respuesta->tiempo/60);
    $segundos = ($respuesta->tiempo%60>=10)?$respuesta->tiempo%60:'0'.$respuesta->tiempo%60;
    $tiempo = $mintuos.':'.$segundos;

    return array(
      'respuestas' => $respuestas,
      'tiempo' => $tiempo,
      'intento' => $respuesta->intento,
      'fecha' => $respuesta->fecha,
      'correctas' => $correctas,
      'incorrectas' => $incorrectas
    );

  }

  public function obtenerHistorialExamenes($idUsuario){

    $this->db->select('*');
    $this->db->from('Examen');
    $this->db->where('idUsuario',$idUsuario);
    $this->db->order_by('intento');
    $examenes = $this->db->get()->result();

    if(count($examenes)>0){

      for ($i=0; $i < count($examenes); $i++) {
        $this->db->select('CASE WHEN re.respuesta = pe.respuesta THEN true ELSE false END as correcta',false);
        $this->db->from('RespuestaExamen re');
        $this->db->join('PreguntasExamen pe','pe.idPreguntasExamen = re.idPregunta');
        $this->db->where('re.idExamen',$examenes[$i]->idExamen);
        $query = $this->db->get()->result();
        $correctas = 0;
        $incorrectas = 0;
        foreach ($query as $respuesta) {
          if($respuesta->correcta == 1){
            $correctas ++;
          }else{
            $incorrectas ++;
          }
        }
        $examenes[$i]->correctas = $correctas;
        $examenes[$i]->incorrectas = $incorrectas;

        $mintuos = ($examenes[$i]->tiempo/60>=10)?floor($examenes[$i]->tiempo/60):'0'.floor($examenes[$i]->tiempo/60);
        $segundos = ($examenes[$i]->tiempo%60>=10)?$examenes[$i]->tiempo%60:'0'.$examenes[$i]->tiempo%60;
        $examenes[$i]->tiempo = $mintuos.':'.$segundos;

      }

      return $examenes;

    }else{
      return null;
    }

  }

  public function actualizarCuentaUsuario($idUsuario,$datosUsuario){

    $this->db->where('idUsuarioDonkey',$idUsuario);
    $this->db->update('UsuarioDonkey',$datosUsuario);

  }

  public function buscarUsuario($busqueda,$idUsuario){

    $this->db->select('ud.usuario');
    $this->db->select('ud.nombre');
    $this->db->select('ud.idUsuarioDonkey');
    $this->db->select('CASE WHEN (SELECT idSolicitud FROM Solicitud WHERE ud.idUsuarioDonkey = idUsuario '.
    'AND estatus = "En espera" '.
    'AND idSolicitante ='.$idUsuario.') '.
    'THEN true ELSE false END as pendiente',FALSE);
    $this->db->select('CASE WHEN (SELECT idAmigo FROM Amigo WHERE ud.idUsuarioDonkey = idUsuarioAmigo '.
    'AND idUsuario ='.$idUsuario.') '.
    'THEN true ELSE false END as amigo',FALSE);
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", a.nombreImagen , ".png") as avatar');
    $this->db->from('UsuarioDonkey ud');
    $this->db->join('Avatar a','a.idAvatar = ud.idAvatar');
    $this->db->like('usuario',$busqueda);
    $this->db->or_like('ud.nombre',$busqueda);
    $this->db->where('ud.idUsuarioDonkey !=',$idUsuario);
    return $this->db->get()->result();
  }

  public function solicitudAdmistad($idUsuario,$idSolicitante,$cancelada = false){
    if(!$cancelada){
      $datosSolicitud = array(
        'idUsuario' => $idUsuario,
        'idSolicitante' => $idSolicitante,
        'estatus' => 'En espera'
      );
      $this->db->insert('Solicitud',$datosSolicitud);
    }else{
      $this->db->where('idUsuario',$idUsuario);
      $this->db->where('idSolicitante',$idSolicitante);
      $this->db->delete('Solicitud');
    }
  }

  public function obtenerSolicitudesAmistad($idUsuario){

    $this->db->select('ud.usuario');
    $this->db->select('ud.idUsuarioDonkey');
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", a.nombreImagen , ".png") as avatar');
    $this->db->select('s.idSolicitud');
    $this->db->from('Solicitud s');
    $this->db->join('UsuarioDonkey ud','ud.idUsuarioDonkey = s.idSolicitante');
    $this->db->join('Avatar a','a.idAvatar = ud.idAvatar');
    $this->db->where('s.idUsuario',$idUsuario);
    $this->db->where('s.estatus','En espera');
    $query = $this->db->get()->result();

    return (count($query)>0)?$query:null;
  }

  public function responderSolicitud($idSolicitud,$respuesta){

    $respuestaSolicitud['estatus'] = $respuesta;

    $this->db->where('idSolicitud',$idSolicitud);
    $this->db->update('Solicitud',$respuestaSolicitud);

    if($respuesta == 'Aceptada'){

      $this->db->select('*');
      $this->db->from('Solicitud');
      $this->db->where('idSolicitud',$idSolicitud);
      $solicitud = $this->db->get()->row_array();

      $this->db->insert('Amigo',array(
        'idUsuario' => $solicitud['idUsuario'],
        'idUsuarioAmigo' => $solicitud['idSolicitante']
      ));

      $this->db->insert('Amigo',array(
        'idUsuario' => $solicitud['idSolicitante'],
        'idUsuarioAmigo' => $solicitud['idUsuario']
      ));
    }
  }

  public function obtenerAmigos($idUsuario){

    $this->db->select('ud.usuario');
    $this->db->select('CONCAT("' . API_SERVER . 'imagenes/avatares/", av.nombreImagen , ".png") as avatar');
    $this->db->select('ud.nombre');
    $this->db->select('ud.idUsuarioDonkey');
    $this->db->select('ng.nivel');
    $this->db->select(' CASE WHEN (SELECT idChat FROM Chat WHERE (idUsuario1 = '.$idUsuario.' or idUsuario2 = '.$idUsuario.
    ') AND (idUsuario1 = ud.idUsuarioDonkey or idUsuario2 = ud.idUsuarioDonkey)) IS NOT NULL THEN ('.
    ' SELECT COUNT(idMensajeChat) FROM MensajeChat WHERE idChat = (SELECT idChat FROM Chat WHERE (idUsuario1 = '.$idUsuario.' or idUsuario2 = '.$idUsuario.
    ') AND (idUsuario1 = ud.idUsuarioDonkey or idUsuario2 = ud.idUsuarioDonkey)) AND leido = 0 AND idUsuario != '.$idUsuario.')'.
    ' ELSE false END as mensajesNoLeidos');
    $this->db->from('Amigo a');
    $this->db->join('UsuarioDonkey ud','ud.idUsuarioDonkey = a.idUsuarioAmigo');
    $this->db->join('NivelGeneral ng','ng.idUsuario = a.idUsuarioAmigo','LEFT');
    $this->db->join('Avatar av','av.idAvatar = ud.idAvatar');
    $this->db->where('a.idUsuario',$idUsuario);
    $this->db->order_by('ud.nombre');
    $query = $this->db->get()->result();

    return (count($query)>0)?$query:null;

  }
}
