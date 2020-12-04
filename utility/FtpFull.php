<?php

namespace zetsoft\service\utility;

use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;


class FtpFull 
{

public function conn($config = []){

   return $filesystem = new Filesystem(new SftpAdapter($config));


    }


    public function write($path, $content){
        return $this->conn()->write($path, $content);
    }


    public function writeStream($path, $resourse){
        return $this->conn()->writeStream($path, $resourse);
    }

    public function put($path, $contents){
        return  $this->conn()->put($path, $contents);
    }

    public function putStream($path, $resourse){
        return $this->conn()->putStream($path, $resourse);
    }

    public function readAndDelete($path){
        return  $this->conn()->readAndDelete($path);
    }

    public function update($path, $resource) {
        return $this->conn()->update($path, $resource) ;
    }


    public function read($path) {
        return  $this->conn()->read($path) ;
    }

    public function readStream($path) {
        return  $this->conn()->readStream($path) ;
    }

    public function rename($path, $newpath) {
        return  $this->conn()->rename($path, $newpath);
    }

    public function copy($path, $newpath){
        return $this->conn()->copy($path, $newpath);
    }

    public function delete($path){
        return  $this->conn()->delete($path);
    }

    public function deleteDir($dirname){
        return  $this->conn()->deleteDir($dirname);
    }

    public function createDir($dirname){
        return  $this->conn()->createDir($dirname);
    }

    public function listContents($directory = ''){
       return $this->conn()->listContents($directory);
    }

    public function getMimetype($path){
        return  $this->conn()->getMimetype($path);
    }

    public function getTimestamp($path){
        return $this->conn()->getTimestamp($path);
    }

    public function getVisibility($path){
        return $this->conn()->getVisibility($path);
    }

    public function setVisibility($path, $visibility){
        return  $this->conn()->setVisibility($path, $visibility);
    }

    public function getSize($path){
       return $this->conn()->getSize($path);
    }

    public function getMetadata($path){
        return $this->conn()->getMetadata($path);
    }

    public function get($path){
       return $this->conn()->get($path);
    }

    public function assertPresent($path){
      return  $this->conn()->assertPresent($path);
    }


    public function assertAbsent($path){
       return $this->conn()->assertAbsent($path);
    }}
