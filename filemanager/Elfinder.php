<?php

/**
 * Author:
 * Xolmat Ravshanov
 * Date: 31.05.2020
 */

namespace zetsoft\service\filemanager;

use elFinderConnector;
use zetsoft\dbitem\elfin\ElfinderItem;
use zetsoft\system\elfinder\ZElFinder;
use zetsoft\system\kernels\ZAction;
use zetsoft\system\kernels\ZFrame;


class Elfinder extends ZFrame
{

    public $options;

    public $trashPath;

    public $data;

    public $config = [
        'uploadAllow' => [
            'image/x-ms-bmp',
            'image/gif',
            'image/jpeg',
            'image/png',
            'image/x-icon',
            'text/plain',
        ],
    ];


    public $_layout;


    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from '.' (dot)
     *
     * @param string $attr attribute name (read|write|locked|hidden)
     * @param string $path absolute file path
     * @param string $data value of volume option `accessControlData`
     * @param object $volume elFinder volume driver object
     * @param bool|null $isDir path is directory (true: directory, false: file, null: unknown)
     * @param string $relpath file path relative to volume root directory started with directory separator
     * @return bool|null
     **/

    public function access($attr, $path, $data, $volume, $isDir, $relpath)
    {

        $basename = bname($path);
        return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
        && strlen($relpath) !== 1           // but with out volume root
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            : null;
        // else elFinder decide it itself

    }

    public function layout()
    {
        $this->_layout['trash'] = [
            'id' => '1',
            'driver' => 'Trash',
            'path' => 'c:/elfind/',
            'tmbURL' => 'c:/elfind/',
            'quarantine' => 'c:/elfind/',
            'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
            'uploadDeny' => ['all'],                // Recomend the same settings as the original volume that uses the trash
            'uploadAllow' => $this->config['uploadAllow'], // Same as above
            'uploadOrder' => ['deny', 'allow'],      // Same as above
            'accessControl' => 'access',                    // Same as above
        ];

        $this->_layout['item'] = [
            //'mime_handler ' =>  ElFinder::MimeType,
            'path' => null,                 // path to files (REQUIRED)
            'URL' => null, // URL to files (REQUIRED)

            'driver' => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
            'quarantine' => 'c:/elfind/',
            'autoload' => false, //It must set true If volume driver supports autoload function.
            'phash' => '', //Folder hash value on elFinder to be the parent of this volume
            'startPath' => null, //Open this path on initial request instead of root path
            'encoding' => '', //This volume's local encoding. (server's file system encoding) It's necessary to be valid value to iconv.

            'locale' => '', //This volume's local locale. It's important for encoding setting. It's necessary to be valid value in your server.


            'alias' => '', //Root path alias for volume root. If not set will use directory name of path

            'i18nFolderName' => false, //Enable i18n folder name that convert name to elFinderInstance.messages['folder_'+name]

            'mimeDetect' => 'auto', //Method to detect files mimetypes. Can be auto, internal, finfo, mime_content_type

            'mimefile' => '', //Path to alternative mime types file. Only used when mimeDetect set to internal

            'additionalMimeMap' => [], //Additional Mime type normalization map
            'dispInlineRegex' => '^(?:(?:video|audio)|image/(?!.+\+xml)|application/(?:ogg|x-mpegURL|dash\+xml)|(?:text/plain|application/pdf)$)', //MIME regex of send HTTP header "Content-Disposition: inline" on file open command.

            'imgLib' => 'auto', //Image manipulations library. Can be auto, imagick, gd or convert
            'tmbPath' => 'c:/elfind/', //Directory for thumbnails. If this is a simple filename, it will be prefixed with the root directory path

            'tmbPathMode' => 0777, //Umask for thumbnails dir creation. Will be removed in future
            'tmbURL' => 'c:/elfind/', //URL for thumbnails directory set in tmbPath. Set it only if you want to store thumbnails outside root directory.

            'tmbSize' => 48, //Thumbnails size in pixels. Thumbnails are square
            'tmbCrop' => true, //Crop thumbnails to fit tmbSize. true - resize and cropLength, false - scale image to fit thumbnail size

            'tmbBgColor' => 'transparent', //Thumbnails background color (hex #rrggbb or transparent)

            'bgColorFb' => '#ffffff', //Image rotate fallback background color (hex #rrggbb). Uses this color if it can not specify to transparent.

            'tmbFbSelf' => false, //Fallback self image to thumbnail (nothing imgLib).
            'copyOverwrite' => true, //Replace files on paste or give new nameOn to pasted files. true - old file will be replaced with new one, false - new file get name - original_name-number.ext

            'copyJoin' => true, //Merge new and old content on paste. true - join new and old directories content, false - replace old directories with new ones

            'copyFrom' => true, //Allow to copy from this volume to other ones
            'copyTo' => true, //Allow to copy from other volumes to this one
            'tmpPath' => 'c:/elfind/', //Temporary directory used for extract etc. The default tmpPath is to use 'tmbPath'.

            'uploadOverwrite' => true, //Replace files with the same name on upload or give them new nameOn. true - replace old files, false give new nameOn like original_name-number.ext

            'uploadMaxSize' => 0, // Maximum upload file size. This size is per files. Can be set as number or string with unit 10M, 500K, 1G. Note: elFinder 2.1+ support chunked file uploading. 0 means unlimited upload.

            'uploadMaxConn' => 3, //Maximum number of connection of chunked file uploading. -1 to disable chunked file upload.

            'defaults' => [], //Default file/directory permissions. Setting hidden, locked here - take no effect


            /*'attributes' => [
                'hidden' => true
            ],*/


            //File (folder) permission attributes
            'attributes' => [],


            'acceptedName' => '/^[^\.].*/', //Validate new file name regex or function
            'accessControlData' => null, //Data that will be passet to access control method

            'statOwner' => false, //Include file owner, group & mode in stat results on supported volume driver (LocalFileSystem(require POSIX in PHP), FTP on UNIX system-like). false to inactivate "chmod" command.

            'allowChmodReadOnly' => false, //Allow exec chmod of read-only( on elFinder permission ) files.
            'treeDeep' => 1, //How many subdirs levels return per request
            'checkSubfolders' => true, //Check children directories for other directories in it. true every folder will be check for children folders, -1 every folder will be check asynchronously, false all folders will be marked as having subfolders
            'separator' => DIRECTORY_SEPARATOR, //Directory separator. Required by client to show correct file paths
            'dateFormat' => 'j M Y H:i', //File modification date format. This value is passed to PHP date() function
            'timeFormat' => 'H:i', //File modification time format
            'cryptLib' => 'undefined', //Library to crypt/uncrypt files nameOn (not implemented yet)
            'archiveMimes' => [], //Allowed archive's mimetypes to create. Leave empty for all available types
            'archivers' => [], //EyufManual config for archivers. Leave empty for auto detect
            'plugin' => [], //Configure plugin options of each volume

            // 'trashHash' => 't1_Lw',                     // elFinder's hash of trash folder
            'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
            //'all' // All Mimetypes not allowed to upload
            'uploadDeny' => [
                'text/javascript',
                'text/x-java',
                'text/x-java-source',
                'text/x-ruby',
                'text/x-perl',
                'text/x-sql',
                'text/x-shellscript',
                'text/x-c++src',
                'text/x-c++',
                'text/x-chdr',
                'text/x-csrc',
                'text/x-c',
                'application/javascript',
            ],

            'uploadAllow' => $this->config['uploadAllow'], // Mimetype `image` and `text/plain` allowed to upload
            'uploadOrder' => ['deny', 'allow'],  // allowed Mimetype `image` and `text/plain` only
            'accessControl' => 'access',                     // disable and hide dot starting files (OPTIONAL)

        ];

    }


