<?php


namespace Composer\Patcher;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

use Composer\Script\Event;
use Composer\Util\RemoteFileSystem;
use Composer\Util\ProcessExecutor;

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

    // Class static properties.
    public static $io;
    public static $executor;

    /**
     * Implements scripts class method postPackageInstall().
     *
     * After package install, interrogates patch JSON array and applies.
     */
    public static function postPackageInstall(Event $event)
    {
        // Store some objects from the event on this class.
        self::$io = $event->getIO();

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

        $downloader = new RemoteFilesystem(self::$io, $event->getComposer()->getConfig());
        foreach ($extraData["patch"] as $patchUrl) {
          self::getAndApplyPatch($downloader, $installPath, $patchUrl);
        }
    }

    /**
     * Apply a remote patch on code in the specified directory.
     */
    private static function getAndApplyPatch(RemoteFilesystem $downloader, $project_directory, $patchUrl)
    {
        // Generate random (but not cryptographically so) filename.
        $filename = uniqid("/tmp/") . ".patch";
        throw new \Exception("Not yet cleaning up patch files");
        throw new \Exception("Not yet keeping record of patches");

        // Download file from remote filesystem to this location.
        $hostname = parse_url($patchUrl, PHP_URL_HOST);
        $downloader->copy($hostname, $patchUrl, $filename, TRUE);

        // Modified from drush6:make.project.inc
        $patched = FALSE;
        $patch_levels = array('-p1', '-p0');

        foreach ($patch_levels as $patch_level)
        {
            $checked = self::executeCommand('cd %s && GIT_DIR=. git apply --check %s %s --verbose', $project_directory, $patch_level, $filename);
            if ($checked)
            {
                // Apply the first successful style.
                $patched = self::executeCommand('cd %s && GIT_DIR=. git apply %s %s --verbose', $project_directory, $patch_level, $filename);
                break;
            }   
        }   

        // In some rare cases, git will fail to apply a patch, fallback to using
        // the 'patch' command.
        if (!$patched)
        {
            foreach ($patch_levels as $patch_level)
            {
                // --no-backup-if-mismatch here is a hack that fixes some
                // differences between how patch works on windows and unix.
                if ($patched = self::executeCommand("patch %s --no-backup-if-mismatch -d %s < %s", $patch_level, $project_directory, $filename))
                {
                  break;
                }   
            }
        }

        if (!$patched)
        {
            throw new \Exception("Cannot apply patch $patchUrl");
        }
    }

    /**
     * Execute a shell command with escaping.
     */
    private static function executeCommand($cmd)
    {
        // Get a process executor for running command lines below.
        if (!isset(self::$executor))
        {
            self::$executor = new ProcessExecutor(self::$io);
        }

        // Shell-escape all arguments except the command.
        $args = func_get_args();
        for ($x = 1; $x < sizeof($args); $x++)
        {
            $args[$x] = escapeshellarg($args[$x]);
        }
        // And replace the arguments.
        $command = call_user_func_array('sprintf', $args);

        return (self::$executor->execute($command) == 0);
    }
}
