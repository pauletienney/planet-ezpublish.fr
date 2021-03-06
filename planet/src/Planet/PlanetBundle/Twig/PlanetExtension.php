<?php

namespace Planet\PlanetBundle\Twig;

use eZ\Publish\Core\Repository\Values\Content\Location,
    Symfony\Component\DependencyInjection\ContainerInterface,
    eZPlaneteUtils;

class PlanetExtension extends \Twig_Extension
{
    /**
     * Service container
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    protected $container;

    /**
     * Contructor of the planet twig extension
     *
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct( ContainerInterface $container )
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'Planet';
    }

    public function getFilters()
    {
        return array(
            'clean_rewrite_xhtml' => new \Twig_Filter_Method(
                $this,
                'cleanRewriteXHTML'
            ),
            'shorten' => new \Twig_Filter_Method(
                $this,
                'shorten'
            ),
            'entity_decode' => new \Twig_Filter_Method(
                $this,
                'entityDecode'
            ),
        );
    }

    public function getGlobals()
    {
        $global = array(
            'planet' => array(
                'tree' => array(
                    'root' => $this->container->getParameter( 'planet.tree.root' ),
                    'blogs' => $this->container->getParameter( 'planet.tree.blogs' ),
                    'planetarium' => $this->container->getParameter( 'planet.tree.planetarium' ),
                ),
                'page' => array(
                    'posts' => $this->container->getParameter( 'planet.page.posts' ),
                    'title' => $this->container->getParameter( 'planet.page.title' ),
                )
            )
        );
        return $global;
    }

    /**
     * Clean up the stored HTML to avoid various issues (injection, broken
     * links, ...)
     *
     * @param string $html
     * @param string $baseUri
     * @return string
     * @todo rewrite the code in the Symfony stack
     */
    public function cleanRewriteXHTML( $html, $baseUri )
    {
        return eZPlaneteUtils::cleanRewriteXHTML( $html, $baseUri );
    }

    /**
     * Shortens the $text to $maxLength. If the text is longer than $maxLength,
     * it adds the suffix to shortened text.
     *
     * @param string $text
     * @param int $maxLength
     * @param string $suffix
     * @return string
     */
    public function shorten( $text, $maxLength, $suffix = '...' )
    {
        mb_internal_encoding( 'UTF-8' ); // move this in a "global" init
        $len = mb_strlen( $text );
        $slen = mb_strlen( $suffix );
        if ( $len <= $maxLength || $len < $slen )
        {
            return $text;
        }
        return mb_substr( $text, 0, $maxLength - $slen ) . $suffix;
    }

    /**
     * Wrapper to html_entity_decode
     *
     * @param string $html
     * @return string
     */
    public function entityDecode( $html )
    {
        return html_entity_decode(
            $html, ENT_QUOTES, 'UTF-8'
        );
    }

}

