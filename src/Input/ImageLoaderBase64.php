<?php

namespace BeeJee\Input;


class ImageLoaderBase64 extends FileLoaderBase64
{
    /**
     * ImageLoader constructor.
     * @param array $types
     * @param array $extensions
     */
    public function __construct($types, $extensions)
    {
        parent::__construct($types, $extensions);
    }
    
    /**
     * @param string $imageBase64
     * @param string $name
     * @param int $widthMax
     * @param int $heightMax
     * @return bool
     */
    public function checkImage($imageBase64, $name, $widthMax, $heightMax)
    {
        $result = false;
        //parse image data into array
        $file = self::decode64($imageBase64);
        //if it parses correctly
        if ($file !== false) {
            //data about file
            $mime = $file['mime'];
            $extension = pathinfo($name,PATHINFO_EXTENSION);
            $data = $file['data'];
            
            //true if correct file extension
            $result = $this->checkExtension($extension);
            //true if permitted type of file
            if ($result === true) {
                $result = $this->checkType($data);
            }
            //true if permitted dimensions of picture
            if ($result === true) {
                $result = $this->checkDimensions($data, $widthMax, $heightMax);
            }
            //true if permitted size of picture
            if ($result === true) {
                $result = $this->checkSize($data, 1000000);
            }
        }
        
        return $result;
    }
    
    /**
     * @param string $data
     * @param int $widthMax
     * @param int $heightMax
     * @return bool
     */
    public function checkDimensions($data, $widthMax, $heightMax)
    {
        list($width, $height) = getimagesizefromstring($data);
        if ($width > $widthMax OR $height > $heightMax) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * @param string $data
     * @param string $formerExtension
     * @param string $directory
     * @param string $newName
     * @return string
     * @throws \Exception
     */
    public function saveFile($data, $formerExtension, $directory, $newName = '')
    {
        $extension = '.' . $formerExtension;
        if (empty($newName)) {
            do {
                $savename = self::randomString(10) . $extension;
                $newPath = $directory . "/$savename";
            } while (file_exists($newPath));
        } else {
            $savename = $newName . $extension;
            $newPath = $directory . "/$savename";
        }
        //base64 to binary
        $file = self::decode64($data);
        $data = $file['data'];
        $result = file_put_contents($newPath, $data);
        if ($result === false) {
            throw new \Exception('File cannot be saved.');
        } else {
            return $savename;
        }
    }
    
    /**
     * Returns array with 'mime' of image, 'extension'  and it's binary 'data' as string
     * @param string $data
     * @return array|bool
     */
    public static function decode64($data)
    {
        if (preg_match('/^data:(image\/(gif|jpeg|png));base64,(.*)/i', $data, $matches))
        {
            $result['mime'] = $matches[1];
            $result['extension'] = $matches[2];
            $data = str_replace(' ','+', $matches[3]);
            $result['data'] = base64_decode($data);
        } else {
            $result = false;
        }
        return $result;
    }
}