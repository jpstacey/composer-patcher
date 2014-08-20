composer-patcher
================

Plugin to patch composer downloads post-hoc.

Arguably you wouldn't want to do this, if you could avoid it. But it's the
way that a lot of existing drupal.org patch workflow happens (especially
via Drush make) and so this provides a useful transition technology.

Minimum `composer.json`
-----------------------

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:jpstacey/composer-patcher.git"
        }
    ],
    "require": {
        "jpstacey/composer-patcher": "*@dev"
    },
    "scripts": {
        "post-package-install": "Composer\\Patcher\\PatcherPlugin::postPackageInstall"
    }
}
```

The "scripts" is required in your root `composer.json` as it will not run in
a subsidiary `composer.json` for security reasons.

Example `composer.json`
-----------------------

Downloads and patches a Drupal module:

```json
{
    "repositories": {
        "composer-patcher": {
            "type": "vcs",
            "url": "git@github.com:jpstacey/composer-patcher.git"
        },
        "xmlsitemap": {
            "type": "package",
            "package": {
                "name": "drupal/xmlsitemap",
                "type": "drupal-module",
                "version": "2.0-rc2",
                "dist": {
                    "url": "http://ftp.drupal.org/files/projects/xmlsitemap-7.x-2.0-rc2.tar.gz",
                    "type": "tar"
                },
                "extra": {
                    "patch": [
                        "https://drupal.org/files/include_inc_file-1392710.patch"
                    ]
                }
            }
        }
    },
    "require": {
        "jpstacey/composer-patcher": "*@dev",
        "drupal/xmlsitemap": "2.0-rc2"
    },
    "scripts": {
        "post-package-install": "Composer\\Patcher\\PatcherPlugin::postPackageInstall"
    }
}
```
