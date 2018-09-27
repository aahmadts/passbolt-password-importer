# Password Importer plugin for Passbolt

This is a CakePHP plugin for PassBolt to enable importing secrets/passwords to the community edition.

After importing the secrets/passwords a detailed downloadable report will appear with imported-, existed- secrets/passwords and errors.

The plugin is tested on the dockerized version _2.1.0-debian_ and _2.3.0-debian__. Check [Docker](./Dockerfile) for an example.


### Screenshots

![screenshots](https://raw.githubusercontent.com/aahmadts/general-styleguide/master/images/passbolt-password-importer-screenshot-1.0.2.png)

![report-screenshots](https://raw.githubusercontent.com/aahmadts/general-styleguide/master/images/passbolt-password-importer-screenshot-report.png)


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



### Load Plugin

After installing the plugin you will need to tell PassBolt to load the Plugin by extending the `config/bootstrap.php` with the following:

	Plugin::load('PassboltPasswordImporter', ['routes' => true]);

or if you have cake's console installed use:

    ./bin/cake plugin load aahmadts/PassboltPasswordImporter


For more info about CakePHP Plugins check here: https://book.cakephp.org/3.0/en/plugins.html

## Usage

To use the Password Importer run `https://your-domain/password-importer` and enjoy.
