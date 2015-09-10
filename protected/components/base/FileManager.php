<?php

/**
 * Helps to manage file.
 */
class FileManager {

    const DIRECTORY = 'directory';
    const FILE = 'file';

    /**
     * @var string[] common mime types for images. 
     */
    public static $commonImagesType = array(
        'image/gif', 'image/jpg', 'image/jpeg',
        'image/pjpeg', 'image/png', 'image/x-png'
    );

    /**
     * Retrieve ControllersName that owned by some context. As say, Admin,
     * then the Controllers that would been given to is /modules/admin 
     * context.
     * 
     * pattern of context in file path system :
     * application.modules.<context>.controllers.*
     * @param string $context defining context of application path, eg. 
     *                        application.modules.<context>.controllers.*
     */
    public static function retrieveControllersMenu($context) {
        $basePath = Yii::app()->basePath;
        $adminControllerPath = sprintf("%s/modules/%s/controllers/", $basePath, $context);
        Yii::import(sprintf("application.modules.%s.controllers.*", $context));

        $menus = array();
        foreach (scandir($adminControllerPath) as $file) {
            $match = null;
            if (preg_match('/(\w+)Controller/', $file, $match)) {
                if ($match[1] !== "Default") {
                    $className = $match[1];

                    $controllersVariables = get_class_vars($className . "Controller");
                    $menu = $controllersVariables['MENU'];
                    $menus[$className] = $menu;
                }
            }
        }
        return $menus;
    }

    /**
     * Converts list of retrieved Controller's Menu into CMenu
     * @param mixed $menus
     * @param string $context
     * @return CMenu[] list of CMenu
     */
    public static function convertMenu($menus, $context) {
        $cmenus = array();
        foreach ($menus as $name => $menu) {
            $nameID = strtolower($name);
            $name = array_key_exists($name, FileManager::$controllersTranslate) ? FileManager::$controllersTranslate[$name] : $name;
            $cmenu = array(
                'label' => $name,
                'url' => Yii::app()->createUrl(sprintf('/%s/%s/', $context, $nameID)),
                'items' => $menu
            );
            $cmenus[] = $cmenu;
        }
        return $cmenus;
    }

