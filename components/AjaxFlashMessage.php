<?php namespace Ebussola\Statefull\Components;

use Cms\Classes\ComponentBase;
use Ebussola\Statefull\Models\Settings;

class AjaxFlashMessage extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Ajax Flash Message',
            'description' => 'Add it only if you use the flash tag markup'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        if (Settings::get('ajax_flash_message_enabled', false)) {
            $this->addJs('assets/js/ajax-flash-message.min.js', [
                'id' => 'ebussola-statefull-ajax-flash-message-script',
                'data-domain' => trim(\Config::get('app.url'), '/ '),
                'data-wrapper' => Settings::get('ajax_flash_message_element_wrapper', 'body'),
                'data-delay' => Settings::get('ajax_flash_message_delay', '4500')
            ]);
        }
    }

}