<?php


namespace Composer\Patcher;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

use Composer\Script\Event;
use Composer\Util\RemoteFileSystem;

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
        $manager = $event->getComposer()->getInstallationManager();
        $installPath = $manager->getInstaller($package->getType())->getInstallPath($package);

        // Get list of patch URLs from "extra": { "patch": [ ... ]}.
        $extraData = $package->getExtra();
        if (!isset($extraData["patch"]) || !count($extraData["patch"]))
        {
            return;
        }

        // Descend into install directory, loop over patches and apply.
        $currentDir = getcwd();
        chdir($installPath);
        $downloader = new RemoteFilesystem($event->getIO(), $event->getComposer()->getConfig());
        foreach ($extraData["patch"] as $patchUrl) {
          self::getAndApplyPatch($downloader, $patchUrl);
        }
        chdir($currentDir);
    }

    /**
     * Apply a remote patch on code in the current directory.
     */
    private static function getAndApplyPatch($downloader, $patchUrl)
    {
        $hostname = parse_url($patchUrl, PHP_URL_HOST);
        $downloader->copy($hostname, $patchUrl, "/tmp/patch", TRUE);
    }
}
