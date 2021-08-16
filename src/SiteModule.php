<?php
namespace sgroup\sitemodule;

use sgroup\sitemodule\base\Module;
use sgroup\sitemodule\base\PluginTrait;
use sgroup\sitemodule\fieldlayoutelements\Note;
use sgroup\sitemodule\fieldlayoutelements\Spacer;
use sgroup\sitemodule\twigextensions\Extension;
use sgroup\sitemodule\variables\Variable;

use Craft;
use craft\events\DefineFieldLayoutElementsEvent;
use craft\models\FieldLayout;
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
        $this->_registerFieldLayoutElements();

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

    private function _registerFieldLayoutElements()
    {
        Event::on(FieldLayout::class, FieldLayout::EVENT_DEFINE_UI_ELEMENTS, function(DefineFieldLayoutElementsEvent $event) {
            $event->elements[] = Note::class;
            $event->elements[] = Spacer::class;
        });
    }
}
