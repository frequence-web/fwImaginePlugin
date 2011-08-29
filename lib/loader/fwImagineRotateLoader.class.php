<?php

use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Imagine\Filter\Basic\Rotate;
use Imagine\Image\Color;

class fwImagineRotateLoader implements fwImagineLoader
{
  public function load(array $options)
  {
    $angle = $options['angle'];
    $color = isset($options['color']) ? $options['color'] : 0xFFFFFF;
    $alpha = isset($options['alpha']) ? $options['alpha'] : 0;

    return new Rotate($angle, new Color($color, $alpha));
  }
}
