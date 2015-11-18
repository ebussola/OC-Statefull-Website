<?php namespace Ebussola\Statefull\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{

    const ajaxPageContainerDefault = '/ajax';

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'ebussola_statefull_settings';

    public $settingsFields = 'fields.yaml';

}