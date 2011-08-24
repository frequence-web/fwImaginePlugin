<?php

class fwImagineActions extends sfActions
{
  public function executeFilter(sfWebRequest $request)
  {
    $filter = $request->getParameter('filter');
    $path = sfConfig::get('sf_test_dir').'/data/image-file.jpg';
    $destPath = sfConfig::get('sf_upload_dir').'/image-file.jpg';

    try
    {
      $this->getContext()->getImagineFilterManager()->get($filter)
                         ->apply($this->getContext()->getImagine()->open($path))
                         ->save($destPath, array('quality' => 100));

      $this->getResponse()->setContentType('image/jpg');

      return $this->renderText(file_get_contents($destPath));
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
}
