<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter("amount", [$this, "formatAmount"]),
            new TwigFilter("price", [$this, "priceFormat"])
        ];
    }

    // Formater un montant. ex : 12299 en 122,99 €
    // if en fonction de la langue sélection (. , € $ ...)
    public function formatAmount($value)
    {
        // 1. Diviser par 100 le montant
        $amount = $value / 100;
        // dd($amount);

        // 2. Mettre une virgule avant les décimals
        $amount = number_format($amount, 2, ',', ' ');
        // dd($amount);

        // 3. Return en Rajoutant le symbole €
        return $amount . ' €';
    }

    public function priceFormat($value, string $symbol = '€', string $decsep = ',', string $thousandsep = ' ')
    {
        $price = $value / 100;
        $price = number_format($price, 2, $decsep, $thousandsep);
        return $price . $symbol;
    }
}
