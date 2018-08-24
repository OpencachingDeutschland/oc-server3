<?php

class GnsLocationsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $rc;

    /** @var int */
    public $ufi;

    /** @var int */
    public $uni;

    /** @var float */
    public $lat;

    /** @var float */
    public $lon;

    /** @var int */
    public $dmsLat;

    /** @var int */
    public $dmsLon;

    /** @var string */
    public $utm;

    /** @var string */
    public $jog;

    /** @var string */
    public $fc;

    /** @var string */
    public $dsg;

    /** @var int */
    public $pc;

    /** @var string */
    public $cc1;

    /** @var string */
    public $adm1;

    /** @var string */
    public $adm2;

    /** @var int */
    public $dim;

    /** @var string */
    public $cc2;

    /** @var string */
    public $nt;

    /** @var string */
    public $lc;

    /** @var string */
    public $sHORTFORM;

    /** @var string */
    public $gENERIC;

    /** @var string */
    public $sORTNAME;

    /** @var string */
    public $fULLNAME;

    /** @var string */
    public $fULLNAMEND;

    /** @var DateTime */
    public $mODDATE;

    /** @var string */
    public $admtxt1;

    /** @var string */
    public $admtxt3;

    /** @var string */
    public $admtxt4;

    /** @var string */
    public $admtxt2;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->rc === null;
    }
}
