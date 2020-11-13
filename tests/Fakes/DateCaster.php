<?php

namespace Tests\Fakes;

use OUTRIGHTVision\ApiModel;

class DateCaster extends ApiModel
{
    protected $cast_dates = [
        'created_at',
        '@nullable:do_not_cast_this_date',
    ];
}
