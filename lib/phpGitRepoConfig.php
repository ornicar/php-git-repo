<?php

/**
 * Simple PHP wrapper for Git configuration
 *
 * @link      http://github.com/ornicar/php-git-repo
 * @version   1.3.0
 * @author    Moritz Schwoerer <moritz.schwoerer at gmail dot com>
 * @license   MIT License
 *
 * Documentation: http://github.com/ornicar/php-git-repo/blob/master/README.markdown
 * Tickets:       http://github.com/ornicar/php-git-repo/issues
 */
class phpGitRepoConfig
{
  const USER_NAME = 'user.name';
  const USER_EMAIL = 'user.email';
  
  /**
   * Holds the actual configuration
   * @var array
   */
  protected $configuration;
  
  /**
   * Holds the Git repository instance.
   * @var phpGitRepo
   */
  protected $repository;
  
  public function __construct(phpGitRepo $gitRepo)
  {
    $this->repository = $gitRepo;    
    $this->loadConfiguraiton();
  }
  
  /**
   * Load or reload the configuration
   */
  public function loadConfiguraiton()
  {
    $this->configuration = array_merge(parse_ini_string($this->repository->git('config -l')), parse_ini_string($this->repository->git('config --local -l')));
  }

  /**
   * Get a config option
   * 
   * @param string $configOption The config option to read
   * @param mixed  $fallback  Value will be returned, if $configOption is not set
   * 
   * @return string
   */
  public function get($configOption, $fallback = null)
  {
    print_r($this->configuration);
    return isset($this->configuration[$configOption]) ? $this->configuration[$configOption] : $fallback;
  }
  
  /**
   * Set or change a *repository* config option
   * 
   * @param string $configOption
   * @param mixed  $configValue 
   */
  public function set($configOption, $configValue)
  {
    $this->repository->git(sprintf('config --local %s %s', $configOption, $configValue));
    $this->loadConfiguraiton();
  }
  
  /**
   * Removes a option from local config
   * 
   * @param string $configOption 
   */
  public function remove($configOption)
  {
    $this->repository->git(sprintf('config --local --unset %s', $configOption));
  }
}
