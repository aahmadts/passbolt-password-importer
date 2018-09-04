# PasswordImporter plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).


### Composer
The recommended way to install composer packages is:

```
composer require aahmadts/passbolt-password-manager
```

### Manually Installing a Plugin

Download PasswordImporter into the `passbolt/plugins` folder, then let composer know about it by adding the following into _composer.json_

    {
        "autoload": {
            "psr-4": {
                "PassboltPasswordImporter\\": "plugins/passbolt-password-manager/src/"
            }
        }
    }

After that run this composer command

    php composer.phar dumpautoload

If plugin is installed correctly, it should be possible to be used without other changes as composer loads CakePHP plugins automatically.

But if for some reason it did not work, you can tell PassBolt to load the Plugin by extending the `config/bootstrap.php` with the following:

	Plugin::load('PassboltPasswordImporter', ['routes' => true]);

ref: https://book.cakephp.org/3.0/en/plugins.html#creating-your-own-plugins

## Usage

To use the Password Importer run `/password-importer` and enjoy.
