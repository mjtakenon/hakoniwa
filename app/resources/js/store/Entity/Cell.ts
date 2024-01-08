import { Point } from './Point.js'

export interface Cell {
  image_path: string
  info: string
  point: Point
}

export const getCells = () => {
  return {
    city: { path: '/img/hakoniwa/gltf/plain.gltf' },
    factory: { path: '/img/hakoniwa/gltf/plain.gltf' },
    farm: { path: '/img/hakoniwa/gltf/farm.gltf' },
    farm_dome: { path: '/img/hakoniwa/gltf/plain.gltf' },
    forest: { path: '/img/hakoniwa/gltf/forest.gltf' },
    metropolis: { path: '/img/hakoniwa/gltf/plain.gltf' },
    mountain: { path: '/img/hakoniwa/gltf/volcano.gltf' },
    volcano: { path: '/img/hakoniwa/gltf/volcano.gltf' },
    mine: { path: '/img/hakoniwa/gltf/mine.gltf' },
    oilfield: { path: '/img/hakoniwa/gltf/plain.gltf' },
    plain: { path: '/img/hakoniwa/gltf/plain.gltf' },
    sea: { path: '/img/hakoniwa/gltf/sea.gltf' },
    shallow: { path: '/img/hakoniwa/gltf/shallow.gltf' },
    lake: { path: '/img/hakoniwa/gltf/shallow.gltf' },
    large_factory: { path: '/img/hakoniwa/gltf/plain.gltf' },
    town: { path: '/img/hakoniwa/gltf/town.gltf' },
    village: { path: '/img/hakoniwa/gltf/village.gltf' },
    wasteland: { path: '/img/hakoniwa/gltf/wasteland.gltf' },
    missile_base: { path: '/img/hakoniwa/gltf/plain.gltf' },
    seabed_base: { path: '/img/hakoniwa/gltf/plain.gltf' },
    park: { path: '/img/hakoniwa/gltf/plain.gltf' },
    monument_of_agriculture: { path: '/img/hakoniwa/gltf/plain.gltf' },
    monument_of_mining: { path: '/img/hakoniwa/gltf/plain.gltf' },
    monument_of_master: { path: '/img/hakoniwa/gltf/plain.gltf' },
    monument_of_peace: { path: '/img/hakoniwa/gltf/plain.gltf' },
    monument_of_war: { path: '/img/hakoniwa/gltf/plain.gltf' },
    monument_of_conquest: { path: '/img/hakoniwa/gltf/plain.gltf' },
    inora: { path: '/img/hakoniwa/gltf/plain.gltf' },
    red_inora: { path: '/img/hakoniwa/gltf/plain.gltf' },
    dark_inora: { path: '/img/hakoniwa/gltf/plain.gltf' },
    king_inora: { path: '/img/hakoniwa/gltf/plain.gltf' },
    sanjira: { path: '/img/hakoniwa/gltf/plain.gltf' },
    kujira: { path: '/img/hakoniwa/gltf/plain.gltf' },
    hamunemu: { path: '/img/hakoniwa/gltf/plain.gltf' },
    ghost_inora: { path: '/img/hakoniwa/gltf/plain.gltf' },
    slime: { path: '/img/hakoniwa/gltf/plain.gltf' },
    slime_legend: { path: '/img/hakoniwa/gltf/plain.gltf' },
    levinoth: { path: '/img/hakoniwa/gltf/plain.gltf' },
    begenoth: { path: '/img/hakoniwa/gltf/plain.gltf' },
    egg: { path: '/img/hakoniwa/gltf/plain.gltf' },
    transport_ship: { path: '/img/hakoniwa/gltf/plain.gltf' },
    battleship: { path: '/img/hakoniwa/gltf/plain.gltf' },
    submarine: { path: '/img/hakoniwa/gltf/plain.gltf' },
    pirate: { path: '/img/hakoniwa/gltf/plain.gltf' },
    levinoth_battleship: { path: '/img/hakoniwa/gltf/plain.gltf' },
    levinoth_submarine: { path: '/img/hakoniwa/gltf/plain.gltf' }
  }
}
