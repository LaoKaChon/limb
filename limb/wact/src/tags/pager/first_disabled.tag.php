<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: first_disabled.tag.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
* @tag pager:first:DISABLED
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerFirstDisabledTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePhp('if (' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->isFirst()) {');

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}

?>