<?php

namespace App\Entity\Edge;

use App\Entity\Util\Point;

class EdgeConst
{
    const ELEVATION_MOUNTAIN = 1;
    const ELEVATION_PLAIN = 0;
    const ELEVATION_SHALLOW = -1;
    const ELEVATION_SEA = -2;

    static public function getClassByType(string $type, object $data): Edge
    {
        return match($type) {
            Wasteland::TYPE => new Wasteland(...get_object_vars($data)),
            Plain::TYPE => new Plain(...get_object_vars($data)),
            Shallow::TYPE => new Shallow(...get_object_vars($data)),
            Sea::TYPE => new Sea(...get_object_vars($data)),
        };
    }

    static public function getDefaultEdge(Point $point, int $face, int $elevation): Edge
    {
        return match(true) {
            $elevation >= self::ELEVATION_PLAIN => new Wasteland(point: $point, face: $face),
            $elevation === self::ELEVATION_SHALLOW => new Shallow(point: $point, face: $face),
            $elevation <= self::ELEVATION_SEA => new Sea(point: $point, face: $face),
        };
    }
}