    /**
     * simply create folder under given string path and force to do it
     * recursively
     * @param string $path path that needed to be created
     */
    public static function createFolder($path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Uploads multiple files and store it into given pathBase. it basicaly 
     * called the uploads single file and iterate it for each files.
     * @param CUploadedFile[] $files multiple files
     * @return boolean true whether the uploads is succeed
     */
    public static function uploadFiles($files, $pathBase = 'cache/') {
        $isSuccess = true;
        $path = Yii::app()->basePath . '/../' . $pathBase;
        FileManager::createFolder($path);
        foreach ($files as $index => $file) {
            if (!FileManager::uploadFile($file, $pathBase)) {
                $isSuccess = false;
                break;
            }
        }
        return $isSuccess;
    }

    /**
     * Uploads single file and store it into given pathBase
     * @param CUploadedFile $file single file
     * @return boolean true whether the uploads is succeed
     */
    public static function uploadFile($file, $pathBase = 'cache/', $filename = null, $mimeTypes = null) {
        $path = Yii::app()->basePath . '/../' . $pathBase;
        $relativeURL = $pathBase;
        FileManager::createFolder($path);
        $rawName = $filename ? $filename : $file->name;

        $fileNameExtension = $rawName;
        $URL = false;

        $filePath = $path . $fileNameExtension;
        if ($mimeTypes) {
            $mimeType = $file->getType();
            if (in_array($mimeType, $mimeTypes)) {
                $URL = $file->saveAs($filePath) ? $relativeURL . $fileNameExtension : false;
            }
        } else {
            $URL = $file->saveAs($filePath) ? $relativeURL . $fileNameExtension : false;
        }
        $URL ? @chmod($filePath, 0755) : false;
        return $URL;
    }

    /**
     * Get list of directories on some path context with getting its URL 
     * information and some additional informations that needed. 
     * It will retrieve directories information through its sub-directories
     * until reached its max depth (if the $maxDepth is given) or until it's
     * finished searching through the deepest leaf.
     * @param string $context context on its path
     * @return array the list of directories with its informations 
     */
    public static function listDirectories($context = '/', $maxDepth = null, $level = 1, $uniqueTimestampKey = false) {
        if ($maxDepth !== null && $level >= $maxDepth) {
            return "";
        }
        $basePath = sprintf('%s/..%s', Yii::app()->basePath, trim($context));
        $directoryTree = array();
        if (is_dir($basePath)) {
            foreach (scandir($basePath) as $file) {
                if (strcasecmp($file, ".") != 0 && strcasecmp($file, "..") != 0) {
                    $fileInfo = FileManager::fileInfo("$context/$file", $maxDepth, $level, $uniqueTimestampKey);
                    if (count($fileInfo)) {
                        if ($uniqueTimestampKey) {
                            $increment = 0;
                            while (isset($directoryTree[$fileInfo['createdTimestamp'] + $increment])) {
                                $increment++;
                            }
                            $directoryTree[$fileInfo['createdTimestamp'] + $increment] = $fileInfo;
                        } else {
                            $directoryTree[] = $fileInfo;
                        }
                    }
                }
            }
        }

        return $directoryTree;
    }

    /**
     * Retrieve file or folder info
     * @param string $context context on its path
     * @return string[] File or folder information
     */
    public static function fileInfo($context = '/', $maxDepth = null, $level = 1, $uniqueTimestampKey = false) {
        $path = sprintf('%s/..%s', Yii::app()->basePath, trim($context));
        $fileInfo = array();
        if (file_exists($path)) {
            if (is_dir($path)) {
                //Filter system folder
                if (!preg_match('/^\.\w+/', basename($path))) {
                    $fileInfo = array(
                        'name' => basename($path),
                        'type' => self::DIRECTORY,
                        'path' => $path,
                        'url' => Yii::app()->getBaseUrl(true) . $context,
                        'createdTimestamp' => filectime($path),
                        'subdirectories' => FileManager::listDirectories($context, $maxDepth, $level + 1, $uniqueTimestampKey)
                    );
                }
            } else {
                $fileInfo = array(
                    'name' => basename($path),
                    'type' => self::FILE,
                    'path' => $path,
                    'url' => Yii::app()->getBaseUrl(true) . $context,
                    'createdTimestamp' => filectime($path),
                );
            }
        }
        return $fileInfo;
    }

    /**
     * get static content from json file to array
     * @param string $fileName
     * @param string $directory
     * @return string[]
     */
    public static function staticContent($fileName, $directory = '/') {
        $path = Yii::app()->basePath . '/data' . $directory;
        return CJSON::decode(file_get_contents($path . $fileName));
    }

    /**
     * Delete file and folder recursively on assets folder
     * @param string $directory directory on assets folder
     * @return integer count deleted file and folder
     */
    public static function deleteAll($directory = '') {
        $count = 0;
        $basePath = sprintf('%s/../assets/%s', Yii::app()->basePath, $directory);
        foreach (scandir($basePath) as $file) {
            if (strcasecmp($file, ".") != 0 && strcasecmp($file, "..") != 0) {
                $filePath = "$basePath/$file";
                if (is_dir($filePath)) {
                    $count += FileManager::deleteAll("$directory/$file");
                    rmdir($filePath) ? $count++ : false;
                } else {
                    unlink($filePath) ? $count++ : false;
                }
            }
        }
        return $count;
    }

    /**
     * Add Watermark to existing image
     * @param string $imagePath
     * @param string $watermarkPath
     * @param integer $offsetX
     * @param integer $offsetY
     * @param integer $opacity
     * @return boolean true if image success add watermark false otherwise
     */
    public static function watermark($imagePath, $watermarkPath, $offsetX = null, $offsetY = null, $opacity = 100) {
        $status = false;
        if (file_exists($imagePath) && file_exists($watermarkPath)) {
            $watermark = new EasyImage($watermarkPath);
            $image = new EasyImage($imagePath);
            $image->watermark($watermark, $offsetX, $offsetY, $opacity);
            $status = $image->save();
        }
        return $status;
    }

    /**
     * Generate random file name (possible unique)
     * @param string $fileName
     * @param string $extension
     * @return string
     */
    public static function generateRandomFileName($fileName, $extension) {
        return strtolower(substr(md5($fileName), 0, 16) . substr(md5(date('Y-m-d H:i:s')), 0, 16) . "." . $extension);
    }

}

?>
