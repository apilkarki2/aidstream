<?php namespace App\Http\Controllers\Lite\Activity;

use App\Http\Controllers\Lite\LiteController;

class ActivityController extends LiteController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return 'asd';
    }
}
