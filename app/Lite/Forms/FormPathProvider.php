<?php namespace App\Lite\Forms;

trait FormPathProvider
{
    public function getFormPath($file, $version = 'V202')
    {
        $formPath = 'App\Lite\Forms\%s\%s';

        return sprintf($formPath, $version, $file);
    }
}
