<?php

namespace App\Services\Hakoniwa\Cell;

use App\Services\Hakoniwa\Util\Point;

abstract class Cell
{
    const IMAGE_PATH = '';
    const TYPE = '';
    const NAME = '';

    protected ?string $imagePath;
    protected ?string $type;
    protected Point $point;

    protected int $population;


    public function __construct(...$data)
    {
        $this->point = new Point($data['point']->x, $data['point']->y);
        $this->imagePath = null;
        $this->population = 0;
    }

    public static function create($data)
    {
        return new static($data);
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,//get_class($this),
            'data' => [
                'point' => $this->point,
                'image_path' => $this->imagePath,
                'info' => $this->getInfoString(),
            ]
        ];
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInfoString(): string
    {
        return '';
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    static public function fromJson(string $type, $data): Cell
    {
//        $point = new Point($data->point->x, $data->point->y);
////        dd($data);
//        $args = [
//            'point' => $point,
//            'image_path' => $data->image_path,
//            'info' => $data->info,
//        ];
        return new (CellTypeConst::getClassByType($type))(...get_object_vars($data));
    }

//    islandとstatusはモデルと別クラスに切り出す?
//    public function elapse(Island $island, Status $status, Terrain $terrain): Cell
//    {
//        return new $this;
//    }
}
