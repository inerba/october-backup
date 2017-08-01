<?php
use Illuminate\Routing\Controller;

Route::get('/backend/inerba/backup/{filename}', function($filename){

    $user = BackendAuth::getUser();

    if ($user && ( $user->is_superuser || $user->hasPermission([
        'inerba.backup.download',
    ]))) {

        $filename = is_null( config('laravel-backup.backup.name') ) ? $filename : config('laravel-backup.backup.name') . '/' . $filename;

        // Download backup        
        $disk = \Storage::disk('sftp');
        $stream = $disk->readStream($filename);

        return \Response::stream(function() use($stream) {
            fpassthru($stream);
        }, 200, [
            "Content-Type" => $disk->getMimetype($filename),
            "Content-Length" => $disk->getSize($filename),
            "Content-disposition" => "attachment; filename=\"" . basename($filename) . "\"",
        ]);
        
    } else {
        throw new ApplicationException('Non sei autorizzato ad eseguire questo comando!');
    }

});