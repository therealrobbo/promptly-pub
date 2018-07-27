<?php

/**
 * The main model class
 *
 * Class PZ_Model
 */
class PZ_Model {

    public $gPZ;
    public $gCI;

    /**
     * Constructor
     *
     */
    public function __construct(  ) {

        global $gPZ;

        $this->gPZ  = $gPZ;
        $this->gCI  = $gPZ['controller_instance'];
    }
}

?>