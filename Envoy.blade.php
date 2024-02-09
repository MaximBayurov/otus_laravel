@servers(['web' => ['maxim@192.168.0.159']])

@setup
    $projectRoot = '~/test/Laravel';
@endsetup

@story('deploy')
    sail-down
    update-code
    update-dependencies
    run-migrations
    key-generate
    run-tests
    sail-up
@endstory

@task('sail-down')
    cd {{ $projectRoot }}
    ./vendor/bin/sail stop
@endtask

@task('sail-up')
    cd {{ $projectRoot }}
    ./vendor/bin/sail up
@endtask

@task('update-code')
    cd {{ $projectRoot }}
    git pull origin {{ $branch }}
@endtask

@task('update-dependencies')
    cd {{ $projectRoot }}
    ./vendor/bin/sail composer update -W
@endtask

@task('run-migrations')
    cd {{ $projectRoot }}
    ./vendor/bin/sail artisan migrate
@endtask

@task('key-generate')
    cd {{ $projectRoot }}
    ./vendor/bin/sail artisan key:generate
@endtask

@task('run-tests')
    cd {{ $projectRoot }}
    ./vendor/bin/sail artisan test
@endtask
