<?php

namespace Phlow\Expression;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class SimpleLanguage
 * Extends Symfony Expression Language to include
 * 1) empty
 * @package Phlow\Expression
 */
class SimpleLanguage extends ExpressionLanguage
{
    public function __construct(CacheItemPoolInterface $cache = null, $providers = array())
    {
        parent::__construct($cache, $providers);
        $this->addFunction(new ExpressionFunction(
            'empty',
            function ($val) {
                return sprintf('empty(%1$s)', $val);
            },
            function (array $args, $val) {
                return empty($val);
            }
        ));
    }
}
