<?php

namespace App\Libraries;

/**
 * Zip library - minimal CI3-compatible wrapper (builds zip in a temp file).
 */
class Zip
{
    protected $zip;
    protected $tmpfile;
    protected $zipPath;

    public function __construct()
    {
        $this->zip = new \ZipArchive();
    }

    public function read_dir($path, $preserve_filepath = true, $root_path = null)
    {
        $full = rtrim(ROOTPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
        if (!is_dir($full)) {
            return false;
        }

        $this->tmpfile = tempnam(sys_get_temp_dir(), 'zip') . '.zip';
        if ($this->zip->open($this->tmpfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }
        $this->zipPath = trim($path, DIRECTORY_SEPARATOR);
        $this->addDir($full, $this->zipPath);
        $this->zip->close();
        return true;
    }

    public function add_dir($directory, $filepath = null)
    {
        return $this->read_dir($directory, true, $filepath);
    }

    protected function addDir($dir, $prefix)
    {
        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') {
                continue;
            }
            $filePath = $dir . DIRECTORY_SEPARATOR . $f;
            $zipName  = $prefix . '/' . $f;
            if (is_dir($filePath)) {
                $this->zip->addEmptyDir($zipName);
                $this->addDir($filePath, $zipName);
            } else {
                $this->zip->addFile($filePath, $zipName);
            }
        }
    }

    public function add_data($filepath, $data = null)
    {
        if ($data === null) {
            return false;
        }
        if ($this->zip->open($this->tmpfile, \ZipArchive::CREATE) === true) {
            $this->zip->addFromString($filepath, $data);
            $this->zip->close();
        }
        return $this;
    }

    public function get_zip()
    {
        return $this->tmpfile ? file_get_contents($this->tmpfile) : false;
    }

    public function download($filename = 'backup.zip')
    {
        if ($this->tmpfile && file_exists($this->tmpfile)) {
            $data = file_get_contents($this->tmpfile);
        } else {
            $data = '';
        }

        if (ob_get_level()) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        $response = service('response');
        $response->setHeader('Content-Type', 'application/zip');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setBody($data);
        $response->send();
        exit;
    }
}
