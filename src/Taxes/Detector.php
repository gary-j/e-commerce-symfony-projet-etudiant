<?php

namespace App\Taxes;

class Detector
{
    protected $seuil;

    public function __construct(int $seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(float $prix): bool
    {
        // if($prix>=100)
        if ($prix >= $this->seuil) {
            return true;
        }
        return false;
    }
}
