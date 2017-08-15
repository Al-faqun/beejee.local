<?php
namespace BeeJee\Input;


class ImageLoader extends FileLoader
{
    /**
     * ImageLoader constructor.
     * @param array $inputArray
     * @param array $types
     * @param array $extensions
     */
    public function __construct($inputArray, $types, $extensions)
    {
        parent::__construct($inputArray, $types, $extensions);
    }
    
    
    /**
     * Check if file inside input array, sent from input named $inputname, is a valid image.
     *
     * @param string $inputname
     * @param int $widthMax
     * @param int $heightMax
     * @return bool
     */
    public function checkImage($inputname, $widthMax, $heightMax)
    {
        $result = false;
        
        $exists = $this->exists($inputname);
        if ($exists === true) {
            $file = $this->input[$inputname];
            //true if correct file extension
            $result = $this->checkExtension($file['name']);
            //true if permitted type of file
            if ($result === true) {
                $result = $this->checkType($file['tmp_name']);
            }
            //true if permitted dimensions of picture
            if ($result === true) {
                $result = $this->checkDimensions($file['tmp_name'], $widthMax, $heightMax);
            }
            //true if permitted size of picture
            if ($result === true) {
                $result = $this->checkSize($file['tmp_name'], 2000000);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param string $filepath
     * @param int $widthMax
     * @param int $heightMax
     * @return bool
     */
    public function checkDimensions($filepath, $widthMax, $heightMax)
    {
        list($width, $height) = getimagesize($filepath);
        if ($width > $widthMax OR $height > $heightMax) {
            return false;
        } else {
            return true;
        }
    }
}