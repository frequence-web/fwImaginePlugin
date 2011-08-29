<?php

class fwImagineActions extends sfActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $filters = $request->getParameter('filters');
    $path = sfConfig::get('sf_web_dir').'/'.$request->getParameter('path');
    $destPath = sfConfig::get('sf_web_dir').'/media/cache/'.$filters.'/'.$request->getParameter('path');

    try
    {
      $this->getResponse()->setContentType('image/png');
      $this->getResponse()->addCacheControlHttpHeader('max_age=31536000');
      
      if (!is_file($destPath))
      {
        $destDir = dirname($destPath);
        if (!is_dir($destDir))
        {
          mkdir($destDir, 0777, true);
        }

        $image = $this->getContext()->getImagine()->open($path);

        foreach (explode(',', $filters) as $filter)
        {
          $image = $this->getContext()->getImagineFilterManager()->get($filter)
                                      ->apply($image);
        }

        ob_start();

        $image->show('png')
              ->save($destPath, array('quality' => 100, 'format' => 'png'));

        $this->getResponse()->setContent(ob_get_clean());

        return sfView::NONE;
      }

      $this->getResponse()->setContent(file_get_contents($destPath));

      return sfView::NONE;
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
}
