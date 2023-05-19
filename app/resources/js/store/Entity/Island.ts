import {Terrain} from "./Terrain";

export interface Island {
    id: number,
    name: string,
    owner_name: string,
    terrains?: Terrain[],
}
