<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Upload library - CI3-compatible file upload wrapper for CodeIgniter 4.
 */
class Upload
{
    protected $config = [
        'upload_path'   => '',
        'allowed_types' => '*',
        'file_name'     => '',
        'max_size'      => 0,
        'max_width'     => 0,
        'max_height'    => 0,
        'overwrite'     => false,
        'remove_spaces' => true,
        'encrypt_name'  => false,
    ];

    protected $upload_data = [];
    protected $error_msg = [];
    protected $uploaded;

    public function initialize(array $config = [])
    {
        foreach ($config as $key => $val) {
            if (isset($this->config[$key])) {
                $this->config[$key] = $val;
            }
        }
        $this->error_msg = [];
        $this->upload_data = [];
        return $this;
    }

    public function do_upload($field = 'userfile')
    {
        $request = service('request');
        $file = $request->getFile($field);

        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            $this->error_msg[] = 'No file selected or file upload invalid for field: ' . $field;
            return false;
        }

        // Allowed types check
        $allowed = $this->parseAllowedTypes();
        $ext = strtolower($file->getClientExtension());
        if ($this->config['allowed_types'] !== '*' && !in_array($ext, $allowed, true)) {
            $this->error_msg[] = 'The filetype you are attempting to upload is not allowed.';
            return false;
        }

        // Size check (KB)
        if ($this->config['max_size'] > 0) {
            $maxBytes = (int) $this->config['max_size'] * 1024;
            if ($file->getSize() > $maxBytes) {
                $this->error_msg[] = 'The file you are attempting to upload is larger than the permitted size.';
                return false;
            }
        }

        $uploadPath = rtrim($this->config['upload_path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Determine final file name
        $fileName = $this->config['file_name'];
        if ($fileName === '') {
            $fileName = $file->getRandomName();
        } elseif ($this->config['remove_spaces']) {
            $fileName = str_replace(' ', '_', $fileName);
        }

        if (!is_dir($uploadPath)) {
            $this->error_msg[] = 'The upload path does not appear to be valid.';
            return false;
        }

        try {
            if ($this->config['overwrite']) {
                $file->move($uploadPath, $fileName, true);
            } else {
                $file->move($uploadPath, $fileName);
            }
        } catch (\Throwable $e) {
            $this->error_msg[] = 'Unable to move file: ' . $e->getMessage();
            return false;
        }

        $this->upload_data = [
            'full_path'  => $uploadPath . $fileName,
            'file_path'  => $uploadPath,
            'file_name'  => $fileName,
            'orig_name'  => $file->getClientName(),
            'file_ext'   => '.' . $ext,
            'file_size'  => round($file->getSize() / 1024, 2),
            'is_image'   => in_array($ext, ['gif', 'jpg', 'jpeg', 'png'], true),
            'image_width' => $file->getWidth() ?: '',
            'image_height' => $file->getHeight() ?: '',
            'client_name' => $file->getClientName(),
        ];

        return true;
    }

    public function data($index = null)
    {
        if ($index === null) {
            return $this->upload_data;
        }
        return $this->upload_data[$index] ?? null;
    }

    public function display_errors($open = '', $close = '')
    {
        $str = '';
        foreach ($this->error_msg as $e) {
            $str .= $open . $e . $close;
        }
        return $str === '' ? ($open . $close) : $str;
    }

    public function set_error($msg)
    {
        $this->error_msg[] = $msg;
    }

    protected function parseAllowedTypes(): array
    {
        $types = trim((string) $this->config['allowed_types']);
        if ($types === '' || $types === '*') {
            return [];
        }
        return array_map('strtolower', preg_split('/[\|,]/', $types));
    }
}
