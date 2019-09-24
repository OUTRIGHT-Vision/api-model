<?php

namespace Tests\Fakes;

use OUTRIGHTVision\ApiModel;

class City extends ApiModel
{
    public function partnerCity()
    {
        return $this->hasOne(City::class, 'partner_city');
    }
}
