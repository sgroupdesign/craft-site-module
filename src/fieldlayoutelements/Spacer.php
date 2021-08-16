<?php
namespace sgroup\sitemodule\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\fieldlayoutelements\BaseUiElement;
use craft\helpers\Cp;
use craft\helpers\Html;

class Spacer extends BaseUiElement
{
    // Protected Methods
    // =========================================================================

    protected function selectorLabel(): string
    {
        return Craft::t('app', 'Spacer');
    }


    // Public Methods
    // =========================================================================

    public function settingsHtml()
    {
        return Cp::textFieldHtml([
            'label' => Craft::t('app', 'Spacer'),
            'id' => 'spacer',
            'name' => 'spacer',
        ]);
    }

    public function formHtml(ElementInterface $element = null, bool $static = false)
    {
        return '';
    }
}
