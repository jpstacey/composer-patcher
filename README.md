composer-patcher
================

Plugin to patch composer downloads post-hoc

Minimum viable `composer.json` to currently use:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:jpstacey/composer-patcher.git"
        }
    ],
    "require": {
        "jpstacey/composer-patcher": "*"
    },
    "minimum-stability": "dev",
    "scripts": {
        "post-package-install": "Composer\\Patcher\\PatcherPlugin::postPackageInstall"
    }
}
```
