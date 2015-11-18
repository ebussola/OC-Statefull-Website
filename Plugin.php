<?php namespace eBussola\Statefull;

use System\Classes\PluginBase;

/**
 * statefull Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Statefull Website',
            'description' => 'Power up your website!',
            'author'      => 'ebussola',
            'icon'        => 'icon-rocket'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            '\Ebussola\Statefull\Components\AJAXPageContainer' => 'ajax_page_container'
        ];
    }

    /**
     * Registers any back-end configuration links used by this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'statefull' => [
                'label'       => 'Settings',
                'description' => 'Main settings for statefull websites.',
                'category'    => 'Statefull Website',
                'icon'        => 'icon-rocket',
                'class' => '\Ebussola\Statefull\Models\Settings',
                'order'       => 500,
                'keywords'    => 'statefull ajax',
                'permissions' => ['ebussola.settings.main']
            ]
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'ebussola.settings.main' => [
                'label' => 'Manage Statefull website settings',
                'tab' => 'system::lang.permissions.name'
            ]
        ];
    }

}
