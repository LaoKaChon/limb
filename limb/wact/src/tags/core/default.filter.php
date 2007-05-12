<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: default.filter.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
 * @filter default
 * @min_attributes 1
 * @max_attributes 1
 * @version $Id: default.filter.php 5873 2007-05-12 17:17:45Z serega $
 */
class WactDefaultFilter extends WactCompilerFilter
{
  function getValue()
  {
    if ($this->isConstant())
    {
      $value = $this->base->getValue();
      if (empty($value) && $value !== "0" && $value !== 0)
      {
        return $this->parameters[0]->getValue();
      } else {
        return $value;
      }
    }
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code_writer)
  {
    $code_writer->registerInclude('limb/wact/src/components/core/default_filter.inc.php');
    $code_writer->writePHP('WactApplyDefault(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(',');
    $this->parameters[0]->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}

?>
