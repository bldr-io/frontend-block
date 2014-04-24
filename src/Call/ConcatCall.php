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
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ConcatCall extends AbstractCall
{
    use FinderAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('concat')
            ->setDescription('Concatenates the src files into a dest')
            ->addOption('src', true, 'The file(s) to concatenate')
            ->addOption('dest', true, 'The filename and path to save the concatenated file')
            ->addOption('banner', false, 'Banner to place at the top of the concatenated file')
            ->addOption('separator', true, 'The separator to use between files', "\n");
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $destination = $this->getOption('dest');
        $files = $this->getFiles($this->getOption('src'));

        $content = '';
        foreach ($files as $file) {
            $fileContents = $this->getFileContents($file);
            $content .= $fileContents . $this->getOption('separator');
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
