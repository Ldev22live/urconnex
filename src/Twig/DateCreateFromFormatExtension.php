<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateCreateFromFormatExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('date_create_from_format', [$this, 'dateCreateFromFormat']),
        ];
    }

    public function dateCreateFromFormat($dateString, $format)
    {
        return \DateTime::createFromFormat($format, $dateString);
    }
}
