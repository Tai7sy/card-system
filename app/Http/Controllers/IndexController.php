<?php

namespace App\Http\Controllers;

use App\System;
use App\UserDomain;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    private $config = [];

    function __construct()
    {
        $this->checkIsInMaintain();
        $this->config = [
            'logo' => System::_get('logo'),
            'name' => config('app.name'),
            'description' => System::_get('description'),
            'keywords' => System::_get('keywords'),
            'home_links' => System::_get('home_links'),
            'company' => System::_get('company'),
            'icp' => System::_get('icp'),
            'js_tj' => System::_get('js_tj'),
            'js_kf' => System::_get('js_kf'),
        ];
    }

    function docs()
    {
        return view('index.docs', array_merge($this->config, []));
    }

    function docs_api(){
        return view('index.docs_api', array_merge($this->config, []));
    }

    function help()
    {
        return view('index.help', array_merge($this->config, []));
    }

    function about()
    {
        return view('index.about', array_merge($this->config, []));
    }

    function legal_notice()
    {
        return view('index.legal_notice', array_merge($this->config, []));
    }

    function forbid_product()
    {
        return view('index.forbid_product', array_merge($this->config, []));
    }

    function tos()
    {
        return view('index.tos', array_merge($this->config, []));
    }

    function privateAction()
    {
        return view('index.private', array_merge($this->config, []));
    }

}
