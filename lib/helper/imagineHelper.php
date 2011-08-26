<?php

function imagine_filter($path, $filter)
{
  return str_replace(
    urlencode(ltrim($path, '/')),
    urldecode(ltrim($path, '/')),
    url_for('_imagine_filter', array('path' => ltrim($path, '/'), 'filter' => $filter))
  );
}

function imagine_image($source, $filter, $options = array())
{
  return image_tag(imagine_filter($source, $filter, $options));
}
