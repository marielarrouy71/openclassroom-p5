<?php
/**
 * Created by IntelliJ IDEA.
 * User: remimichel
 * Date: 28/02/2019
 * Time: 19:55
 */

namespace App\Service;


class FileService
{

    /**
     * @param string $type
     */
    public function getExtension($type){
        $extensions = [
            'text/csv' => '.csv',
            'application/pdf' => '.pdf',
            'image/jpeg' => '.jpeg',
            'image/png' => '.png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        ];

        return $extensions[$type];
    }

}
