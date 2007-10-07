<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroExpressionInterface.interface.php');

/**
 * class lmbMacroFilter
 * @package macro
 * @version $Id$
 */
abstract class lmbMacroFilter implements lmbMacroExpressionInterface
{
  protected $base;
  protected $params;

  function __construct($base)
  {
    $this->base = $base;
  }

  function preGenerate($code)
  {
    $this->base->preGenerate($code);
  }

  function setParams($params)
  {
    $this->params = $params;
  }
}

