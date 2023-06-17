<?php
/***
 * To use this script, fill it with the right login
 * Run a Xampp server.
 * Put the file in xampp/htdocs
 * Then go on your browser and go to http://localhost/ftpUpload.php
 *
 * If no error appears, everything appened right
 *
 * This script doesn't erase old data
 */

// Login FTP Server
$ftp_server = "ftp.lescigales.org";
$ftp_username = "xxx";
$ftp_password = "xxx";
const absolutePathProjectRoot = "C:\Users\Romain\Desktop\Logiciel\Repo Git\Website_AEDI\\";

$ftp_conn = ftp_connect($ftp_server) or die("unable to connect to $ftp_server server");

//login to FTP server
ftp_login($ftp_conn, $ftp_username, $ftp_password);

//Uploading files
//$filePattern = "*.lock";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "*.json";
//uploadFileToFtp($ftp_conn, $filePattern);
//
//$filePattern = "config/*.php";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "config/*.yaml";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "config/*.yaml";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "config/packages/*.yaml";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "config/packages/*.yaml";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "config/packages/prod/*.yaml";
//uploadFileToFtp($ftp_conn, $filePattern);
//
//$filePattern = "public/img/connexion/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "public/img/home/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "public/img/icons/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "public/img/entity/*.png";
//uploadFileToFtp($ftp_conn, $filePattern);
//
//$filePattern = "public/style/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "public/script/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//
//$filePattern = "src/Controller/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "src/Entity/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "src/Manager/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "src/Repository/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//$filePattern = "src/Utils/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//
//$filePattern = "templates/*";
//uploadFileToFtp($ftp_conn, $filePattern);
//
//$filePattern = "vendor/*";
//uploadFileToFtp($ftp_conn, $filePattern);


$filePattern = "poupi/README.md";
uploadFileToFtp($ftp_conn, $filePattern);

// close connection
ftp_close($ftp_conn);

function createPathFromRootProject($pathFromProjectRoot): string
{
    return absolutePathProjectRoot . $pathFromProjectRoot;
}

function uploadFileToFtp($ftp_conn, $filePattern): void
{
    foreach (glob(createPathFromRootProject($filePattern)) as $filename) {
        $filenameFromRoot = str_replace(absolutePathProjectRoot, "", $filename);
        $location = str_replace(basename($filenameFromRoot), "", $filenameFromRoot);


        if (ftp_mkdir($ftp_conn, $location)) {
            echo "Successfully created the directory $location
            ";
        } else {
            echo "Error while creating the directory $location
            ";
        }

        if (ftp_put($ftp_conn, $filenameFromRoot, $filename, FTP_ASCII)) {
            echo "Successfully uploaded $filename at the location $filenameFromRoot
            ";
        } else {
            echo "Error uploading $filename at the location $filenameFromRoot
            ";
        }
    }
}