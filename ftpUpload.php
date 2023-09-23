<?php
/**
 * - To use this script, fill it with the right login
 *
 * - Run a Xampp server.
 *
 * - Put the file in xampp/htdocs/ftpUpload/
 *
 * - Then go on your browser and go to http://localhost/ftpUpload/ftpUpload.php
 *
 * If no error appears, everything appened right
 *
 * This script doesn't erase old data
 *
 * This script, once completed, will download all the remote file excepted var and vendor folder
 *
 * After that, it will send you local file that fit with the patterns added in the list
 *
 * Actually, it sends all the local file excepted var and vendor one
 */

ini_set('max_execution_time', 0);

// Login FTP Server
$ftp_server = "ftp.lescigales.org";
$ftp_username = "XXXXXX";
$ftp_password = "XXXXXX";
const absolutePathProjectRoot = "XXX\\";

// Create the connection and set options
$ftp_conn = ftp_connect($ftp_server) or die("unable to connect to $ftp_server server");
ftp_login($ftp_conn, $ftp_username, $ftp_password);
ftp_pasv($ftp_conn, true); // Define the passive mode

// Copy all the remotes files for a backup if necessary
copyRemoteFile(".", ".", $ftp_conn);
echo("Download proceed\n");


$patterns = array("*.lock");
$patterns[] = "*.json";
$patterns[] = "config/*.php";
$patterns[] = "config/*.yaml";
$patterns[] = "config/packages/*.yaml";
$patterns[] = "config/packages/prod/*.yaml";
$patterns[] = "config/routes/*.yaml";
$patterns[] = "public/img/connexion/*";
$patterns[] = "public/img/entity/*.png";
$patterns[] = "public/img/home/*.*";
$patterns[] = "public/img/icons/*.*";
$patterns[] = "public/img/*.png";
$patterns[] = "public/script/*/*.js";
$patterns[] = "public/script/*.js";
$patterns[] = "public/style/*/*.css";
$patterns[] = "public/style/*.css";
$patterns[] = "src/Controller/*.php";
$patterns[] = "src/Controller/*/*.php";
$patterns[] = "src/Entity/*.php";
$patterns[] = "src/Manager/*.php";
$patterns[] = "src/Repository/*.php";
$patterns[] = "src/Utils/*.php";
$patterns[] = "src/Utils/Enum/*.php";
$patterns[] = "templates/*/*.twig";
$patterns[] = "templates/*.twig";

foreach ($patterns as $pattern) {
    uploadFileToFtp($ftp_conn, $pattern);
}



echo("\nUpload proceed\n");

// close connection
ftp_close($ftp_conn);


function createPathFromRootProject($pathFromProjectRoot): string {
    return absolutePathProjectRoot . $pathFromProjectRoot;
}

/**
 * Copy all the remoty file at remote_dir
 * doesn't copy "var" and "vendor" folder
 *
 * @param $local_dir
 * @param $remote_dir
 * @param $ftp_conn
 * @return void
 */
function copyRemoteFile($local_dir, $remote_dir, $ftp_conn): void {

    if ($remote_dir != ".") {
        if (!ftp_chdir($ftp_conn, $remote_dir)) {
            echo("Change Dir Failed: $remote_dir \n");
            return;
        }
        if (!(is_dir($remote_dir))) {
            mkdir($remote_dir);
        }
        chdir($remote_dir);
    }

    $contents = ftp_nlist($ftp_conn, ".");
    foreach ($contents as $file) {

        if ($file == '.' || $file == '..') {
            continue;
        }
        if (($file != "var") && ($file != "vendor")) {
            if (@ftp_chdir($ftp_conn, $file)) {
                ftp_chdir($ftp_conn, "..");
                copyRemoteFile($local_dir, $file, $ftp_conn);
            } else {
                ftp_get($ftp_conn, "$local_dir/$file", $file, FTP_ASCII);
            }
        }
    }

    ftp_chdir($ftp_conn, "..");
    chdir("..");
}


function uploadFileToFtp($ftp_conn, $filePattern): void {
    foreach (glob(createPathFromRootProject($filePattern)) as $filename) {
        $filenameFromRoot = str_replace(absolutePathProjectRoot, "", $filename);
        $location = str_replace(basename($filenameFromRoot), "", $filenameFromRoot);

        if (ftp_mkdir($ftp_conn, $location)) {
            echo "Successfully created the directory $location \n";
        } else {
            echo "Error while creating the directory $location \n";
        }

        if (ftp_put($ftp_conn, $filenameFromRoot, $filename, FTP_BINARY)) {
            echo "Successfully uploaded $filename at the location $filenameFromRoot \n";
        } else {
            echo "Error uploading $filename at the location $filenameFromRoot \n ";
        }
    }
}