<?php

namespace App\Services;

use App\Repository\TaxesRepository;

class TaxeCalculatorService
{
    private $taxe;
    private $taxeRepo;
    public function __construct(TaxesRepository $taxesRepository)
    {
       $this->taxeRepo=$taxesRepository;
    }

    public function Taxe(int $amount){
        $taxes = $this->taxeRepo->findAll();
        foreach ($taxes as $tax) {
            switch (true){
                case($amount >= $tax->getMin() && $amount < $tax->getMax()):
                    $this->taxe = $tax->getTaxe();
                    if($this->taxe == 0.02){
                        $this->taxe *=$amount;
                    }
            }
        }
        return $this->taxe;
    }
}
