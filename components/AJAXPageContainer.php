<?php namespace Ebussola\Statefull\Components;

use Cms\Classes\ComponentBase;
use Ebussola\Statefull\Models\Settings;

class AJAXPageContainer extends ComponentBase
{

    public $ajaxPageContainer;

    public function componentDetails()
    {
        return [
            'name'        => 'AJAX Page Container component',
            'description' => 'Add this to your footer\'s layout. Just after the ajax framework.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->ajaxPageContainer = Settings::get('ajax_page_container', Settings::ajaxPageContainerDefault);
    }

}