<?php
use Illuminate\Routing\Controller;

Route::get('/backend/inerba/backup/{filename}', function($filename){

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

});