<?php
namespace sgroup\sitemodule\web\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class CpJs extends AssetBundle
{
    public function init()
    {
        parent::init();

        $this->sourcePath = '@sgroup/sitemodule/resources';

        $this->js = [
            'js/cp.js',
        ];
    }

}
