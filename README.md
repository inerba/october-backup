## Installiamo laravel-backup V3
https://docs.spatie.be/laravel-backup/v3/introduction

`composer require "spatie/laravel-backup:^3.0.0"`

### Pubblichiamo la configurazione
`php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"`

e la modifichiamo come `examples/laravel-backup.php`

## Installiamo il disco sftp
`composer require league/flysystem-sftp`

### copiamo il plugin che si trova in `src` dentro la cartella `plugins`

in 'app/filesystems.php' aggiungiamo un disco
```
'sftp' => [
            'driver' => 'sftp',
            'host' => 'xxx.xxx.xxx.xxx',
            'port' => 22,
            'username' => 'user',
            'password' => 'password',
            //'privateKey' => 'path/to/or/contents/of/privatekey',
            'root' => '/path/to/backup/container/folder',
            //'timeout' => 10,
            //'directoryPerm' => 0755
          ],       
```


## controlliamo che esista questa riga nel crontab
`* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`
