<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompilerTag.class.php 5203 2007-03-07 08:58:21Z serega $
 * @package    macro
 */
lmb_require('limb/macro/src/lmbMacroException.class.php');
lmb_require('limb/macro/src/lmbMacroSourceLocation.class.php');

class lmbMacroNode
{
  protected $id;  
  protected $children = array();
  protected $parent;  
  /**
  * @var lmbMacroSourceLocation
  **/
  protected $location;    

  function __construct($location = null)
  {
    if($location)
      $this->location = $location;
    else
      $this->location = new lmbMacroSourceLocation();
  }
  
  function setParent($parent)
  {
    $this->parent = $parent;
  }
  
  function getLocationInTemplate()
  {
    return $this->location;
  }

  function getTemplateFile()
  {
    return $this->location->getFile();
  }

  function getTemplateLine()
  {
    return $this->location->getLine();
  }

  function getId()
  {
    if($this->id)
      return $this->id;
        
    $this->id = self :: generateNewId();
    return $this->id;
  }

  function setId($id)
  {
    $this->id = $id;
  }  
  
  static function generateNewId()
  {
    static $counter = 1;
    return 'id00' . $counter++;
  }    
  
  function raise($error, $vars = array())
  {    
    $vars['file'] = $this->location->getFile();
    $vars['line'] = $this->location->getLine();
    throw new lmbMacroException($error, $vars);
  }  
  
  function addChild($child)
  {
    $child->parent = $this;
    $this->children[] = $child;
  }

  function removeChild($id)
  {
    foreach(array_keys($this->children) as $key)
    {
      $child = $this->children[$key];
      if($child->getId() == $id)
      {
        unset($this->children[$key]);
        return $child;
      }
    }
  }

  function getChildren()
  {
    return $this->children;
  }

  function removeChildren()
  {
    foreach (array_keys($this->children) as $key)
    {
      $this->children[$key]->removeChildren();
      unset($this->children[$key]);
    }
  }

  function getChild($id)
  {
    if($child = $this->findChild($id))
      return $child;
    else
      $this->raise('Could not find component', array('id' => $id));
  }

  function findChild($id)
  {
    foreach(array_keys($this->children) as $key)
    {
      if($this->children[$key]->getId() == $id)
        return $this->children[$key];
      else
        return $this->children[$key]->findChild($id);          
    }
  }

  function findChildByClass($class)
  {
    foreach(array_keys($this->children) as $key)
    {
      if(is_a($this->children[$key], $class))
        return $this->children[$key];
      else
      {
        if($result = $this->children[$key]->findChildByClass($class))
          return $result;
      }
    }
  }

  function findChildrenByClass($class)
  {
    $ret = array();
    foreach(array_keys($this->children) as $key)
    {
      if(is_a($this->children[$key], $class))
        $ret[] = $this->children[$key];
      else
      {
        $more_children = $this->children[$key]->findChildrenByClass($class);
        if(count($more_children))
          $ret = array_merge($ret, $more_children);
      }
    }
    return $ret;
  }

  function findImmediateChildByClass($class)
  {
    foreach(array_keys($this->children) as $key)
    {
      if(is_a($this->children[$key], $class))
        return $this->children[$key];
    }
  }

  function findImmediateChildrenByClass($class)
  {
    $result = array();
    foreach(array_keys($this->children) as $key)
    {
      if(is_a($this->children[$key], $class))
        $result[] = $this->children[$key];
    }
    return $result;
  }  
  
  function findParentByClass($class)
  {
    $parent = $this->parent;

    while($parent && !is_a($parent, $class))
      $parent = $parent->parent;

    return $parent;
  }  
  
  function prepare()
  {
    foreach(array_keys($this->children) as $key)
      $this->children[$key]->prepare();
  }  

  function preParse(){}
  
  function generateConstructor($code_writer)
  {
    foreach(array_keys($this->children) as $key)
      $this->children[$key]->generateConstructor($code_writer);
  }

  function generateContents($code_writer)
  {
    foreach(array_keys($this->children) as $key)
      $this->children[$key]->generate($code_writer);
  }
  
  function generate($code_writer)
  {   
    $this->generateContents($code_writer);   
  }  
     
  /**
  * Checks that each immediate child of the current component has a unique ID
  * amongst its siblings.
  */
  function checkChildrenIds()
  {
    $child_ids = array();
    $checked_children = array();
    foreach($this->getChildren() as $key => $child)
    {
      $id = $child->getId();
      if (in_array($id, $child_ids))
      {
        $duplicate_child = $checked_children[$id];
        $child->raise('Duplicate "id" attribute',
                                   array('id' => $id,
                                         'duplicate_node_file' => $duplicate_child->getTemplateFile(),
                                         'duplicate_node_line' => $duplicate_child->getTemplateLine()));
      }
      else
      {
        $child_ids[] = $id;
        $checked_children[$id] = $child;
      }
    }
  }  
}
?>