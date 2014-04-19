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
use Less_Parser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class LessCall extends AbstractCall
{
    use FinderAwareTrait;

    /**
     * @var Less_Parser $less
     */
    private $less;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('less')
            ->setDescription('Compiles the `src` less files')
            ->addOption('src', true, 'Source to watch')
            ->addOption('dest', true, 'Destination to save to')
            ->addOption('compress', false, 'Should bldr remove whitespace and comments')
            ->addOption('sourceMap', false, 'Should bldr create a source map')
            ->addOption(
                'sourceMapWriteTo',
                false,
                'Where should bldr write to? If this isn\'t set, it will be written to the compiled file.'
            )
            ->addOption('sourceMapUrl', false, 'Url to use for the sourcemap');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {

        $this->less = new Less_Parser($this->getLessOptions());

        $source = $this->getOption('src');
        $files = $this->findFiles($source);

        $this->compileFiles($files, $this->getOption('dest'));
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getLessOptions()
    {
        $options = [];
        if ($this->getOption('compress') === true) {
            $options['compres'] = true;
        }

        if ($this->hasOption('sourceMap')) {
            $options['sourceMap'] = $this->getOption('sourceMap');
        }

        if ($this->hasOption('sourceMapWriteTo')) {
            $options['sourceMapWriteTo'] = $this->getOption('sourceMapWriteTo');
        }

        if ($this->hasOption('sourceMapUrl')) {
            $options['sourceMapUrl'] = $this->getOption('sourceMapUrl');
        }

        return $options;
    }

    /**
     * @param SplFileInfo[] $files
     * @param string        $destination
     */
    private function compileFiles(array $files, $destination)
    {
        $content = '';
        foreach ($files as $file) {
            if ($this->getOutput()->isVerbose()) {
                $this->getOutput()->writeln("Compiling ".$file);
            }
            $this->less->parseFile($file);
        }

        $output = $this->less->getCss();

        if ($this->getOutput()->isVerbose()) {
            $this->getOutput()->writeln("Writing to ".$destination);
        }
        $fs = new Filesystem;
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $output);
    }
}
