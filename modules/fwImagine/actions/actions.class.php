<?php

class fwImagineActions extends sfActions
{
  public function executeFilter(sfWebRequest $request)
  {
    // Get the filters
    $filters = $request->getParameter('filters');

    // Resolve paths
    $path = sfConfig::get('sf_web_dir').'/'.$request->getParameter('path');
    $destPath = sfConfig::get('sf_web_dir').'/media/cache/'.$filters.'/'.$request->getParameter('path');

    // Set image header
    // TODO : make it configurable
    $this->getResponse()->setContentType('image/png');
    $this->getResponse()->addCacheControlHttpHeader('max_age=31536000');

    // If the file doesn't exists
    if (!is_file($destPath))
    {
      // Create directory structure
      $destDir = dirname($destPath);
      if (!is_dir($destDir))
      {
        mkdir($destDir, 0777, true);
      }

      // Open source and apply filters
      $image = $this->getContext()->getImagine()->open($path);
      foreach (explode(',', $filters) as $filter)
      {
        $image = $this->getContext()->getImagineFilterManager()->get($filter)
                                    ->apply($image);
      }

      // Save (cache)
      $image->save($destPath, array('quality' => 100, 'format' => 'png'));

      // Send it
      $this->getResponse()->setContent($image->get('png'));
    }
    else
    {
      // Just send it
      $this->getResponse()->setContent(file_get_contents($destPath));
    }

    return sfView::NONE;
  }
}
