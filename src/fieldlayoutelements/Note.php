<?php
namespace sgroup\sitemodule\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\fieldlayoutelements\BaseUiElement;
use craft\helpers\Cp;
use craft\helpers\Html;

class Note extends BaseUiElement
{
    // Properties
    // =========================================================================

    public $note;


    // Protected Methods
    // =========================================================================

    protected function selectorLabel(): string
    {
        return $this->note ?: Craft::t('app', 'Note');
    }

    protected function selectorIcon()
    {
        return '@appicons/hash.svg';
    }


    // Public Methods
    // =========================================================================

    public function settingsHtml()
    {
        return Cp::textFieldHtml([
            'label' => Craft::t('app', 'Note'),
            'id' => 'note',
            'name' => 'note',
            'value' => $this->note,
        ]);
    }

    public function formHtml(ElementInterface $element = null, bool $static = false)
    {
        return Html::tag('p', Html::encode(Craft::t('site', $this->note)));
    }
}
