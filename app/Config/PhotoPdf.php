<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PhotoPdf extends BaseConfig
{
    /** Maximum participant records fetched into one HTML2PDF document. */
    public int $batchSize = 48;

    /** Print derivatives are capped to the card photo's approximate aspect ratio. */
    public int $maxImageWidth = 600;
    public int $maxImageHeight = 800;

    /** Do not decode very large images into GD buffers in the web process. */
    public int $maxDecodePixels = 20000000;

    /** Retention window for generated files if a shutdown cleanup is interrupted. */
    public int $retentionSeconds = 86400;

    /** Diagnostics are allowed only outside production and only when opted in. */
    public bool $diagnostics = false;

    public function __construct()
    {
        parent::__construct();

        $configuredBatchSize = max(1, min(50, (int) env('PHOTO_PDF_BATCH_SIZE', $this->batchSize)));
        // The card views paginate six cards per page (three per row). Keep PDF
        // part boundaries page-aligned so splitting does not alter pagination.
        $this->batchSize = max(6, intdiv(max(6, $configuredBatchSize), 6) * 6);

        $this->diagnostics = defined('ENVIRONMENT')
            && ENVIRONMENT !== 'production'
            && filter_var(env('PHOTO_PDF_DIAGNOSTICS', false), FILTER_VALIDATE_BOOL);
    }
}
