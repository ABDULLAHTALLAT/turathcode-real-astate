<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('بيتك في ايدك');
})->purpose('Display an inspiring quote');
