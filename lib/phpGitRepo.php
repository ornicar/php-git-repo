<?php

/**
 * Include the command class
 */
require_once(dirname(__FILE__).'/phpGitRepoCommand.php');

/**
 * Simple PHP wrapper for Git repository
 *
 * @link      http://github.com/ornicar/php-git-repo
 * @version   1.3.0
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

    /**
     * @var boolean Whether to enable debug mode or not
     * When debug mode is on, commands and their output are displayed
     */
    protected $debug;

    /**
     * @var array of options
     */
    protected $options;

    protected static $defaultOptions = array(
        'command_class'   => 'phpGitRepoCommand', // class used to create a command
        'git_executable'  => '/usr/bin/git'       // path of the executable on the server
    );

    /**
     * Instanciate a new Git repository wrapper
     *
     * @param   string $dir real filesystem path of the repository
     * @param   boolean $debug
     * @param   array $options
     */
    public function __construct($dir, $debug = false, array $options = array())
    {
        $this->dir      = $dir;
        $this->debug    = $debug;
        $this->options  = array_merge(self::$defaultOptions, $options);

        $this->checkIsValidGitRepo();
    }

    /**
     * Create a new Git repository in filesystem, running "git init"
     * Returns the git repository wrapper
     *
     * @param   string $dir real filesystem path of the repository
     * @param   boolean $debug
     * @param   array $options
     * @return phpGitRepo
     **/
    public static function create($dir, $debug = false, array $options = array())
    {
        $options = array_merge(self::$defaultOptions, $options);
        $commandString = $options['git_executable'].' init';
        $command = new $options['command_class']($dir, $commandString, $debug);
        $command->run();

        $repo = new self($dir, $debug, $options);

        return $repo;
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

        foreach(explode("\n", $this->git('branch')) as $branchLine) {
            if('*' === $branchLine{0}) {
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
     * Get tags list
     *
     * @return array list of tag names
     */
    public function getTags()
    {
        $output = $this->git('tag');
        return $output ? array_filter(explode("\n", $output)) : array();
    }

    /**
     * Return the result of `git log` formatted in a PHP array
     *
     * @return array list of commits and their properties
     **/
    public function getCommits($nbCommits = 10)
    {
        $dateFormat = 'iso';
        $format = '"%H|%T|%an|%ae|%ad|%cn|%ce|%cd|%s"';
        $output = $this->git(sprintf('log -n %d --date=%s --format=format:%s', $nbCommits, $dateFormat, $format));
        $commits = array();
        foreach(explode("\n", $output) as $line) {
            $infos = explode('|', $line);
            $commits[] = array(
                'id' => $infos[0],
                'tree' => $infos[1],
                'author' => array(
                    'name' => $infos[2],
                    'email' => $infos[3]
                ),
                'authored_date' => $infos[4],
                'commiter' => array(
                    'name' => $infos[5],
                    'email' => $infos[6]
                ),
                'committed_date' => $infos[7],
                'message' => $infos[8]
            );
        }

        return $commits;
    }

    /**
     * Check if a directory is a valid Git repository
     */
    public function checkIsValidGitRepo()
    {
        if(!is_dir($this->dir.'/.git')) {
            throw new InvalidGitRepositoryDirectoryException($this->dir.' is not a valid Git repository');
        }
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
        // clean commands that begin with "git "
        $commandString = preg_replace('/^git\s/', '', $commandString);

        $commandString = $this->options['git_executable'].' '.$commandString;

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

class InvalidGitRepositoryDirectoryException extends InvalidArgumentException
{
}
