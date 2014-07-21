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
use SassParser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class SassCall extends AbstractCall
{
    use FinderAwareTrait;

    /**
     * @var SassParser $sass
     */
    private $sass;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('sass')
            ->setDescription('Compiles the `src` sass/scss files')
            ->addOption('src', true, 'Sass/Scss files to compile')
            ->addOption('dest', true, 'Destination to save to')
            ->addOption('compress', false, 'Should bldr remove whitespace and comments');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->sass = new SassParser($this->getSassOptions());

        $source = $this->getOption('src');
        $files  = $this->getFiles($source);

        $this->compileFiles($files, $this->getOption('dest'));
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getSassOptions()
    {
        $options = [];
        if ($this->getOption('compress') === true) {
            $options['style'] = \SassRenderer::STYLE_COMPRESSED;
        }

        return $options;
    }

    /**
     * @param SplFileInfo[] $files
     * @param string        $destination
     */
    private function compileFiles(array $files, $destination)
    {
        $fileSet = [];
        foreach ($files as $file) {
            if ($this->getOutput()->isVerbose()) {
                $this->getOutput()->writeln("Compiling ".$file);
            }
            $fileSet[] = (string) $file;
        }

        $output = $this->sass->toCss($fileSet);

        if ($this->getOutput()->isVerbose()) {
            $this->getOutput()->writeln("Writing to ".$destination);
        }

        $fs = new Filesystem;
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $output);
    }
}
