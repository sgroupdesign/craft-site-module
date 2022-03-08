<?php
namespace sgroup\sitemodule;

use sgroup\sitemodule\base\Module;
use sgroup\sitemodule\base\PluginTrait;
use sgroup\sitemodule\fields\SectionField;
use sgroup\sitemodule\fieldlayoutelements\Note;
use sgroup\sitemodule\fieldlayoutelements\Spacer;
use sgroup\sitemodule\twigextensions\Extension;
use sgroup\sitemodule\variables\Variable;
use sgroup\sitemodule\web\assets\CpJs;

use Craft;
use craft\events\DefineFieldLayoutElementsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\TemplateEvent;
use craft\models\FieldLayout;
use craft\services\Fields;
use craft\web\Application;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;

use yii\base\Event;

class SiteModule extends Module
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->_checkOffline();
        $this->_registerCpJs();
        $this->_registerTwigExtensions();
        $this->_registerFieldTypes();
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

    private function _registerCpJs()
    {
        // If not control panel request, bail
        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        // Load JS before template is rendered
        Event::on(View::class, View::EVENT_BEFORE_RENDER_TEMPLATE, function(TemplateEvent $event) {
            Craft::setAlias('@sgroup/sitemodule', __dir__);
            
            Craft::$app->getView()->registerAssetBundle(CpJs::class);
        });
    }

    private function _registerTwigExtensions()
    {
        Craft::$app->view->registerTwigExtension(new Extension);
    }

    private function _registerFieldTypes()
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = SectionField::class;
        });
    }

    private function _registerFieldLayoutElements()
    {
        Event::on(FieldLayout::class, FieldLayout::EVENT_DEFINE_UI_ELEMENTS, function(DefineFieldLayoutElementsEvent $event) {
            $event->elements[] = Note::class;
            $event->elements[] = Spacer::class;
        });
    }
}
