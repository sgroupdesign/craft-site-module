<?php

namespace sgroup\sitemodule\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\ArrayHelper;

class SectionField extends Field
{

    public $defaultText = '';
    public $whitelistedSections = [];

    public static function displayName(): string
    {
        return Craft::t('app', 'Section (Site Module)');
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('site-module/section-field/_input', [
            'field'  => $this,
            'value' => $value,
            'options' => $this->getOptions($element),
        ]);
    }

    public function getSettingsHtml(): string
    {
        $sections = Craft::$app->getSections()->getAllSections();

        $options = array();

        foreach ($sections as $section) {
            $options[] = [
                'label' => $section->name,
                'value' => $section->handle,
            ];
        }

        return Craft::$app->getView()->renderTemplate('site-module/section-field/_settings', [
            'field' => $this,
            'options' => $options
        ]);
    }

    private function getOptions(ElementInterface $element = null): array
    {
        $sections = $this->filterWhitelistedSections(Craft::$app->getSections()->getAllSections());

        $options = array();

        $options[] = [
            'label' => $this->defaultText != '' ? $this->defaultText : 'Select a section',
            'value' => '',
        ];

        foreach ($sections as $section) {
            $currentSite = $element->getSite();

            if (ArrayHelper::isIn($currentSite->id, $section->getSiteIds())) {
                $options[] = [
                    'label' => $section->name,
                    'value' => $section->handle,
                ];
            }
        }

        return $options;
    }

    private function filterWhitelistedSections($sections)
    {
        $sections = ArrayHelper::whereMultiple($sections, [
            'handle' => $this->whitelistedSections
        ]);

        return $sections;
    }

}