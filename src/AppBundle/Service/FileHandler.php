<?php 

namespace AppBundle\Service;
use Eventviva\ImageResize;


class FileHandler
{

	public function upload($file, $directory, $width = 470, $height = 320)
	{
        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $extension = $file->guessExtension();
        // Move the file to the directory where brochures are stored
        $file->move(
            $directory,
            $fileName
        );
        //dump($directory . $fileName);
        //die;
        $thumb = new ImageResize($directory . $fileName);
        $thumb->resize($width, $height, true);
        $thumb->save($directory . 'thumb_' . $fileName);

        return [
        	'name' => $fileName,
            'thumb' => 'thumb_' . $fileName,
        	'extension' => $extension
        ];
	}


}