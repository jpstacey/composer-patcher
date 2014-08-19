<?php


namespace Composer\Patcher;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

use Composer\Script\Event;

class PatcherPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        file_put_contents("/tmp/patcher.txt", "Yes");
    }
    public static function postPackageInstall(Event $event)
    {
        file_put_contents("/tmp/ppi.txt", "Yes");
    }
}
