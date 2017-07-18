<?php namespace Inerba\Backup\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Artisan;
use Flash;
use Lang;
use Storage;
use Response;

class DownloadBackup extends ReportWidgetBase
{
    protected $defaultAlias = 'inerba_backup';
	
    public function render(){
        $this->vars['files'] = $this->read_files();
        return $this->makePartial('widget');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'Titolo del widget',
                'default'           => 'Backup',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
            'max_items' => [
                'title'             => 'Backup in elenco',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Solo numeri!',
                'default'           => '10',
            ],
        ];
    }

    public function onBackupDb(){
        $name = date('Y-m-d-His').'.database.zip';
        try {
            traceLog('Backup manuale del database');
            Artisan::call('backup:run',
                [
                    '--only-db' => true,
                    '--filename' => $name,
                ]
            );
        } catch (Exception $e) {
            Response::make($e->getMessage(), 500);
        }

        Flash::success('Backup creato con successo!');
        
        return [
            'partial' => $this->makePartial(
                'widget',
                [
                    'files'  => $this->read_files(),
                ]
            )
        ];

    }

    public function onBackupClean(){
        try {
            traceLog('Pulizia dei backup non necessari');
            Artisan::call('backup:clean');
        } catch (Exception $e) {
            Response::make($e->getMessage(), 500);
        }

        Flash::success('Pulizia effettuata!');
        
        return [
            'partial' => $this->makePartial(
                'widget',
                [
                    'files'  => $this->read_files(),
                ]
            )
        ];

    }

    private function formatSize($size) {
        $mod = 1024;
        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    private function read_files(){

        $disk = Storage::disk('sftp');

        $folder = config('laravel-backup.backup.name');

        $files = array_reverse($disk->files($folder));

        if(empty($files)) return [];

        $n=0;
        foreach ($files as $file) {

            $filename = basename($file);
            $type = explode('.',$filename);

            $backup[] = [
                'filename' => $filename,
                'type' => $type[1],
                'size' => $this->formatSize($disk->getSize($file)),
                'date' => date('d/m/Y H:i:s',$disk->lastModified($file)),
            ];
            $n++;
            if($n >= $this->property('max_items')) break;
        }
        
        return $backup;
        
    }

}
