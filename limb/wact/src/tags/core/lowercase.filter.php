<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lowercase.filter.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
 * @filter lowercase
 */
class WactLowerCaseFilter extends WactCompilerFilter {

  /**
   * Return this value as a PHP value
   * @return String
   */
  function getValue()
  {
    if ($this->isConstant())
      return strtolower($this->base->getValue());
    else
      $this->raiseUnresolvedBindingError();
  }

  /**
   * Generate the code to read the data value at run time
   * Must generate only a valid PHP Expression.
   * @param WactCodeWriter
   * @return void
   */
  function generateExpression($code_writer)
  {
    $code_writer->writePHP('strtolower(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }

}

?>
