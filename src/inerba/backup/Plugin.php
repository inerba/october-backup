<?php namespace Inerba\Backup;

use App;
use Backend;
use System\Classes\PluginBase;

/**
 * Backup Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Backup',
            'description' => 'No description provided yet...',
            'author'      => 'Inerba',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        App::register(\Spatie\Backup\BackupServiceProvider::class);
        App::register('Inerba\Backup\Providers\SftpServiceProvider');

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Inerba\Backup\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'inerba.backup.download' => [
                'tab' => 'Backup',
                'label' => 'Download backup'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'backup' => [
                'label'       => 'Backup',
                'url'         => Backend::url('inerba/backup/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['inerba.backup.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'Inerba\Backup\ReportWidgets\DownloadBackup' => [
                'label'   => 'Backup',
                'context' => 'dashboard'
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $name = date('Y-m-d-His').'.auto-db.zip';
        $name_full = date('Y-m-d-His').'.auto-full.zip';

        if( \Config::get('inerba.backup::enable_schedule') ) {

            $schedule->command('backup:clean')->daily()->at('02:30');
            $schedule->command('backup:run --only-db --filename='.$name)->daily()->at('02:00');
            $schedule->command('backup:run --filename='.$name_full)->weekly()->at('03:00');

        }
    }
}
