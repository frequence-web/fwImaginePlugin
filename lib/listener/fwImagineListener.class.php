<?php

class fwImagineListener
{
  /**
   * @var \Imagine\Image\ImagineInterface
   */
  protected $imagine;

  /**
   * @var sfConfigCache
   */
  protected $configCache;

  /**
   * @var array
   */
  protected $config;

  protected static $adapters = array(
    'gd'      => 'Imagine\\Gd\\Imagine',
    'Imagick' => 'Imagine\\Imagick\\Imagine',
    'Gmagick' => 'Imagine\\Gmagick\\Imagine',
  );

  public function listenToMethodNotFound(sfEvent $event)
  {
    if($event['method'] == 'getImagine')
    {
      $event->setProcessed(true);
      $event->setReturnValue($this->getImagine());
    }
  }

  public function listenToContextLoadFactories(sfEvent $event)
  {
    $this->configCache = $event->getSubject()->getConfigCache();
  }

  public function getImagine()
  {
    return null !== $this->imagine ? $this->imagine : $this->loadImagine();
  }

  protected function loadImagine()
  {
    $this->config = include $this->configCache->checkConfig('config/fw_imagine.yml');

    $this->imagine = new self::$adapters[$this->config['adapter']];

    return $this->imagine;
  }
}
