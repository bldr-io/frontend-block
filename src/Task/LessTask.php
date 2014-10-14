<?php

/**
 * This file is part of frontend-block
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Frontend\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Block\Core\Task\Traits\FinderAwareTrait;
use Less_Parser;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class LessTask extends AbstractTask
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
            ->addParameter('src', true, 'Less files to compile')
            ->addParameter('dest', true, 'Destination to save to')
            ->addParameter('compress', false, 'Should bldr remove whitespace and comments')
            ->addParameter('sourceMap', false, 'Should bldr create a source map')
            ->addParameter(
                'sourceMapWriteTo',
                false,
                'Where should bldr write to? If this isn\'t set, it will be written to the compiled file.'
            )
            ->addParameter('sourceMapURL', false, 'Url to use for the source map');
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {

        $this->less = new Less_Parser($this->getLessOptions());

        $source = $this->getParameter('src');
        $files = $this->getFiles($source);

        $this->compileFiles($output, $files, $this->getParameter('dest'));
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getLessOptions()
    {
        $options = [];
        if ($this->getParameter('compress') === true) {
            $options['compres'] = true;
        }

        if ($this->hasParameter('sourceMap')) {
            $options['sourceMap'] = $this->getParameter('sourceMap');
        }

        if ($this->hasParameter('sourceMapWriteTo')) {
            $options['sourceMapWriteTo'] = $this->getParameter('sourceMapWriteTo');
        }

        if ($this->hasParameter('sourceMapURL')) {
            $options['sourceMapURL'] = $this->getParameter('sourceMapURL');
        }

        return $options;
    }

    /**
     * @param OutputInterface $output
     * @param SplFileInfo[]   $files
     * @param string          $destination
     */
    private function compileFiles(OutputInterface $output, array $files, $destination)
    {
        foreach ($files as $file) {
            if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln("Compiling ".$file);
            }
            $this->less->parseFile($file);
        }

        $css = $this->less->getCss();

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln("Writing to ".$destination);
        }

        $fs = new Filesystem;
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $css);
    }
}
