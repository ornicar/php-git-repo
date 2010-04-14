# PHP Git Repo

Manage a Git repository with PHP.

## Instanciate a phpGitRepo

    $repo = new phpGitRepo('/path/to/the/git/repo');

## Run git commands

Any git command can be run:

    // change current branch to master
    $repo->run('checkout master');

    // add a remote repo
    $repo->run('remote add origin git://github.com/ornicar/php-git-repo.git');

The run() method returns the output string:

    echo $repo->run('log --oneline');
    // e30b70b Move test repo to system tmp dir, introduce phpGitRepoCommand
    // 01fabb1 Add test repo
    // 12a95e6 Add base class with basic unit test
    // 58e7769 Fix readme
    // c14c9ec Initial commit

The run() method throws a RuntimeException if the command is invalid:

    $repo->run('wtf'); // this git command does NOT exist
    // throws RuntimeException

## Get branches informations

Get the branches list:

    $branches = $repo->getBranches();
    // returns array('master', 'other_branch')

Get the current branch:

    $branch = $repo->getCurrentBranch();
    // returns 'master'

Know if the repo has a given branch:

    $hasBranch = $repo->hasBranch('master');
    // returns true