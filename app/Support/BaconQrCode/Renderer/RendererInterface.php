<?php
declare(strict_types = 1);

namespace App\Support\BaconQrCode\Renderer;

use App\Support\BaconQrCode\Encoder\QrCode;

interface RendererInterface
{
    public function render(QrCode $qrCode) : string;
}
