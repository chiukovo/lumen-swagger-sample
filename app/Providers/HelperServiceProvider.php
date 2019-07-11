<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * 引入所有helper檔案
     */
    public function register()
    {
        foreach (glob(app('path').'/Helper/*.php') as $filename){
            require_once($filename);
        }
    }
}
