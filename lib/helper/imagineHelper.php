<?php

function imagine_filter($path, $filters)
{
  $filters = join(',', (array)$filters);
  
  return str_replace(
    urlencode(ltrim($path, '/')),
    urldecode(ltrim($path, '/')),
    url_for('_imagine_filter', array('path' => ltrim($path, '/'), 'filters' => $filters))
  );
}

function imagine_image($source, $filter, $options = array())
{
  return image_tag(imagine_filter($source, $filter, $options));
}
