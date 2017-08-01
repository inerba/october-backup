<?php

namespace Inerba\Backup\Providers;

use League\Flysystem\Sftp\SftpAdapter;
use Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;

/**
 * Class SftpServiceProvider
 * @package App\Providers
 * 
 * composer require league/flysystem-sftp "~1.0"
 * 
 * Add this file to the Providers directory and update your app.php config
 * 
 * Sample config to put in filesystems.php config:
 * 'sftp' => [
 *      'driver' => 'sftp',
 *      'host' => 'example.com',
 *      'port' => 22,
 *      'username' => 'username',
 *      'password' => 'password',
 *      'privateKey' => 'path/to/or/contents/of/privatekey',
 *      // 'root' => '/path/to/root',
 *      // 'timeout' => 10,
 *      // 'directoryPerm' => 0755
 *  ],
 */
class SftpServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('sftp', function($app, $config) {
            unset($config['driver']);

            foreach($config as $key => $value) {
                if(!strlen($value)) {
                    unset($config[$key]);
                }
            }

            $adapter = new SftpAdapter($config);

            return new Filesystem($adapter);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}