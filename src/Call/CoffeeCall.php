<?php

/**
 * This file is part of frontend-block
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Frontend\Call;

use Bldr\Call\AbstractCall;
use Bldr\Call\Traits\FinderAwareTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use CoffeeScript\Compiler;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CoffeeCall extends AbstractCall
{
    use FinderAwareTrait;

    /**
     * @var Compiler $coffee
     */
    private $coffee;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('coffee')
            ->setDescription('Compiles the `src` coffee files')
            ->addOption('src', true, 'Coffeescript files to compile')
            ->addOption('dest', true, 'Destination to save to');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->coffee = new Compiler();

        $source = $this->getOption('src');
        $files = $this->getFiles($source);

        $this->compileFiles($files, $this->getOption('dest'));
    }

    /**
     * @param SplFileInfo[] $files
     * @param string        $destination
     */
    private function compileFiles(array $files, $destination)
    {
        $code = '';
        foreach ($files as $file) {
            if ($this->getOutput()->isVerbose()) {
                $this->getOutput()->writeln("Compiling ".$file);
            }

            $code .= $file->getContents() . "\n";
        }

        $output = $this->coffee->compile($code);

        if ($this->getOutput()->isVerbose()) {
            $this->getOutput()->writeln("Writing to ".$destination);
        }

        $fs = new Filesystem;
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $output);
    }
}
