<?php
declare(strict_types = 1);

namespace App\Support\BaconQrCode\Renderer\RendererStyle;

use App\Support\BaconQrCode\Renderer\Eye\EyeInterface;
use App\Support\BaconQrCode\Renderer\Eye\ModuleEye;
use App\Support\BaconQrCode\Renderer\Module\ModuleInterface;
use App\Support\BaconQrCode\Renderer\Module\SquareModule;

final class RendererStyle
{
    private ModuleInterface $module;

    private EyeInterface|null $eye;

    private Fill $fill;

    public function __construct(
        private int $size,
        private int $margin = 4,
        ?ModuleInterface $module = null,
        ?EyeInterface $eye = null,
        ?Fill $fill = null
    ) {
        $this->module = $module ?: SquareModule::instance();
        $this->eye = $eye ?: new ModuleEye($this->module);
        $this->fill = $fill ?: Fill::default();
    }

    public function withSize(int $size) : self
    {
        $style = clone $this;
        $style->size = $size;
        return $style;
    }

    public function withMargin(int $margin) : self
    {
        $style = clone $this;
        $style->margin = $margin;
        return $style;
    }

    public function getSize() : int
    {
        return $this->size;
    }

    public function getMargin() : int
    {
        return $this->margin;
    }

    public function getModule() : ModuleInterface
    {
        return $this->module;
    }

    public function getEye() : EyeInterface
    {
        return $this->eye;
    }

    public function getFill() : Fill
    {
        return $this->fill;
    }
}
