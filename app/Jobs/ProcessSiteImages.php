<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;
class ProcessSiteImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $imagePaths;
    protected $type;

    public function __construct($model, array $imagePaths, $type = 'original')
    {
        $this->model = $model;
        $this->imagePaths = $imagePaths;
        $this->type = $type;
    }

    public function handle()
    {
        foreach ($this->imagePaths as $filePath) {
            $this->model->images()->create([
                'image' => $filePath,
                'type' => $this->type
            ]);
        }
    }
}
