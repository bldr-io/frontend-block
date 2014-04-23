<?php

/**
 * This file is part of frontend-block
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Frontend\Call\Minify;

use Bldr\Call\AbstractCall;
use Bldr\Call\Traits\FinderAwareTrait;
use MatthiasMullie\Minify\CSS;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CssCall extends AbstractCall
{
    use FinderAwareTrait;

    public function configure()
    {
        $this->setName('minify:css')
            ->addOption('src', true, 'Files to Minify')
            ->addOption('dest', true, 'Destination to save minified css');
    }

    public function run()
    {
        $destination = $this->getOption('dest');

        /** @var SplFileInfo[] $files */
        $files = $this->getFiles($this->getOption('src'));

        $minifier = new CSS();
        foreach ($files as $file) {
            $minifier->add($file);
        }

        if ($this->getOutput()->isVerbose()) {
            $this->getOutput()->writeln("Writing to ".$destination);
        }
        (new Filesystem)->mkdir(dirname($destination));

        $minifier->minify($destination);
    }
}
