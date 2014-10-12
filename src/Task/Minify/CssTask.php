<?php

/**
 * This file is part of frontend-block
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Frontend\Task\Minify;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Block\Core\Task\Traits\FinderAwareTrait;
use MatthiasMullie\Minify\CSS;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CssTask extends AbstractTask
{
    use FinderAwareTrait;

    public function configure()
    {
        $this->setName('minify:css')
            ->setDescription('Minified the src files into the dest')
            ->addParameter('src', true, 'Files to Minify')
            ->addParameter('dest', true, 'Destination to save minified css');
    }

    /**
     * {@inheritdoc}
     */
    public function run(OutputInterface $output)
    {
        $destination = $this->getParameter('dest');

        /** @var SplFileInfo[] $files */
        $files = $this->getFiles($this->getParameter('src'));

        $minifier = new CSS();
        foreach ($files as $file) {
            $minifier->add($file);
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln("Writing to ".$destination);
        }
        (new Filesystem)->mkdir(dirname($destination));

        $minifier->minify($destination);
    }
}
