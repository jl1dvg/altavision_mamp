<?php

error_reporting(E_ALL ^ E_NOTICE);

define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);

processRequest();

// Front controller
function processRequest() {
    if(isset($_GET['delay']) && is_numeric($_GET['delay'])) {
        sleep($_GET['delay']);
    }

    $action = $_GET['action'];
    switch($action) {
        case 'upload': { // handle file upload
            header('Content-type: text/plain; charset=utf-8');

            $fileNames = handleUploadedFiles();
            if(is_array($fileNames) && count($fileNames) > 0) {
                foreach($fileNames as $index => $filename) {
                    if($index > 0) {
                        print('\n');
                    }
                    if(strpos($filename, 'ERROR:') === 0) {
                        print($filename); // error mesg
                    } else {
                        print(dirname(getCurrentPageURL()) . str_replace(DIRECTORY_SEPARATOR, "/", substr(UPLOAD_DIR, strlen(__DIR__))) . $filename);
                    }
                }
            } else {
                print('ERROR: no file uploaded');
            }
            return; // without printing footer
        }
        break;

        case 'source': { // display source code of this file
            show_source(__FILE__);
        }
        break;

        case 'dump': { // dump request information
            print('<html><head><title>Asprise Upload Tester</title><link rel="stylesheet" href="' . dirname(getCurrentPageURL()) . '/upload.css" /></head><body>');
            print('<p class="url">URL: ' . getCurrentPageURL());

            print('<div id="main"><div id="left"><h2>Archivo Scaneado</h2>');

            $fileNames = handleUploadedFiles();
            if(is_array($fileNames) && count($fileNames) > 0) {
                foreach($fileNames as $index => $filename) {
                    if(strpos($filename, 'ERROR:') === 0) {
                        print("<p>$filename</p>");
                    } else {
                        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $targetUrl = dirname(getCurrentPageURL()) . str_replace(DIRECTORY_SEPARATOR, "/", substr(UPLOAD_DIR, strlen(__DIR__))) . $filename;
                        print('<input type="text" name="file[]" value="C:/xampp/htdocs/altavision/templates/documents/tmp/' . $filename . '" />');
                        print('<a href="' . $targetUrl . '" target="_blank">');
                        if (strpos($filename, 'ERROR:') !== 0 && in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
                            $imgAnchor = '<img src="' . $targetUrl . '" height="160">';
                        } else if (strpos($filename, 'ERROR:') !== 0 && in_array($extension, array('tif', 'tiff'))) {
                            $imgAnchor = '<img src="' . dirname(getCurrentPageURL()) . '/icon-tif.png">';
                        } else if (strpos($filename, 'ERROR:') !== 0 && $extension == 'pdf') {
                            $imgAnchor = '<img src="' . dirname(getCurrentPageURL()) . '/icon-pdf.png">';
                        } else {
                            $imgAnchor = '<img src="' . dirname(getCurrentPageURL()) . '/icon-others.png">';
                        }
                        print($imgAnchor . '</a>');
                    }
                }
            }
            print('</div>');

            print('<div id="right">');

            print('<h2>SE HA SCANEADO CORRECTAMENTE</h2>');
            print('</div>');

            $imgArray = array(); // print POST values pointing to image URL
            foreach($_POST as $paramKey => $paramValue) {
                if(strlen($paramValue) < 6) {
                    continue;
                }
                if(strtolower(substr(trim($paramValue), 0, 4)) == 'http' && in_array(strtolower(pathinfo($paramValue, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'gif', 'png', 'tif', 'tiff')) )  {
                    array_push($imgArray, $paramValue);
                }
            }

            if(is_array($imgArray) && count($imgArray) > 0) {
                print('<br>');
                foreach($imgArray as $imgSrc) {
                    print('<a href="' . $imgSrc . '" target="_blank">');
                    print('<img src="' . $imgSrc . '" style="border: solid 1px #19f" hspace=8 vspace=8 height=160></a>');
                }
            }

        }
        break;

        case 'form': { // form for manual upload
            print('<form action="upload.php?action=dump" method="post" enctype="multipart/form-data">' .
                '<input type="file" name="file[]"><br>' .
                '<input type="file" name="file[]"><br>' .
                '<input type="submit" name="submit" value="Submit"></form>');
        }
        break;

        default: {
            print('Use a &lt;form&gt; to post scanned images or <a href="' . $_SERVER["PHP_SELF"] .
                '?action=form">manually upload</a> to this URL | <a href="' . $_SERVER["PHP_SELF"] . '?action=source">Show source code</a>');
        }
    } // end of switch

    print('<hr style="height: 1px; color: #999; font-size: 0; border: 0; background: #999; margin-top: 20px; margin-bottom: 10px;">' .
        '<span style="font-family: Arial; color: #999; font-size: 12px;">Todos los derechos reservados por ALTAVISION</span>');
}

/**
 * @return an array of mixing simple names of the files uploaded into UPLOAD_DIR and error strings starting with 'ERROR: ' or empty array if there is no uploaded file.
 */
function handleUploadedFiles() {
    $fileNames = array();
    if(is_array($_FILES)) {
        foreach($_FILES as $name => $fileSpec) {
            if(! is_array($fileSpec)) {
                continue;
            }

            if(is_array($fileSpec['tmp_name'])) { // multiple files with same name
                foreach($fileSpec['tmp_name'] as $index => $value) {
                    if($fileSpec['error'][$index] == UPLOAD_ERR_OK) {
                        array_push($fileNames, doHandleUploadedFile($fileSpec['name'][$index], $fileSpec['type'][$index], $fileSpec['tmp_name'][$index], $fileSpec['error'][$index], $fileSpec['size'][$index]));
                    }
                }
            } else {
                if($fileSpec['error'] == UPLOAD_ERR_OK) {
                    array_push($fileNames, doHandleUploadedFile($fileSpec['name'], $fileSpec['type'], $fileSpec['tmp_name'], $fileSpec['error'], $fileSpec['size']));
                }
            }
        }
    }

    return $fileNames;
}


/**
 * @return simple name of the file in the UPLOAD_DIR or an error string starting with 'ERROR: '.
 */
function doHandleUploadedFile($name, $type, $tmp_name, $error, $size) {
    if($error != UPLOAD_ERR_OK) {
        return 'ERROR: upload error code: ' . $error . ' for file ' . $name;
    }

    $extension = pathinfo($name, PATHINFO_EXTENSION);
    if($extension == null || strlen($extension) == 0) {
        $extension = getImageExtensionByMimeType($type);
        if($extension != null) {
            $name .= '.' . $extension;
        }
    }

    if($extension == null || strlen($extension) == 0 ||  (strlen($extension) > 0 && (!in_array(strtolower($extension), array('jpg', 'jpeg', 'gif', 'png', 'tif', 'tiff', 'pdf'))))) {
        return 'ERROR: extension not allowed: ' . $extension . ' for file ' . $name;
    }

    $name = preg_replace("/[^A-Z0-9._-]/i", "_", $name);
    // don't overwrite an existing file
    $i = 0;
    $parts = pathinfo($name);
    while (file_exists(UPLOAD_DIR . $name)) {
        $i++;
        $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
    }

    if(! file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR); // try to mkedir
    }

    $moved = move_uploaded_file($tmp_name, UPLOAD_DIR . $name);
    if($moved) {
        chmod(UPLOAD_DIR . $name, 0644);
    } else {
        return 'ERROR: moving uploaded file failed' . ' for file ' . $name;
    }

    return $name;
}

function getCurrentPageURL() {
    $defaultPort = "80";
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
        $defaultPort = "443";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != $defaultPort) {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function getImageExtensionByMimeType($mimeType) {
    $mimeType = strtolower($mimeType);
    switch($mimeType) {
        case 'image/jpeg': return "jpg";
        case 'image/pjpeg': return 'jpg';
        case 'image/png': return 'png';
        case 'image/gif': return 'gif';
        case 'image/tiff': return 'tif';
        case 'image/x-tiff': return 'tif';
        case 'application/pdf': return 'pdf';
        default: return '';
    }
}

?>
