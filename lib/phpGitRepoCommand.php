<?php

class phpGitRepoCommand
{
  protected $dir;

  protected $commandString;

  protected $debug;

  public function __construct($dir, $commandString, $debug)
  {
    $commandString = trim($commandString);

    // Add git prefix if missing
    if(0 !== strncmp($commandString, 'git ', 4))
    {
      $commandString = 'git '.$commandString;
    }

    $this->dir            = $dir;
    $this->commandString  = $commandString;
    $this->debug          = $debug;
  }

  public function run()
  {
    $commandToRun = sprintf('cd %s && %s', escapeshellarg($this->dir), $this->commandString);

    if($this->debug)
    {
      print $commandToRun."\n";
    }

    ob_start();
    passthru($commandToRun, $returnVar);
    $output = ob_get_clean();
    
    if($this->debug)
    {
      print $output."\n";
    }

    if(0 !== $returnVar)
    {
      throw new RuntimeException(sprintf(
        'Command %s failed with code %s: %s',
        $commandToRun,
        $returnVar,
        $output
      ), $returnVar);
    }

    return $output;
  }
}