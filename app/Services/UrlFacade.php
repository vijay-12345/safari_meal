<?php namespace App\Services;

use Illuminate\Support\Facades\Facade;

class UrlFacade extends Facade {

    protected static function getFacadeAccessor() { return 'url'; }

}