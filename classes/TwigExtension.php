<?php
/**
 * Created by PhpStorm.
 * User: shina
 * Date: 11/18/15
 * Time: 2:13 PM
 */

namespace eBussola\Statefull\Classes;


class TwigExtension extends \Twig_Extension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'eBussola Statefull Website - Ajax Flash Message';
    }

    public function getTokenParsers()
    {
        return [new TwigFlashTokenParser()];
    }

}