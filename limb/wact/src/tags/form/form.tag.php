<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form.tag.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
 * Compile time component for building runtime WactFormComponents
 * @tag form
 * @suppress_attributes children_reuse_runat from
 * @runat client
 * @restrict_self_nesting
 */
class WactFormTag extends WactRuntimeDatasourceComponentHTMLTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';
  protected $runtimeComponentName = 'WactFormComponent';

  /**
   * Returns the identifying server ID. It's value it determined in the
   * following order;
   * <ol>
   * <li>The XML wact:id attribute in the template if it exists</li>
   * <li>The XML id attribute in the template if it exists</li>
   * <li>The XML name attribute in the template if it exists</li>
   * <li>The value of $this->ServerId</li>
   * <li>An ID generated by the generateNewServerId() function</li>
   * </ol>
   */
  function getServerId()
  {
    if($this->hasAttribute('wact:id'))
      return $this->getAttribute('wact:id');

    if($this->hasAttribute('id'))
      return $this->getAttribute('id');

    if($this->hasAttribute('name'))
      return $this->getAttribute('name');

    if (!empty($this->ServerId))
      return $this->ServerId;

    $this->ServerId = self :: generateNewServerId();
    return $this->ServerId;
  }

  function generateBeforeOpenTag($code_writer)
  {
    $code_writer->writePHP($this->getComponentRefCode() . '->prepare();');
  }

  function generateBeforeCloseTag($code_writer)
  {
    $code_writer->writePHP($this->getComponentRefCode() . '->renderState();');
  }
}
?>
