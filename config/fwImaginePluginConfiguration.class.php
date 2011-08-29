<?php

require_once __DIR__.'/../lib/listener/fwImagineListener.class.php';

class fwImaginePluginConfiguration extends sfPluginConfiguration
{
  public function configure()
  {
    new fwImagineListener($this->dispatcher);
  }
}
