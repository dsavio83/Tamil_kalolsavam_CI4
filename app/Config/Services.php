<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use Config\Services as AppServices;
use App\Libraries\LegacyView;
use Config\View as ViewConfig;
use Config\Paths;

class Services extends BaseService
{
    public static function renderer(?string $viewPath = null, ?ViewConfig $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('renderer', $viewPath, $config);
        }

        $viewPath = in_array($viewPath, [null, '', '0'], true) ? (new Paths())->viewDirectory : $viewPath;
        $config ??= config(ViewConfig::class);

        return new LegacyView($config, $viewPath, AppServices::get('locator'), CI_DEBUG, AppServices::get('logger'));
    }

    public static function template($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('template');
        }

        return new \App\Libraries\Template();
    }

    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
}
