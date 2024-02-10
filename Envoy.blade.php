@servers(['web' => 'maxim@192.168.0.159'])

@story('deploy')
    update-code
    update-dependencies
    run-migrations
    run-tests
@endstory

@error
    if ($task === 'run-tests') {
        rollback-migrations
        rollback-code
        update-dependencies
    }
@enderror

@task('update-code')
    cd {{ $projectRoot }}
    git pull origin-with-creds {{ $branch }}
@endtask

@task('rollback-code')
    cd {{ $projectRoot }}
    git pull origin-with-creds master -r
@endtask

@task('update-dependencies')
    cd {{ $projectRoot }}
    ./vendor/bin/sail composer update -W
@endtask

@task('run-migrations')
    cd {{ $projectRoot }}
    ./vendor/bin/sail artisan migrate
@endtask

@task('rollback-migrations')
    cd {{ $projectRoot }}
    ./vendor/bin/sail artisan migrate:rollback
@endtask

@task('run-tests')
    cd {{ $projectRoot }}
    ./vendor/bin/sail artisan test
@endtask
