<?php

/**
 * This file is part of frontend-block
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Frontend;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class FrontendBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    protected function assemble(array $config, SymfonyContainerBuilder $container)
    {
        $this->addTask('bldr_frontend.less', 'Bldr\Block\Frontend\Task\LessTask');
        $this->addTask('bldr_frontend.sass', 'Bldr\Block\Frontend\Task\SassTask');
        $this->addTask('bldr_frontend.coffee', 'Bldr\Block\Frontend\Task\CoffeeTask');
        $this->addTask('bldr_frontend.concat', 'Bldr\Block\Frontend\Task\ConcatTask');
        $this->addTask('bldr_frontend.minify.css', 'Bldr\Block\Frontend\Task\Minify\CssTask');
        $this->addTask('bldr_frontend.minify.js', 'Bldr\Block\Frontend\Task\Minify\JsTask');
    }
}
