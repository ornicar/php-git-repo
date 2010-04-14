<?php

require_once dirname(__FILE__).'/vendor/lime.php';
require_once dirname(__FILE__).'/../lib/phpGitRepo.php';

$t = new lime_test();

$nonExistingDir = '/non/existing/directory';
$message = $nonExistingDir.' is not a valid git repository';
try
{
  new phpGitRepo($nonExistingDir);
  $t->fail($message);
}
catch(InvalidArgumentException $e)
{
  $t->pass($message);
}