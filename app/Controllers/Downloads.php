<?php

namespace App\Controllers;

class Downloads extends \App\Controllers\BaseController
{
    private array $allowlist = [
        'kalolsavam.pdf' => 'application/pdf',
    ];

    public function serve(string $file = ''): \CodeIgniter\HTTP\Response
    {
        $response = service('response');

        if ($file === '' || $file !== basename($file)) {
            return $response->setStatusCode(403)->setBody('Forbidden');
        }

        if ($file === 'chittur_itemcodes.html' || strpos($file, 'itemcodes') !== false) {
            return redirect()->to(base_url('downloads/item_codes'));
        }

        if (!isset($this->allowlist[$file])) {
            return $response->setStatusCode(404)->setBody('Not Found');
        }

        $path = ROOTPATH . 'doc' . DIRECTORY_SEPARATOR . $file;

        if (!is_file($path)) {
            return $response->setStatusCode(404)->setBody('Not Found');
        }

        $contentType = $this->allowlist[$file];

        return $response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $file . '"')
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setBody(file_get_contents($path));
    }
}
