<?php


namespace Composer\Patcher;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

use Composer\Script\Event;

class PatcherPlugin implements PluginInterface
{
    /**
     * Implements PluginInterface::activate().
     *
     * Currently does nothing: postPackageInstall() below does heavy lifting.
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * Implements scripts class method postPackageInstall().
     *
     * After package install, interrogates patch JSON array and applies.
     */
    public static function postPackageInstall(Event $event)
    {
        // Obtain install path, so as to patch within it.
        $package = $event->getOperation()->getPackage();
        $installation_manager = $event->getComposer()->getInstallationManager();
        $install_path = $installation_manager->getInstaller($package->getType())->getInstallPath($package);
    }
}
