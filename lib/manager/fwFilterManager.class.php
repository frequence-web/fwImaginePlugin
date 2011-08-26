<?php

class fwFilterManager
{
  protected $loaders = array();

  protected $filters;

  protected $dispatcher;

  public function __construct(sfEventDispatcher $dispatcher, array $filters = array())
  {
    $this->filters = $filters;
    $this->dispatcher = $dispatcher;
    $this->getLoaders();
  }

  public function get($filter)
  {
    if (!isset($this->filters[$filter]))
    {
      throw new InvalidArgumentException('The filter '.$filter.' does not exists.');
    }

    if (!isset($this->filters[$filter]['type']))
    {
      throw new InvalidArgumentException('You must provide a type for the '.$filter.' filter.');
    }

    if (!isset($this->loaders[$this->filters[$filter]['type']]))
    {
      throw new InvalidArgumentException('There is no loader for the filter '.$filter);
    }

    $options = isset($this->filters[$filter]['options']) ? $this->filters[$filter]['options'] : array();

    return $this->loaders[$this->filters[$filter]['type']]->load($options);
  }

  public function addLoader($filter, fwImagineLoader $loader)
  {
    $this->loaders[$filter] = $loader;
  }

  protected function getLoaders()
  {
    $event = new sfEvent($this, 'fw_imagine.get_loaders');
    $this->dispatcher->notify($event);
  }

  public function getFilters()
  {
    return $this->filters;
  }
}
