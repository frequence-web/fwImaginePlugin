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
