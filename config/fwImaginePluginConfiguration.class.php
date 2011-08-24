<?php

require_once __DIR__.'/../lib/listener/fwImagineListener.class.php';

class fwImaginePluginConfiguration extends sfPluginConfiguration
{
  public function configure()
  {
    $listener = new fwImagineListener($this->dispatcher);
    $this->dispatcher->connect('context.method_not_found', array($listener, 'listenToMethodNotFound'));
    $this->dispatcher->connect('view.method_not_found', array($listener, 'listenToMethodNotFound'));
    $this->dispatcher->connect('context.load_factories', array($listener, 'listenToContextLoadFactories'));
    $this->dispatcher->connect('fw_imagine.get_loaders', array($listener, 'listenToGetLoaders'));
  }
}
