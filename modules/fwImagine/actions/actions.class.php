<?php

class fwImagineActions extends sfActions
{
  public function executeFilter(sfWebRequest $request)
  {
    // Get the filters
    $filters = $request->getParameter('filters');

    // Resolve paths
    $path = $request->getParameter('path');

    $destPath = sprintf(
      '%s/%s/%s/%s',
      sfConfig::get('sf_web_dir'),
      sfConfig::get('fw_imagine_cache_prefix', 'media/cache'),
      $filters,
      $request->getParameter('path')
    );

    // Set image header
    // TODO : make it configurable
    $this->getResponse()->setContentType('image/png');
    if (sfConfig::get('fw_imagine_http_cache_enabled', true))
    {
      $this->getResponse()->addCacheControlHttpHeader('max-age='.sfConfig::get('fw_imagine_http_cache_lifetime', 3600 * 24 * 365));
    }
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
