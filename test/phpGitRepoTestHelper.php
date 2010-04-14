<?php

require_once dirname(__FILE__).'/../lib/phpGitRepo.php';

/**
 *
 * @param lime_test $t
 * @return phpGitRepo the git repo
 */
function _createTmpGitRepo(lime_test $t)
{
  $repoDir = sys_get_temp_dir().'/php-git-repo/'.uniqid();
  $t->ok(!is_dir($repoDir.'/.git'), $repoDir.' is not a Git repo');

  try
  {
    new phpGitRepo($repoDir);
    $t->fail($repoDir.' is not a valid git repository');
  }
  catch(InvalidArgumentException $e)
  {
    $t->pass($repoDir.' is not a valid git repository');
  }

  $t->comment('Create Git repo');
  exec('git init '. escapeshellarg($repoDir.'/.git'));
  $t->ok(is_dir($repoDir.'/.git'), $repoDir.' is a Git repo');

  $repo = new phpGitRepo($repoDir);
  $t->pass($repoDir.' is a valid git repo');

  $originalRepoDir = dirname(__FILE__).'/repo';
  foreach(array('README.markdown', 'index.php') as $file)
  {
    copy($originalRepoDir.'/'.$file, $repoDir.'/'.$file);
  }

  return $repo;
}