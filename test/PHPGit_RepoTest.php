<?php

require_once dirname(__FILE__) . '/vendor/lime.php';
require_once dirname(__FILE__) . '/PHPGit_RepoTestHelper.php';

$t = new lime_test();

$repo = _createTmpGitRepo($t);

$t->is($repo->git("branch"), '', '$repo->git("branch") returns nothing');

$t->is_deeply($repo->getBranches(), array(), 'No branches');

$t->is($repo->getCurrentBranch(), null, 'No current branch');

$t->is($repo->hasBranch('master'), false, 'No master branch');

try {
    $repo->git('checkout master');
    $t->fail('Can not checkout master');
} catch (RuntimeException $e) {
    $t->pass('Can not checkout master');
}

$repo->git('remote add origin git://github.com/ornicar/php-git-repo.git');

$repo->git('pull origin master');

$t->is_deeply($repo->getBranches(), array('master'), 'One branch master');

$t->is($repo->hasBranch('master'), true, 'master branch exists');

$t->is($repo->getCurrentBranch(), 'master', 'Current branch: master');

$repo->git('checkout -b other_branch');

$t->is_deeply($repo->getBranches(), array('master', 'other_branch'), 'Two branches, master and other_branch');

$t->is($repo->getCurrentBranch(), 'other_branch', 'Current branch: other_branch');

$t->is($repo->hasBranch('other_branch'), true, 'other_branch branch exists');

$repo->git('checkout master');

$t->is($repo->getCurrentBranch(), 'master', 'Current branch: master');

$t->comment('repeat "git " in the command string');
$repo->git('git checkout other_branch');

$t->is($repo->getCurrentBranch(), 'other_branch', 'Current branch: other_branch');

try {
    $repo->git('wtf');
    $t->fail('wtf is not a valid command');
} catch (RuntimeException $e) {
    $t->pass('wtf is not a valid command');
}

$t->comment('Use a valid git binary: /usr/bin/git');

$repo = _createTmpGitRepo($t, array('git_executable' => '/usr/bin/git'));

$t->comment('Use a invalid git binary: /usr/bin/git-foobar');

try {
    $repo = _createTmpGitRepo($t, array('git_executable' => '/usr/bin/git-foobar'));
    $repo->git('status');
    $t->fail('/usr/bin/git-foobar is not a valid git binary');
} catch (RuntimeException $e) {
    $t->pass('/usr/bin/git-foobar is not a valid git binary');
}

$repoDir = sys_get_temp_dir() . '/php-git-repo/' . uniqid();
mkdir($repoDir);
try {
    $repo = PHPGit_Repository::create($repoDir);
    $t->pass('Create a new Git repository in filesystem');
} catch (InvalidArgumentException $e) {
    $t->fail($e->getMessage());
}

$repo = _createTmpGitRepo($t);

$config = $repo->getConfiguration();

$t->ok($config->get('core.editor', true));
$config->set('core.editor', 'nano');
$t->is($config->get('core.editor'), 'nano');
$t->is($config->get('core.editor'), 'nano');
$config->remove('core.editor');
$t->ok($config->get('core.editor', true));


file_put_contents($repo->getDir() . '/README', 'No, finally, do not read me.');
$repo->git('add README');
$repo->git('commit -m "Add README"');
unlink($repo->getDir() . '/README');
$repo->git('rm README');
$repo->git('commit -m "Remove README"');

$log = $repo->getCommits(7);
$t->ok(is_array($log));
$t->is(count($log), 2);
$commit = $log[0];
$t->ok(is_array($commit));
$t->is($commit['message'], 'Remove README');

$t->is($commit['author']['name'], $config->get(PHPGit_Configuration::USER_NAME));
$t->is($commit['commiter']['name'], $config->get(PHPGit_Configuration::USER_NAME));
$commit = $log[1];
$t->is($commit['message'], 'Add README');

$t->is_deeply($repo->getTags(), array(), 'No tags');
$repo->git('tag -am "tag 1" first_tag');
$repo->git('tag -am "tag 2" second_tag');
$t->is_deeply($repo->getTags(), array('first_tag', 'second_tag'), '2 tags');

// cloneUrl
$repoDir = sys_get_temp_dir() . '/php-git-repo/' . uniqid();
try {
    $repo = PHPGit_Repository::cloneUrl('https://github.com/ornicar/php-git-repo.git', $repoDir);
    $t->pass('Create a new Git repository in filesystem');
    $t->is($repo->getCurrentBranch(), 'master', 'Current branch: master');

} catch (InvalidArgumentException $e) {
    $t->fail($e->getMessage());
}
