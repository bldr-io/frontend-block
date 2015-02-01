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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use CoffeeScript\Compiler;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CoffeeTask extends AbstractTask
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
            ->addParameter('src', true, 'Coffeescript files to compile')
            ->addParameter('dest', true, 'Destination to save to');
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $this->coffee = new Compiler();

        $source = $this->getParameter('src');
        $files = $this->getFiles($source);


        $this->compileFiles($output, $files, $this->getParameter('dest'));
    }

    /**
     * @param OutputInterface $output
     * @param SplFileInfo[]   $files
     * @param string          $destination
     */
    private function compileFiles(OutputInterface $output, array $files, $destination)
    {
        $code = '';
        foreach ($files as $file) {
            if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln("Compiling ".$file);
            }

            $code .= $file->getContents() . "\n";
        }

        $js = $this->coffee->compile($code, ['header' => false, 'bare' => true]);

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln("Writing to ".$destination);
        }

        $fs = new Filesystem;
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $js);
    }
}
