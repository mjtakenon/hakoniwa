import axios from "axios";
import {store} from "./store";

export namespace api {
    export async function sendPlan() {
        return await axios.put(
            '/islands/' + store.state.island.id + '/plans' ,
            {
                plan: JSON.stringify(store.state.plan),
            }
        )
    }
}
