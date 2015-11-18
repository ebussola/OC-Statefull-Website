<?php
/**
 * Created by PhpStorm.
 * User: shina
 * Date: 11/18/15
 * Time: 2:14 PM
 */

namespace eBussola\Statefull\Classes;


class TwigFlashNode extends \Twig_Node
{

    public function __construct($name, \Twig_NodeInterface $body, $lineno, $tag = 'flash')
    {
        parent::__construct(['body' => $body], ['name' => $name], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $attrib = $this->getAttribute('name');

//        if ($attrib == 'ajax') {
            $compiler
                ->write('echo "<script type=\"text/template\" id=\"ebussola-statefull-message-template\">";')
                ->write('$context["type"] = \'{{ type }}\';')
                ->write('$context["message"] = \'{{ message }}\';')
                ->subcompile($this->getNode('body'))
                ->write('echo "</script>";')
            ;
//        }
    }

}