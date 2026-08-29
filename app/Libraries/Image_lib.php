<?php

namespace App\Libraries;

use CodeIgniter\Images\Image;

/**
 * Image_lib - CI3-compatible image manipulation wrapper using CI4's Image service.
 */
class Image_lib
{
    protected $config = [
        'image_library'  => 'gd2',
        'source_image'   => '',
        'new_image'      => '',
        'create_thumb'   => false,
        'thumb_marker'   => '_thumb',
        'maintain_ratio' => true,
        'width'          => 0,
        'height'         => 0,
        'quality'        => 90,
    ];

    protected $error_msg = [];

    public function initialize(array $config = [])
    {
        foreach ($config as $key => $val) {
            if (isset($this->config[$key])) {
                $this->config[$key] = $val;
            }
        }
        $this->error_msg = [];
        return $this;
    }

    public function clear()
    {
        foreach (array_keys($this->config) as $key) {
            $this->config[$key] = '';
        }
        $this->config = array_merge($this->config, [
            'image_library'  => 'gd2',
            'create_thumb'   => false,
            'thumb_marker'   => '_thumb',
            'maintain_ratio' => true,
            'quality'        => 90,
        ]);
        $this->error_msg = [];
        return $this;
    }

    public function resize()
    {
        $source = $this->config['source_image'];
        if (!is_file($source)) {
            $this->error_msg[] = 'Source image does not exist.';
            return false;
        }

        try {
            $image = \Config\Services::image()
                ->withFile($source);

            if ($this->config['maintain_ratio'] &&
                $this->config['width'] > 0 && $this->config['height'] > 0) {
                $image->resize($this->config['width'], $this->config['height'], true);
            } elseif ($this->config['width'] > 0 || $this->config['height'] > 0) {
                $image->resize(
                    $this->config['width'] > 0 ? $this->config['width'] : $this->config['height'],
                    $this->config['height'] > 0 ? $this->config['height'] : $this->config['width'],
                    false
                );
            }

            if ($this->config['create_thumb']) {
                $marker = $this->config['thumb_marker'];
                $dir = dirname($source);
                $base = pathinfo($source, PATHINFO_FILENAME);
                $ext = pathinfo($source, PATHINFO_EXTENSION);
                $thumbPath = $dir . DIRECTORY_SEPARATOR . $base . $marker . '.' . $ext;
                $image->save($thumbPath, $this->config['quality']);
            } elseif ($this->config['new_image'] !== '') {
                $this->saveToNewImage($image, $this->config['new_image'], $source);
            } else {
                $image->save($source, $this->config['quality']);
            }
        } catch (\Throwable $e) {
            $this->error_msg[] = $e->getMessage();
            return false;
        }

        return true;
    }

    protected function saveToNewImage($image, $newImage, $source)
    {
        // If new_image is relative (no dir), place beside source
        if (strpos($newImage, DIRECTORY_SEPARATOR) === false) {
            $newImage = dirname($source) . DIRECTORY_SEPARATOR . $newImage;
        }
        $image->save($newImage, $this->config['quality']);
    }

    public function display_errors($open = '', $close = '')
    {
        $str = '';
        foreach ($this->error_msg as $e) {
            $str .= $open . $e . $close;
        }
        return $str === '' ? ($open . $close) : $str;
    }
}
