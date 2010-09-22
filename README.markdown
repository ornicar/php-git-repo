# PHP Git Repo

Manage a Git repository with PHP.
Provide an object oriented wrapper to run any Git command.

## Requirements

- PHP >= 5.2 (PHP 5.3 works fine)
- Git >= 1.5

## Instanciate a phpGitRepo

    $repo = new phpGitRepo('/path/to/the/git/repo');

It does NOT create a Git repo, but a PHP object to manipulate an existing Git repo.

## Create a Git repository

If the Git repository does not exist yet on filesystem, phpGitRepo can create it for you.

    $repo = phpGitRepo::create('/path/to/the/git/repo');

It runs `git init` and returns a phpGitRepo object.

## Run git commands

git commands can be run with the same syntax as in the CLI. Some examples:

    // change current branch to master
    $repo->git('checkout master');

    // pull from a remote
    $repo->git('pull origin master');

    // add a remote repo
    $repo->git('remote add origin git://github.com/ornicar/php-git-repo.git');

There is no limitation, you can run any git command.

The git() method returns the output string:

    echo $repo->git('log --oneline');

    e30b70b Move test repo to system tmp dir, introduce phpGitRepoCommand
    01fabb1 Add test repo
    12a95e6 Add base class with basic unit test
    58e7769 Fix readme
    c14c9ec Initial commit

The git() method throws a RuntimeException if the command is invalid:

    $repo->git('wtf'); // this git command does NOT exist: throw RuntimeException

## Get branches informations

Some shortcut methods are provided to deal with branches in a convenient way.

### Get the branches list:

    $branches = $repo->getBranches();
    // returns array('master', 'other_branch')

### Get the current branch:

    $branch = $repo->getCurrentBranch();
    // returns 'master'

### Know if the repo has a given branch:

    $hasBranch = $repo->hasBranch('master');
    // returns true

## Debug mode

`phpGitRepo` constructor second parameter lets you enable debug mode.
When debug mode is on, commands and their output are displayed.

    $repo = new phpGitRepo('/path/to/the/git/repo', true);

## Configure

`phpGitRepo` can be configured by passing an array of options to the constructor third parameter.

### Change git executable path

You may need to provide the path to the git executable.

    $repo = new phpGitRepo('/path/to/the/git/repo', false, array('git_executable' => '/usr/bin/git'));

On most Unix system, it's `/usr/bin/git`. On Windows, it may be `C:\Program Files\Git\bin`.

### Change the command class

By default, `phpGitRepo` will use `phpGitRepoCommand` class to implement Git commands.
By replacing this option, you can use your own command implementation:

    $repo = new phpGitRepo('/path/to/the/git/repo', false, array('command_class' => 'myGitCommand'));

## Run test suite

All code is fully unit tested. To run tests on your server, from a CLI, run

    php /path/to/php-git-repo/prove.php
