<?php

/**
 * Simple PHP wrapper for Git repository
 *
 * @link      http://github.com/ornicar/php-git-repo
 * @version   1.0
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 *
 * Documentation: http://github.com/ornicar/php-git-repo/blob/master/README.markdown
 * Tickets:       http://github.com/ornicar/php-git-repo/issues
 */

class phpGitRepo
{
  /**
   * @var string  local repository directory
   */
  protected $dir;

  public function __construct($dir)
  {
    if(!$this->isValidGitRepo($dir))
    {
      throw new InvalidArgumentException($dir.' is not a valid Git repository');
    }
  }

  /**
   * Check is a directory is a valid Git repository
   *
   * @param   string    $dir  the repository directory
   * @return  boolean         true if $dir is a valid Git repo, false otherwise
   */
  public function isValidGitRepo($dir)
  {
    return is_dir($dir.'/.git');
  }
}