<?php

class fwImagineListener
{
  /**
   * @var \Imagine\Image\ImagineInterface
   */
  protected $imagine;

  /**
   * @var fwFilterManager
   */
  protected $filterManager;

  /**
   * @var sfConfigCache
   */
  protected $configCache;

  /**
   * @var array
   */
  protected $config;

  /**
   * @var sfEventDispatcher
   */
  protected $dispatcher;

  protected static $adapters = array(
    'gd'      => 'Imagine\\Gd\\Imagine',
    'Imagick' => 'Imagine\\Imagick\\Imagine',
    'Gmagick' => 'Imagine\\Gmagick\\Imagine',
  );

  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  public function listenToMethodNotFound(sfEvent $event)
  {
    if($event['method'] == 'getImagine')
    {
      $event->setProcessed(true);
      $event->setReturnValue($this->getImagine());
    }
    else if ($event['method'] == 'getImagineFilterManager')
    {
      $event->setProcessed(true);
      $event->setReturnValue($this->getImagineFilterManager());
    }
  }

  public function listenToContextLoadFactories(sfEvent $event)
  {
    $this->configCache = $event->getSubject()->getConfigCache();
  }

  public function listenToGetLoaders(sfEvent $event)
  {
    $event->getSubject()->addLoader('thumbnail', new fwImagineThumbnailLoader);
  }

  /**
   * @return Imagine\Image\ImagineInterface
   */
  public function getImagine()
  {
    return null !== $this->imagine ? $this->imagine : $this->loadImagine();
  }

  /**
   * @return Imagine\Image\ImagineInterface
   */
  public function getImagineFilterManager()
  {
    if (null === $this->filterManager)
    {
      $this->loadImagine();
    }

    return $this->filterManager;
  }

  protected function loadImagine()
  {
    $this->config = include $this->configCache->checkConfig('config/fw_imagine.yml');

    $this->imagine = new self::$adapters[$this->config['adapter']];

    $this->filterManager = new fwFilterManager($this->dispatcher, $this->config['filters']);

    return $this->imagine;
  }
}
