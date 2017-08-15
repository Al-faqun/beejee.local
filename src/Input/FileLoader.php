<?php
/**
 * Created by PhpStorm.
 * User: Shinoa
 * Date: 12.08.2017
 * Time: 20:48
 */

namespace BeeJee\Input;


class FileLoader
{
    protected $input;
    protected $extensions = array();
    protected $types = array();
    
    /**
     * FileLoader constructor.
     * @param array $inputArray
     * @param array $types
     * @param array $extensions
     */
    public function __construct($inputArray, $types, $extensions)
    {
        $this->input = $inputArray;
        $this->extensions = $extensions;
        $this->types = $types;
    }
    
    /**
     * Saves file and returns it's new path, or rises exception.
     *
     * @param string $inputname
     * @param string $directory
     * @param string $newName
     * @return string $newPath
     * @throws \Exception
     */
    public function saveFile($inputname, $directory, $newName = '')
    {
        //check extension of previous filename
        $formerExtension = pathinfo($this->input[$inputname]['name'],PATHINFO_EXTENSION);
        if ( !empty($formerExtension) ) {
            $extension = '.' . $formerExtension;
        } else {
            $extension = '';
        }
        if (empty($newName)) {
            $savename = self::randomString(10) . $extension;
        } else {
            $savename = $newName . $extension;
        }
        
        $newPath = $directory . "/$savename";
        $result = move_uploaded_file(
            $this->input[$inputname]['tmp_name'], $newPath);
        if ($result === false) {
            throw new \Exception('File cannot be saved.');
        } else {
            return $newPath;
        }
    }
    
   
    
    /**
     * @param string $inputname
     * @return bool
     */
    public function exists($inputname)
    {
        if (isset($this->input[$inputname]) AND file_exists($this->input[$inputname]['tmp_name'])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @param string $filepath
     * @param bool $strict
     * @return bool
     */
    public function checkExtension($filepath, $strict = false)
    {
        $result = false;
        $imageFileType = pathinfo($filepath,PATHINFO_EXTENSION);
        //check against every extension
        foreach ($this->extensions as $ext) {
            //if previous example checks - stop checking
            if ($result === true) {
                break;
            }
            if ($strict !== false) {
                $imageFileType = strtolower($imageFileType);
                $ext = strtolower($ext);
            }
            $result = ($imageFileType === $ext) ? true : false;
        }
        
        return $result;
        
    }
    
    /**
     * @param string $filepath
     * @return bool
     */
    public function checkType($filepath)
    {
        $result = false;
        $finfo = new \finfo();
        $mime = $finfo->file($filepath, FILEINFO_MIME_TYPE);
        foreach ($this->types as $type) {
            //if previous example checks - stop checking
            if ($result === true) {
                break;
            }
            $result = ($mime === $type) ? true : false;
        }
        
        return $result;
    }
    
    /**
     * @param string $filepath
     * @param int $limit
     * @return bool
     */
    public function checkSize($filepath, $limit)
    {
        $filesize = filesize($filepath);
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