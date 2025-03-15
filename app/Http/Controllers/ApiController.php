<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

class ApiController extends Controller
{
   /* Using the trait, all the controllers that extend from APIController
    will have access to the methods of the trait  */
    use ApiResponser;
    /*
    public function __construct()
    {
    	$this->middleware('auth:api');
    }
    */
  
}
