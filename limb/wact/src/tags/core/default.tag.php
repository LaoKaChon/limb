<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: default.tag.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
 * Output a portion of the template if DBE property has a value at runtime
 * @tag core:DEFAULT
 * @req_const_attributes for
 */
class WactCoreDefaultTag extends WactCompilerTag
{
  protected $DBE;

  function prepare()
  {
    $this->DBE = new WactDataBindingExpressionNode($this->getAttribute('for'), $this);
    $this->DBE->prepare();

    parent::prepare();
  }

  function generateTagContent($code)
  {
    $tempvar = $code->getTempVariable();
    $this->DBE->generatePreStatement($code);
    $code->writePHP('$' . $tempvar . ' = ');
    $this->DBE->generateExpression($code);
    $code->writePHP(';');
    $this->DBE->generatePostStatement($code);

    $code->writePHP('if (is_scalar($' . $tempvar .' )) $' . $tempvar . ' = trim($' . $tempvar . ');');
    $code->writePHP('if (empty($' . $tempvar . ')) {');

    parent :: generateTagContent($code);

    $code->writePHP('}');
  }
}
?>