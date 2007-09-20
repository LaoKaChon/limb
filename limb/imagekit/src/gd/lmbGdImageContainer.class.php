<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package imagekit
 * @version $Id$
 */
lmb_require(dirname(__FILE__).'/../lmbAbstractImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../exception/lmbImageTypeNotSupportException.class.php');
lmb_require(dirname(__FILE__).'/../exception/lmbImageCreateFailedException.class.php');
lmb_require(dirname(__FILE__).'/../exception/lmbImageSaveFailedException.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * GD image container
 *
 * @package imagekit
 * @version $Id$
 */
class lmbGdImageContainer extends lmbAbstractImageContainer
{
  protected static $gd_types = array(
    'gif' => IMG_GIF,
    //'jpg' => IMG_JPG,
    'jpeg' => IMG_JPG,
    'png' => IMG_PNG,
    'wbmp' => IMG_WBMP
  );
  protected static $lookup_types = array(
    IMAGETYPE_GIF => 'gif',
    IMAGETYPE_JPEG => 'jpeg',
    IMAGETYPE_PNG => 'png',
    IMAGETYPE_WBMP => 'wbmp'
  );

  protected $img;
  protected $img_type;
  protected $pallete;

  function load($file_name, $type = '')
  {
    $imginfo = @getimagesize($file_name);
    if(!$imginfo) throw new lmbFileNotFoundException($file_name);
    if(!$type) $type = self::convertImageType($imginfo[2]);
    if(!self::supportLoadType($type)) throw new lmbImageTypeNotSupportException($type);
    $createfunc = 'imagecreatefrom'.$type;
    if(!($this->img = @$createfunc($file_name)))
        throw new lmbImageCreateFailedException($file_name);
    $this->img_type = $type;
  }

  function save($file_name = null, $type = '')
  {
    if(!$type) $type = $this->img_type;
    if(!self::supportSaveType($type)) throw new lmbImageTypeNotSupportException($type);
    $imagefunc = 'image'.$type;
    if(!@$imagefunc($this->img, $file_name))
        throw new lmbImageSaveFailedException($file_name);
    $this->destroyImage();
  }

  function getResource()
  {
    return $this->img;
  }

  function replaceResource($img)
  {
    imagedestroy($this->img);
    $this->img = $img;
  }

  function isPallete()
  {
    return !imageistruecolor($this->img);
  }

  function getWidth()
  {
    return imagesx($this->img);
  }

  function getHeight()
  {
    return imagesy($this->img);
  }

  function destroyImage()
  {
    if(!$this->img) return;
    imagedestroy($this->img);
    $this->img = null;
  }

  static function supportLoadType($type)
  {
    return self::supportType($type);
  }

  static function supportSaveType($type)
  {
    return self::supportType($type);
  }

  static function supportType($type)
  {
    if(!function_exists('imagetypes')) return false;
    $gdtype = self::getGdType($type);
    if($gdtype === false) return false;
    return (boolean)(imagetypes() & $gdtype);
  }

  static function getGdType($type)
  {
    return isset(self::$gd_types[$type]) ? self::$gd_types[$type] : false;
  }

  static function convertImageType($imagetype)
  {
    if(!isset(self::$lookup_types[$imagetype]))
    {
      $type = function_exists('image_type_to_extension') ? image_type_to_extension($imagetype) : '';
        throw new lmbImageTypeNotSupportException($type);
    }
    return self::$lookup_types[$imagetype];
  }

  function __destruct()
  {
  	$this->destroyImage();
  }
}
?>