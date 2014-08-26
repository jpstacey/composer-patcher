composer-patcher
================

Plugin to patch composer downloads post-hoc.

Arguably you wouldn't want to do this, if you could avoid it. But it's the
way that a lot of existing drupal.org patch workflow happens (especially
via Drush make) and so this provides a useful transition technology.

Minimum `composer.json`
-----------------------

The package is now registered on Packagist:

https://packagist.org/packages/jpstacey/composer-patcher

so you should only require the following minimum JSON:

```json
{
    "require": {
        "jpstacey/composer-patcher": "*"
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
        "jpstacey/composer-patcher": "*",
        "drupal/xmlsitemap": "2.0-rc2"
    },
    "scripts": {
        "post-package-install": "Composer\\Patcher\\PatcherPlugin::postPackageInstall"
    }
}
```
