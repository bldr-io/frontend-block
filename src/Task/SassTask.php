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
use SassParser;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class SassTask extends AbstractTask
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
            ->addParameter('src', true, 'Sass/Scss files to compile')
            ->addParameter('dest', true, 'Destination to save to')
            ->addParameter('compress', false, 'Should bldr remove whitespace and comments');
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $this->sass = new SassParser($this->getSassOptions());

        $source = $this->getParameter('src');
        $files  = $this->getFiles($source);

        $this->compileFiles($output, $files, $this->getParameter('dest'));
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function getSassOptions()
    {
        $options = [];
        if ($this->getParameter('compress') === true) {
            $options['style'] = \SassRenderer::STYLE_COMPRESSED;
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
        $fileSet = [];
        foreach ($files as $file) {
            if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln("Compiling ".$file);
            }
            $fileSet[] = (string) $file;
        }

        $output = $this->sass->toCss($fileSet);

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln("Writing to ".$destination);
        }

        $fs = new Filesystem;
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $output);
    }
}
