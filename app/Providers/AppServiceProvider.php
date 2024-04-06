<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $yamlData = Yaml::parseFile(config_path('config.yaml'));

        config(['config' => $yamlData]);
        /*$extensionName = 'dom'; 
        if (!extension_loaded($extensionName)) {
            if (function_exists('dl')) {
                if (dl("{$extensionName}.so")) {
                    echo "The {$extensionName} extension has been loaded successfully.";
                } else {
                    echo "Failed to load the {$extensionName} extension.";
                }
            } else {
                echo "The dl() function is not available. You cannot load extensions dynamically.";
            }
        } else {
            echo "The {$extensionName} extension is already loaded.";
        }*/
    }
}
