<?php
/**
 * Laddar upp filer
 * 
 * @param string $input_name namn på fil
 */
function upload_file($input_name) {
    $target_dir = 'filer';
    $filename = 'none';

    if (isset($_FILES[$input_name]["tmp_name"]) && $_FILES[$input_name]["tmp_name"] != "") {
        $filename = basename($_FILES[$input_name]["name"]);
        $image_file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filename = count_files($target_dir) . '.' . $image_file_type;
        $target_file = $target_dir . "/" . count_files($target_dir) . '.' . $image_file_type;

        // Kollar om filen är en bild
        $check = getimagesize($_FILES[$input_name]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
        }

        // Kollar om filen redan existerar
        if (file_exists($target_file)) {
            echo "File already exists.";
        }

        // Kollar så att bilden inte är för stor
        if ($_FILES[$input_name]["size"] > 3 * 1024 * 1024) {
            echo "File is too large.";
        }

        // Kollar så att bilden är i ett tillåtet format
        if (!in_array($image_file_type, array("jpg", "jpeg", "png", "gif"))) {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }

        if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
            echo "success";
        } else {
            echo "Error uploading file.";
        }
    }
    return $filename;
}

/**
 * Raderar fil
 * 
 * @param string filnamnet
 */
function deleteFile($fileName) {
    $directory = 'filer/';
    $filePath = $directory . $fileName;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "The file '" . $fileName . "' has been deleted successfully.";
        } else {
            echo "Error: Unable to delete the file '" . $fileName . "'.";
        }
    } else {
        echo "Error: The file '" . $fileName . "' does not exist.";
    }
}

/**
 * Räknar antalet filer i ett visst directory
 * 
 * @param string directory
 */
function count_files($directory) {
    $file_count = 0;
    $dir_handle = opendir($directory);

    // Felhantering för om directory inte går att öppna
    if ($dir_handle === false) {
        return false;
    }

    while (($file = readdir($dir_handle)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $file_path = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_file($file_path)) {
                $file_count++;
            }
        }
    }

    closedir($dir_handle);
    return $file_count;
}