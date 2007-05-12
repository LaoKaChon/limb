<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactRuntimeDatasourceComponentHTMLTag.class.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

require_once('limb/wact/src/compiler/tag_node/WactRuntimeComponentHTMLTag.class.php');

class WactRuntimeDatasourceComponentHTMLTag extends WactRuntimeComponentHTMLTag
{
  function generateBeforeContent($code_writer)
  {
    parent :: generateBeforeContent($code_writer);

    if($this->hasAttribute('from'))
      $this->generateRegisterDatasource($code_writer, $this->getAttribute('from'));
  }

  function generateRegisterDatasource($code_writer, $from)
  {
    $from_dbe = new WactDataBindingExpressionNode($from, $this->parent);

    $from_dbe->generatePreStatement($code_writer);

    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataSource(');
    $from_dbe->generateExpression($code_writer);
    $code_writer->writePHP(');');

    $from_dbe->generatePostStatement($code_writer);
  }

  function getDataSource()
  {
    return $this;
  }

  function isDataSource()
  {
    return TRUE;
  }
}
?>