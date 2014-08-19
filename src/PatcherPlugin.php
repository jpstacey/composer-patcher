<?php


namespace Patcher;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class PatcherPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        file_put_contents("/tmp/patcher.txt", "Yes");
    }
}