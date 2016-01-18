<?php namespace Ebussola\Statefull\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Get Param Blacklist Back-end Controller
 */
class GetParamBlacklist extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('eBussola.Statefull', 'statefull', 'getparamblacklist');
    }
}