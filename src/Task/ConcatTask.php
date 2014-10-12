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

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ConcatTask extends AbstractTask
{
    use FinderAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('concat')
            ->setDescription('Concatenates the src files into a dest')
            ->addParameter('src', true, 'The file(s) to concatenate')
            ->addParameter('dest', true, 'The filename and path to save the concatenated file')
            ->addParameter('banner', false, 'Banner to place at the top of the concatenated file')
            ->addParameter('separator', true, 'The separator to use between files', "\n");
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $destination = $this->getParameter('dest');
        $files = $this->getFiles($this->getParameter('src'));

        $content = '';
        foreach ($files as $file) {
            $fileContents = $this->getFileContents($file);
            $content .= $fileContents . $this->getParameter('separator');
        }

        $fs = new Filesystem();
        $fs->mkdir(dirname($destination));
        $fs->dumpFile($destination, $content);
    }

    /**
     * @param SplFileInfo $file
     *
     * @return string
     */
    private function getFileContents(SplFileInfo $file)
    {
        $content = $file->getContents();
        if ($file->getExtension() === 'js' && substr($content, -1) !== ';') {
            $content .= ';';
        }

        return $content;
    }
}
