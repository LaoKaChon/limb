<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

require_once('limb/wact/src/tags/fetch/WactBaseFetchingTag.class.php');

/**
* @tag fetch
* @req_const_attributes using to
*/

class WactFetchTag extends WactBaseFetchingTag
{
  protected $runtimeComponentName = 'WactFetchComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/fetch/WactFetchComponent.class.php';

  function generateBeforeContent($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->setFetcherName("' . $this->getAttribute('using') .'");');

    $code->writePhp($this->getComponentRefCode() . '->setIncludePath("' . $this->getAttribute('include') .'");');

    if($this->hasAttribute('cache_dataset') && !$this->getBoolAttribute('cache_dataset'))
    {
      $code->writePhp($this->getComponentRefCode() . '->setCacheDataset(false);');
    }

    parent :: generateBeforeContent($code);
  }
}

?>