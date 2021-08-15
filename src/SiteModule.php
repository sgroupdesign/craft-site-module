<?php
namespace sgroup\sitemodule;

use sgroup\sitemodule\base\Module;
use sgroup\sitemodule\twigextensions\Extension;
use sgroup\sitemodule\variables\Variable;

use Craft;
use craft\web\Application;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class SiteModule extends Module
{
    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->_checkOffline();
        $this->_setPluginComponents();
        $this->_registerTwigExtensions();

        // Prevent code from firing too early before Craft is bootstrapped
        Craft::$app->on(Application::EVENT_INIT, function() {
            // Set an alias for our Vite-processed static assets. Useful for SVGs.
            Craft::setAlias('@resource', $this->getService()->getResourcePath());
        });
    }

    // Private Methods
    // =========================================================================

    private function _checkOffline()
    {
        if ((Craft::$app->getConfig()->general->isOffline ?? null)) {
            exit();
        }
    }

    private function _registerTwigExtensions()
    {
        Craft::$app->view->registerTwigExtension(new Extension);
    }
}
