import axios from "axios";
import {store} from "./store";

export namespace api {
    export async function sendPlan() {
        return await axios.get(
            '/islands/' +1+ '/plans' ,
            {
                params: {
                    plan: JSON.stringify(store.state.plan),
                }
            }
        )
    }
}
