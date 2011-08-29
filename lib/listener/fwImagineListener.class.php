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
    $this->dispatcher->connect('context.method_not_found', array($this, 'listenToMethodNotFound'));
    $this->dispatcher->connect('view.method_not_found', array($this, 'listenToMethodNotFound'));
    $this->dispatcher->connect('context.load_factories', array($this, 'listenToContextLoadFactories'));
    $this->dispatcher->connect('fw_imagine.get_loaders', array($this, 'listenToGetLoaders'));
    $this->dispatcher->connect('routing.load_configuration', array($this, 'listenToRoutingLoadConfiguration'));
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
    $event->getSubject()->addLoader('rotate', new fwImagineRotateLoader);
  }

  public function listenToRoutingLoadConfiguration(sfEvent $event)
  {
    /** @var $routing sfPatternRouting */
    $routing = $event->getSubject();

    $routing->prependRoute('_imagine_filter', new sfRoute(
      '/_imagine/filter/:filters/:path',
      array('module' => 'fwImagine', 'action' => 'filter'),
      array('path' => '.+'),
      array('segment_separators' => array('/'))
    ));
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
    $this->config = $config = include $this->configCache->checkConfig('config/fw_imagine.yml');

    $this->imagine = new self::$adapters[$config['adapter']];

    $this->filterManager = new fwFilterManager($this->dispatcher, $config['filters']);

    unset($config['filters']);
    sfConfig::add(array_combine(
      array_map(function($key) { return 'fw_imagine_'.$key; }, array_keys($config)),
      array_values($config)
    ));

    return $this->imagine;
  }
}
