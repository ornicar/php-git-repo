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

require_once(dirname(__FILE__).'/phpGitRepoCommand.php');

class phpGitRepo
{
  /**
   * @var string  local repository directory
   */
  protected $dir;

  protected $debug;

  protected $options = array(
    'command_class' => 'phpGitRepoCommand'
  );

  /**
   * Instanciate a new HTML Writer
   *
   * @param   array $options
   */
  public function __construct($dir, $debug = false, array $options = array())
  {
    $this->dir      = $dir;
    $this->debug    = $debug;
    $this->options  = array_merge($this->options, $options);

    $this->checkIsValidGitRepo();
  }

  /**
   * Get branches list
   *
   * @return array list of branches names
   */
  public function getBranches()
  {
    return array_filter(preg_replace('/[\s\*]/', '', explode("\n", $this->git('branch'))));
  }

  /**
   * Get current branch
   *
   * @return string the current branch name
   */
  public function getCurrentBranch()
  {
    $output = $this->git('branch');

    foreach(explode("\n", $this->git('branch')) as $branchLine)
    {
      if('*' === $branchLine{0})
      {
        return substr($branchLine, 2);
      }
    }
  }

  /**
   * Tell if a branch exists
   *
   * @return  boolean true if the branch exists, false otherwise
   */
  public function hasBranch($branchName)
  {
    return in_array($branchName, $this->getBranches());
  }

  /**
   * Check if a directory is a valid Git repository
   */
  public function checkIsValidGitRepo()
  {
    if(!is_dir($this->dir.'/.git'))
    {
      throw new InvalidArgumentException($this->dir.' is not a valid Git repository');
    }

    $this->git('status');
  }

  /**
   * Run any git command, like "status" or "checkout -b mybranch origin/mybranch"
   *
   * @throws  RuntimeException
   * @param   string  $commandString
   * @return  string  $output
   */
  public function git($commandString)
  {
    $command = new $this->options['command_class']($this->dir, $commandString, $this->debug);

    return $command->run();
  }

  /**
   * Get the repository directory
   *
   * @return  string  the repository directory
   */
  public function getDir()
  {
    return $this->dir;
  }
}