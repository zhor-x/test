@servers(['web' => 'ghazarqn@driveandgo.help])

@task('queues', ['on' => 'web'])
cd /home/g/ghazarqn/driveandgo.help
php artisan queue:work
@endtask
