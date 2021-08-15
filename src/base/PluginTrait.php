<?php
namespace sgroup\sitemodule\base;

use sgroup\sitemodule\SiteModule;
use sgroup\sitemodule\services\Service;

use Craft;

use putyourlightson\logtofile\LogToFile;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static $plugin;


    // Public Methods
    // =========================================================================

    public function getService()
    {
        return $this->get('service');
    }

    public static function log($message)
    {
        LogToFile::info($message, 'site-module');
    }

    public static function error($message)
    {
        LogToFile::error($message, 'site-module');
    }


    // Private Methods
    // =========================================================================

    private function _setPluginComponents()
    {
        $this->setComponents([
            'service'  => Service::class,
        ]);
    }

}