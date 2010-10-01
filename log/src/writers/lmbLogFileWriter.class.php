<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/log/src/writers/lmbLogBaseWriter.class.php');

/**
 * class lmbLogFileWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogFileWriter extends lmbLogBaseWriter
{
  protected $log_file;

  function __construct(lmbUri $dsn)
  {
    $this->log_file = $dsn->getPath();
    parent::__construct($dsn);
  }

  protected function _write(lmbLogEntry $entry)
  {
    $this->_appendToFile($this->getLogFile(), $entry->toString(), $entry->getTime());
  }

  protected function _appendToFile($file_name, $message, $stamp)
  {
    lmbFs :: mkdir(dirname($file_name), 0775);
    $file_existed = file_exists($file_name);

    if($fh = fopen($file_name, 'a'))
    {
      @flock($fh, LOCK_EX);
      fwrite($fh, $message);
      @flock($fh, LOCK_UN);
      fclose($fh);
      if(!$file_existed)
        chmod($file_name, 0664);
    }
    else
    {
      throw new lmbFsException("Cannot open log file '$file_name' for writing" . PHP_EOL .
                               "The web server must be allowed to modify the file." . PHP_EOL .
                               "File logging for '$file_name' is disabled.");
    }
  }

  function getLogFile()
  {
    return $this->log_file;
  }
}


