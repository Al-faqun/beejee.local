<?php

namespace BeeJee\Input;


class FileLoaderBase64
{
    protected $extensions = array();
    protected $types = array();
    
    /**
     * FileLoader constructor.
     * @param array $types
     * @param array $extensions
     */
    public function __construct($types, $extensions)
    {
        $this->extensions = $extensions;
        $this->types = $types;
    }
    
    
    
    /**
     * @param string $given former extension of file
     * @param bool $strict whether to be case sensitive or not
     * @return bool
     */
    public function checkExtension($given, $strict = false)
    {
        $result = false;
        //check against every extension
        foreach ($this->extensions as $ext) {
            //if previous example checks - stop checking
            if ($result === true) {
                break;
            }
            if ($strict !== false) {
                $imageFileType = strtolower($given);
                $ext = strtolower($ext);
            }
            $result = ($given === $ext) ? true : false;
        }
        
        return $result;
    }
    
    /**
     * @param string $data
     * @return bool
     */
    public function checkType($data)
    {
        $result = false;
        //file with unique name in temp directory
        $tmpfname = tempnam("", "php_tmp_");
        //write binary data in file and save
        $temp = fopen($tmpfname, "w+");
        fwrite($temp, $data);
        fclose($temp);
        //check fileinfo
        $finfo = new \finfo();
        $mime = $finfo->file($tmpfname, FILEINFO_MIME_TYPE);
        foreach ($this->types as $type) {
            //if previous example checks - stop checking
            if ($result === true) {
                break;
            }
            $result = ($mime === $type) ? true : false;
        }
        //delete temp file
        unlink($tmpfname);
        
        return $result;
    }
    
    /**
     * @param string $data
     * @param int $limit
     * @return bool
     */
    public function checkSize($data, $limit)
    {
        $filesize = mb_strlen($data, '8bit');
        if ($filesize > $limit) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * @return string
     */
    public static  function randomString($count)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $count; $i++) {
            $result.= $characters[rand(0, strlen($characters) - 1)];
        }
        return $result;
    }
}