MatuckMenuBundle
================

The MatuckMenu provides an easy to create and manage menus for your application.



## Installation

MatuckMenuBundle uses Composer, please checkout the [composer website](http://getcomposer.org) for more information.

The simple following command will install `matuckmenubundle` into your project. It also add a new
entry in your `composer.json` and update the `composer.lock` as well.

This bundle requires Assetic and Doctrine Extensions.
It also has a client requirement for jQuery.  Without jQuery you will not be able to reorder menus.
IT is predesigned to work with bootstrap.

```bash
$ composer require matuck/menubundle
```
##Configuration

Add the below entries to AppKernel.php  
```
new matuck\MenuBundle\matuckMenuBundle()
```

In app/config/config.yml add a mapping entry like below.
```
doctrine:
    orm:
        entity_managers:
            default:
                mappings:
                    matuckMenuBundle: ~
```

### Setup Doctrine Extensions
Follow the below linked instructions.  The main extension we need is tree.
[Doctrine Extensions Setup](http://symfony.com/doc/current/bundles/StofDoctrineExtensionsBundle/index.html)

### Setup Assetic
Follow the below linked instructions.
[Assetic Setup](http://symfony.com/doc/current/cookbook/assetic/asset_management.html)

## Getting Started
Add the routes to your application.  In your routing.yml add.  You can change the prefix,
but I suggest you make it something that is secured by your firewall
```
matuck_menu:
    resource: "@matuckMenuBundle/Controller/"
    type:     annotation
    prefix:   /admin/menu
```

Update the database to include the new entities
```
php bin/console doctrine:schema:update --force
```

## What now?
Build the menus in the panels.  In your template where you want to display the menu put
```
{{ matuck_menu_render('main') }}
{{ matuck_menu_render('main', 'matuckMenuBundle::bootstrapmenuright.html.twig') }}
```

Main is the name of the root menu you want to show.
The Second parameter can be deleted but allows you to pass a template file for rendering the menu.
The bundle includes two template files.  bootstrapmenu.html.twig and bootstrapmenuright.html.twig.
By creating your own template and passing it you can design the menus however you want.
