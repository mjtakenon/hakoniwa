import axios from "axios";
import {store} from "./store";

export namespace api {
    export async function putPlan() {
        console.debug('PUT', '/islands/' + store.state.island.id + '/plans')
        return await axios.put(
            '/islands/' + store.state.island.id + '/plans' ,
            {
                plan: JSON.stringify(store.state.plans),
            }
        )
    }
}
