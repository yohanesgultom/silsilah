{{-- 

Checklists:
✓ supervisor is installed $server
✓ $data_dir is available $server
✓ .env is available inside $data_dir/.env in $server
✓ $server user is a sudoers that requires NO password
✓ $server has registered git repo public key as known server (try git clone from inside $server once) 

Running:

envoy run deploy --server=user@server

 --}}

@servers(['web' => $server])

@setup
    $repository = 'git@github.com:nafiesl/silsilah';
    $releases_dir = '$HOME/silsilah/releases';
    $app_dir = '/var/www/silsilah'; // symlink to public dir in current release
    $data_dir = '$HOME/silsilah/data';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository    
    run_composer
    update_symlinks
    refresh_database
    {{-- migrate_database --}}
    {{-- setup_supervisor --}}
    set_permissions
    clear_cache
    update_release_symlinks
    clean_old_releases    
@endstory

@task('ensure_paths')
    echo 'Ensuring paths exist'
    mkdir -p {{ $releases_dir }}
    mkdir -p {{ $data_dir }}
@endtask

@task('clone_repository')
    echo 'Cloning repository'
    [ -d {{ $releases_dir }} ] || mkdir -p {{ $releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
@endtask

@task('setup_supervisor')
    echo 'Setup supervisor'
    sudo cp {{ $new_release_dir }}/*-worker.conf /etc/supervisor/conf.d/
    sudo supervisorctl reread
    sudo supervisorctl update
@endtask

@task('run_composer')
    echo "Starting deployment ({{ $release }})"
    cd {{ $new_release_dir }}
    {{-- composer install --prefer-dist --no-scripts -q -o --no-dev --}}
    composer install
    composer dump-autoload
@endtask

@task('set_permissions')
    echo "Setting permissions"
    sudo chown -R www-data:www-data {{ $new_release_dir }}/bootstrap
    sudo chown -R www-data:www-data {{ $new_release_dir }}/storage
    sudo chown -R www-data:www-data {{ $data_dir }}/storage
    {{-- sudo chown -R www-data:www-data {{ $new_release_dir }}/tmp --}}
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    [ -d {{ $data_dir }}/storage ] || cp -R {{ $new_release_dir }}/storage {{ $data_dir }}
    rm -rf {{ $new_release_dir }}/storage    
    ln -nfs {{ $data_dir }}/storage {{ $new_release_dir }}/storage

    echo "Linking public storage directory"
    rm -rf {{ $new_release_dir }}/public/storage
    ln -nfs {{ $data_dir }}/storage/app/public {{ $new_release_dir }}/public/storage

    echo 'Linking .env file'
    ln -nfs {{ $data_dir }}/.env {{ $new_release_dir }}/.env

    echo 'Linking tmp dir'    
    rm -Rf {{ $new_release_dir }}/tmp
    ln -nfs {{ $data_dir }}/tmp {{ $new_release_dir }}/tmp
@endtask

@task('update_release_symlinks')
    echo 'Linking current release'
    sudo ln -nfs {{ $new_release_dir }} {{ $releases_dir }}/current
    sudo ln -nfs {{ $new_release_dir }}/public {{ $app_dir }}
@endtask

@task('refresh_database')
    {{-- Refresh database. Only for development --}}
    echo "Refreshing database"
    rm -Rf {{ $data_dir }}/storage/app/public/* || true
    cd {{ $new_release_dir }}
    {{-- php artisan migrate:fresh --}}
    sudo -u www-data php artisan migrate:fresh --seed
    {{-- php artisan passport:install --}}
    {{-- php artisan queue:flush --}}
@endtask

@task('migrate_database')    
    echo "Migrate database"
    cd {{ $new_release_dir }}
    php artisan migrate
@endtask


@task('clear_cache')    
    echo "Clearing cache"
    cd {{ $new_release_dir }}
    sudo -u www-data php artisan cache:clear
    sudo -u www-data php artisan config:cache
    sudo -u www-data php artisan route:cache
    sudo -u www-data php artisan view:cache
    {{-- sudo -u www-data php artisan queue:restart --}}
    {{-- echo "Restarting supervisor worker"
    sudo supervisorctl restart all --}}
@endtask

@task('clean_old_releases')
    {{-- This will list our releases by modification time and delete all but the 4 most recent. --}}
    purging=$(ls -dt {{ $releases_dir }}/* | tail -n +5);

    if [ "$purging" != "" ]; then
        echo Purging old releases: $purging;
        sudo rm -rf $purging;
    else
        echo "No releases found for purging at this time";
    fi
@endtask
