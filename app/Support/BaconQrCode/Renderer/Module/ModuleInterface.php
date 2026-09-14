<?php
declare(strict_types = 1);

namespace App\Support\BaconQrCode\Renderer\Module;

use App\Support\BaconQrCode\Encoder\ByteMatrix;
use App\Support\BaconQrCode\Renderer\Path\Path;

/**
 * Interface describing how modules should be rendered.
 *
 * A module always receives a byte matrix (with values either being 1 or 0). It returns a path, where the origin
 * coordinate (0, 0) equals the top left corner of the first matrix value.
 */
interface ModuleInterface
{
    public function createPath(ByteMatrix $matrix) : Path;
}
