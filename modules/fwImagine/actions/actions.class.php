<?php

class fwImagineActions extends sfActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $filter = $request->getParameter('filter');
    $path = sfConfig::get('sf_web_dir').'/'.$request->getParameter('path');
    $destPath = sfConfig::get('sf_web_dir').'/media/cache'.$request->getParameter('path');

    try
    {
      if (!is_file($destPath))
      {
        $destDir = dirname($destPath);
        if (!is_dir($destDir))
        {
          mkdir($destDir, 0777, true);
        }

        $this->getContext()->getImagineFilterManager()->get($filter)
                           ->apply($this->getContext()->getImagine()->open($path))
                           ->save($destPath, array('quality' => 100, 'format' => 'png'));
      }
      
      $this->getResponse()->setContentType('image/png');
      $this->getResponse()->addCacheControlHttpHeader('max_age=31536000');
      $this->getResponse()->setContent(file_get_contents($destPath));

      return sfView::NONE;
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
}
