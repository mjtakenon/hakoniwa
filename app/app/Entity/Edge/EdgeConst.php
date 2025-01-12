<?php

namespace App\Entity\Edge;

use App\Entity\Edge\Others\Plain;
use App\Entity\Edge\Others\Sea;
use App\Entity\Edge\Others\Shallow;
use App\Entity\Edge\Others\Shore;
use App\Entity\Edge\Others\Wasteland;
use App\Entity\Util\Point;

class EdgeConst
{
    const ELEVATION_LAND = 0;
    const ELEVATION_SHALLOW = -2;
    const ELEVATION_SEA = -4;

    static public function getClassByType(string $type, object $data): Edge
    {
        return match($type) {
            Wasteland::TYPE => new Wasteland(...get_object_vars($data)),
            Plain::TYPE => new Plain(...get_object_vars($data)),
            Shore::TYPE => new Shore(...get_object_vars($data)),
            Shallow::TYPE => new Shallow(...get_object_vars($data)),
            Sea::TYPE => new Sea(...get_object_vars($data)),
        };
    }

    static public function getDefaultEdge(Point $point, int $face, int $elevation): Edge
    {
        return match(true) {
            $elevation >= self::ELEVATION_LAND => new Wasteland(point: $point, face: $face, elevation: $elevation),
            $elevation <= self::ELEVATION_SEA => new Sea(point: $point, face: $face, elevation: $elevation),
            default => new Shallow(point: $point, face: $face, elevation: $elevation),
        };
    }
}