    public function run()
    {


        $this->layout();

        $this->data = $this->sessionGetObject('elfinder');


        $roots = [];
        foreach ($this->data as $item) {

            /** @var ElfinderItem $item */
            $root = $this->_layout['item'];
            $root['path'] = $item->path;
            $root['URL'] = $item->url;
            $root['startPath'] = $item->startPath;
            $root['alias'] = $item->alias;
            $root['startPath'] = $item->startPath;
            $root['mimeDetect'] = $item->mimeDetect;
            $root['mimefile'] = $item->mimefile;
            $root['additionalMimeMap'] = $item->additionalMimeMap;
            $root['imgLib'] = $item->imgLib;
            $root['tmbPath'] = $item->tmbPath;
            $root['tmpPath'] = $item->tmpPath;
            $root['tmbPathMode'] = $item->tmbPathMode;
            $root['tmbSize'] = $item->tmbSize;
            $root['tmbCrop'] = $item->tmbCrop;
            $root['tmbBgColor'] = $item->tmbBgColor;
            $root['bgColorFb'] = $item->bgColorFb;
            $root['tmbFbSelf'] = $item->tmbFbSelf;
            $root['archiveMimes'] = $item->archiveMimes;
            $root['uploadMaxSize'] = $item->uploadMaxSize;
            $root['uploadMaxConn'] = $item->uploadMaxConn;
            $root['quarantine'] = $item->quarantine;
            $root['attributes'] = $item->attributes;
            $root['defaults'] = $item->mode;
            $roots[] = $root;
        }


        ZElFinder::$netDrivers['ftp'] = 'FTP';


        /**
         *
         *  https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
         */

        $this->options = [

            'debug' => false, // Send debug to client.

            'locale' => 'en_US.UTF-8', // Set locale. Currently only UTF-8 locales are supported. Passed to setLocale PHP function.

            'defaultMimefile' => '', // elFinderVolumeDriver mime.type file path as defaults. This can be overridden in each of the volume by setting the volume root mimefile. The default value '' meaning uses a file 'php/mime.type'.

            'uploadTempPath' => '',  // Temp directory path for Upload. Default uses sys_get_temp_dir()


            'commonTempPath' => sys_get_temp_dir(), //Temp directory path for temporally working files. Default uses ./.tmp if it writable.

            'connectionFlagsPath ' => '', //Connection flag files path that connection check of current request. A file is created every time an access is made to this location and it is deleted at the end of the request. It is recommended to specify RAM disk such as "/dev/shm".

            'maxArcFilesSize' => 0, //Max allowed archive files size (0 - no limit)
            'optionsNetVolumes' => [], //Root options of the network mounting volume
            'maxTargets' => 1000, //Max number of limits of selectable items (0 - no limit)

            'throwErrorOnExec' => true, //Throw Error on exec() true need try{} block for $connector->run();

            'bind' => [], //Bind callbacks for user actions, similar to jQuery .bind() More information in documentation

            'plugin' => [], //Configure plugin options of All volumes default value. When this config is omitted, the default value which plugin has is applied.

            'roots' => $roots
        ];


        $connector = new elFinderConnector(new ZElFinder($this->options));


        return $connector->run();
    }
}


