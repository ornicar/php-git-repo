<?php

require_once dirname(__FILE__).'/vendor/lime.php';
require_once dirname(__FILE__).'/phpGitRepoTestHelper.php';

$t = new lime_test();

$repo = _createTmpGitRepo($t);