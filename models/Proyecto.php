<?php

namespace Model;

class Proyecto extends ActiveRecord{

    // Base de datos
    protected static $tabla = 'proyectos';
    protected static $columnasDB = ['id', 'proyecto', 'url', 'propietarioId'];

    // Atributos
    public $id;
    public $proyecto;
    public $url;
    public $propietarioId;

    // Constructor
    public function __construct($args = [])
    {
        $this->id               = $args['id'] ?? null;
        $this->proyecto         = $args['proyecto'] ?? '';
        $this->url              = $args['url'] ?? '';
        $this->propietarioId    = $args['propietarioId'] ?? '';
    }

    // Validar Proyecto
    public function validarProyecto(){
        if(!$this->proyecto){
            self::$alertas['error'][] = 'El nombre del Proyecto es obligatorio';
        }

        return self::$alertas;
    }
}

?>