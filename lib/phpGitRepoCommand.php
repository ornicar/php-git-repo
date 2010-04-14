<?php

class phpGitRepoCommand
{
  protected $dir;

  protected $command;

  public function __construct($dir, $command)
  {
    $this->dir      = $dir;
    $this->command  = $command;
  }

  public function run()
  {
    
  }
}