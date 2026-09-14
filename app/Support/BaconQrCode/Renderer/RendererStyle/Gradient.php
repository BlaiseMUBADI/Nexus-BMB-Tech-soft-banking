<?php
declare(strict_types = 1);

namespace App\Support\BaconQrCode\Renderer\RendererStyle;

use App\Support\BaconQrCode\Renderer\Color\ColorInterface;

final class Gradient
{
    public function __construct(
        private readonly ColorInterface $startColor,
        private readonly ColorInterface $endColor,
        private readonly GradientType   $type
    ) {
    }

    public function getStartColor() : ColorInterface
    {
        return $this->startColor;
    }

    public function getEndColor() : ColorInterface
    {
        return $this->endColor;
    }

    public function getType() : GradientType
    {
        return $this->type;
    }
}
