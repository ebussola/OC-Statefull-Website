<?php namespace eBussola\Statefull;

use eBussola\Statefull\Classes\TwigExtension;
use System\Classes\PluginBase;
use Ebussola\Statefull\Models\Settings;

/**
 * statefull Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Flag used to identify if the router is active or not
     *
     * @var bool
     */
    public static $routerActive = false;

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
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        if (Settings::get('ajax_flash_message_enabled', false)) {
            \Event::listen('cms.page.beforeDisplay', function($controller) {
                $controller->getTwig()->addExtension(new TwigExtension());

                $controller->addJs('/plugins/ebussola/statefull/assets/js/ajax-flash-message.min.js', [
                    'id' => 'ebussola-statefull-ajax-flash-message-script',
                    'data-domain' => trim(\Config::get('app.url'), '/ '),
                    'data-wrapper' => Settings::get('ajax_flash_message_element_wrapper', 'body'),
                    'data-delay' => Settings::get('ajax_flash_message_delay', '4500')
                ]);
            });
        }

        $this->registerConsoleCommand('statefull:cache:refresh', '\ebussola\statefull\commands\StatefullCacheRefresh');
        $this->registerConsoleCommand('statefull:cache:clean', '\ebussola\statefull\commands\StatefullCacheClean');
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
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'statefull' => [
                'label'       => 'Statefull Website',
                'url'         => \Backend::url('ebussola/statefull/urldynamics'),
                'icon'        => 'icon-rocket',
                'permissions' => ['ebussola.statefull.*'],
                'order'       => 500,

                'sideMenu' => [
                    'urldynamics' => [
                        'label'       => 'Dynamics URL',
                        'icon'        => 'icon-code',
                        'url'         => \Backend::url('ebussola/statefull/urldynamics'),
                        'permissions' => ['ebussola.statefull.*']
                    ],
                    'urlblacklist' => [
                        'label'       => 'URL Blacklist',
                        'icon'        => 'icon-minus-circle',
                        'url'         => \Backend::url('ebussola/statefull/urlblacklist'),
                        'permissions' => ['ebussola.statefull.*']
                    ],
                    'getparamblacklist' => [
                        'label'       => 'Get Param Blacklist',
                        'icon'        => 'icon-minus-circle',
                        'url'         => \Backend::url('ebussola/statefull/getparamblacklist'),
                        'permissions' => ['ebussola.statefull.*']
                    ],
                    'urlpurge' => [
                        'label'       => 'URL Purge',
                        'icon'        => 'icon-trash',
                        'url'         => \Backend::url('ebussola/statefull/urldynamics/purge'),
                        'permissions' => ['ebussola.statefull.*']
                    ]
                ]
            ]
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
                'permissions' => ['ebussola.statefull.settings.main']
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
            'ebussola.statefull.settings.main' => [
                'label' => 'Manage Statefull website settings',
                'tab' => 'system::lang.permissions.name'
            ]
        ];
    }

}
