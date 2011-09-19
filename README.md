fwImaginePlugin
===============

This plugin provide Imagine (https://github.com/avalanche123/Imagine) support for symfony 1.3/1.4.
Inspired from AvalancheImagineBundle for Symfony2

Warning
-------

This plugin is functional, but still experimental, so, it is only available on Github for the moment.
The stable version will be available by the symfony plugin manager.

Dependencies
------------

This plugin is designed to works with the fwClassLoaderPlugin, see https://github.com/frequence-web/fwClassLoaderPlugin

You can use fwImaginePlugin without the fwClassLoaderPlugin, but you need to implement an way to autoload Imagine classes (namespaced php5.3 classes)

Installation
------------

### Install imagine

    git clone https://github.com/avalanche123/Imagine.git lib/vendor/imagine

or

    git submodule add https://github.com/avalanche123/Imagine.git lib/vendor/imagine

### Install the plugin

    git clone https://github.com/frequence-web/fwImaginePlugin.git plugins/fwImaginePlugin

or

    git submodule add https://github.com/frequence-web/fwImaginePlugin.git plugins/fwImaginePlugin

Edit your config/ProjectConfiguration.class.php file and add this line in the setup method

    $this->enablePlugins('fwImaginePlugin');

### Install the Symfony2 ClassLoader component and the fwClassLoaderPlugin (Optional if you use your own autoload)

    git clone https://github.com/symfony/ClassLoader.git lib/vendor/symfony2/src/Symfony/ClassLoader
    ./symfony plugin:install fwClassLoader

Edit your config/ProjectConfiguration.class.php file and add this line in the setup method

    $this->enablePlugins('fwClassLoaderPlugin');

Usage
-----

### Define your filters

Create a fw_imagine.yml file inside config/ or apps/*/config/ dir and define your filters :

    all:
      filters:
        list_thumbnail:
          type: thumbnail
          options: { size: [120, 90], mode: inset }
        rotate90:
          type: rotate
          options: { angle: 90, color: 'FFFFFF', alpha: 0 }

### Use your filters

You can now use your filters into your templates

#### Display a filtered image

    <?php use_helper('imagine'); ?>
    <?php echo imagine_image('/web/path/to/image', 'list_thumbnail') ?>

#### Get a filtered image path

    <?php use_helper('imagine'); ?>
    <?php $path = imagine_filter('/web/path/to/image', 'list_thumbnail'); ?>

#### Available filter types

    thumbnail, rotate

Extra usage
-----------

### Define your own filter type

#### Create a filter loader

    <?php

    use Imagine\Image\Box;
    use Imagine\Image\ManipulatorInterface;
    use Imagine\Filter\Basic\Thumbnail;

    class fwImagineThumbnailLoader implements fwImagineLoader
    {
      public function load(array $options)
      {
        $mode = $options['mode'] === 'inset' ?
          ManipulatorInterface::THUMBNAIL_INSET :
          ManipulatorInterface::THUMBNAIL_OUTBOUND;

        list($width, $height) = $options['size'];

        return new Thumbnail(new Box($width, $height), $mode);
      }
    }

#### Listen the 'fw_imagine.get_loaders' event yo add the loader to the loaderManager

    $this->dispatcher->connect('fw_imagine.get_loaders', function(sfEvent $event) {
      $event->getSubject()->addLoader(new fwImagineThumbnailLoader());
    });
  

TODO
----

 * Unit tests
 * Functional tests
