<?php
namespace sgroup\sitemodule\services;

use modules\SiteModule;

use Craft;
use craft\base\Component;

use nystudio107\vite\Vite;
use nystudio107\pluginvite\helpers\FileHelper;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function getResourceUrl($url = '')
    {
        try {
            $viteService =  Vite::$plugin->vite;
            $root = $viteService->devServerRunning() ? $viteService->devServerPublic : $viteService->serverPublic;

            return FileHelper::createUrl($root, $url);
        } catch (\Throwable $e) {
            SiteModule::error($e->getMessage());

            return $url;
        }
    }

    public function getResourcePath($path = '')
    {
        try {
            $root = Vite::$plugin->vite->devServerRunning() ? '@webroot/../resources' : '@webroot/dist';

            return FileHelper::createUrl($root, $path);
        } catch (\Throwable $e) {
            SiteModule::error($e->getMessage());

            return $path;
        }
    }

}
