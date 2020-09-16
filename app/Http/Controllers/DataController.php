<?php

namespace App\Http\Controllers;

use App\Data;
use Illuminate\Http\Request;

use ZipArchive;
use File;

class DataController extends Controller
{
    public function parse()
    {
        //ftp.zakupki.gov.ru/fcs_regions/Kaliningradskaja_obl/notifications/currMonth

        /*/
         * подключаемся к фтп с паролем в пассивном режиме
         * скачиваем архивы в папку download, извлекаем только xml файлы в одноименную подпапку
         * не выполнял никаких проверок на соеденение итд...
        /*/
        
        $conn = ftp_connect('ftp.zakupki.gov.ru');
            ftp_login($conn, 'free', 'free');
            ftp_pasv($conn, 1);
            ftp_chdir($conn, '/fcs_regions/Kaliningradskaja_obl/notifications/currMonth');
        $list = ftp_nlist($conn, '.');

        $zip = new ZipArchive;

        foreach($list as $filename)
        {
            ftp_get($conn, 'download/'.$filename, $filename, FTP_ASCII);
            
            $zip->open('download/'.$filename);

            for($i = 0; $i < $zip->numFiles; $i++) {
                $namef = $zip->getNameIndex($i);
                $filetype = pathinfo($namef, PATHINFO_EXTENSION);
                if($filetype == 'xml')
                    $zip->extractTo('download/xml/'.current(explode('.', $filename)), $namef);
            }

            $zip->close();
        }
        ftp_close($conn);

        /*/
         * получаем список всех файлов в директориях xml
         * правим неймспейсы, кодируем в json (юникод)
         * сохраняем в базу, без валидации итд...
        /*/

        $files = File::allFiles('download\xml');

        foreach($files as $file)
        {
            $xml = simplexml_load_file($file->getPathname());

            $namespaces = $xml->getNamespaces();
            $jns = $xml->children($namespaces['ns2'])->children();

            $json = json_encode($jns, JSON_UNESCAPED_UNICODE);

            $newjs = new Data();
                $newjs->jsonData = $json;
            $newjs->save();
        }

        return 'done';
    }

    public function testshow($id)
    {
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://medline.rip/api/data/'.$id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 0);
            $data = curl_exec($ch);
        curl_close($ch);

        dd($data);
    }
}
