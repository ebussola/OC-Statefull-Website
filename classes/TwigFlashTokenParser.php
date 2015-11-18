<?php
/**
 * Created by PhpStorm.
 * User: shina
 * Date: 11/18/15
 * Time: 2:16 PM
 */

namespace ebussola\statefull\classes;


use Twig_Error_Syntax;
use Twig_NodeInterface;
use Twig_Token;

class TwigFlashTokenParser extends \Twig_TokenParser
{

    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     *
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        if ($token = $stream->nextIf(Twig_Token::NAME_TYPE)) {
            $name = $token->getValue();
        }
        else {
            $name = 'all';
        }
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideIfEnd'), true);
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new TwigFlashNode($name, $body, $lineno, $this->getTag());
    }

    public function decideIfEnd(Twig_Token $token)
    {
        return $token->test(array('endflash'));
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'flash';
    }

}