export interface NewLog {
    turn: number,
    islandId?: number,
    islandName?: string,
    islandLink?: string,
    diff?: LogStatus,
    texts: LogText[][],
    rawTexts: LogText[][]
}

export interface LogStatus {
    foods: number,
    funds: number,
    resources: number,
    population: number,
    points: number,
}

export interface LogText {
    text: string,
    link?: string,
    style?: string
}

interface IslandInfo {
    id: number,
    name: string,
    link: string,
}

export class LogParser {
    parse(logs: { log: string }[]) {
        const result: NewLog[] = [];
        let turnLog: NewLog
        console.log(logs);
        for (const [index, raw] of logs.entries()) {
            // 行の解析
            const log: LogText[] = JSON.parse(raw.log);
            const turn = this.getTurn(log);
            console.log("turnlog: " + turn);
            console.log(log);
            const status: LogStatus | null = this.getStatus(log);
            const island: IslandInfo | null = this.getIslandInfo(log);

            // 初回処理
            if (index === 0) {
                turnLog = {turn: turn, texts: [], rawTexts: []}
            }
            // ターンが違う or Islandが違うとき1個分のログをresultに格納
            else if (turnLog.turn !== turn ||
                (turnLog.islandId !== undefined && island !== null && turnLog.islandId !== island.id)) {
                turnLog.texts.reverse();
                result.push(turnLog);
                turnLog = {turn: turn, texts: [], rawTexts: []};
                console.log("----- turn:" + turn + " change turn")
            }

            // データの格納
            if (status !== null && turnLog.diff === undefined) {
                turnLog.diff = status;
            }
            if (island !== null) {
                if (turnLog.islandId === undefined) {
                    turnLog.islandId = island.id;
                    turnLog.islandName = island.name;
                    turnLog.islandLink = island.link;
                }
                console.log(log)
            }
            if (status === null) turnLog.texts.push(log.slice(1));
            turnLog.rawTexts.push(log);
        }
        // finalize
        result.push(turnLog);
        return result;
    }

    /**
     * ログ情報からターン数を抽出
     * @param texts 1行分のログ
     * @return ターン数
     */
    getTurn(texts: LogText[]): number {
        const turnText = texts[0].text;
        const res = turnText.match(/[0-9]+/g);
        if (res.length > 0) return parseInt(res[0]);
        else throw new Error("ログデータの先頭にターン情報が見つかりませんでした。");
    }

    /**
     * ログ情報からステータス増減情報を抽出
     * @param texts 1行分のログ
     * @return LogStatus or null
     */
    getStatus(texts: LogText[]): LogStatus | null {
        if (texts[1].text !== "収支") return null;
        return {
            foods: parseInt(texts[4].text.match(/[0-9\-]+/)[0]),
            funds: parseInt(texts[7].text.match(/[0-9\-]+/)[0]),
            resources: parseInt(texts[10].text.match(/[0-9\-]+/)[0]),
            population: parseInt(texts[13].text.match(/[0-9\-]+/)[0]),
            points: parseInt(texts[16].text.match(/[0-9\-]+/)[0])
        }
    }

    getIslandInfo(texts: LogText[]): IslandInfo | null {
        for(const target of texts) {
            if (target.link !== null && target.link !== undefined) {
                return {
                    id: parseInt(target.link.match(/([^/]+)$/)[0]),
                    name: target.text,
                    link: target.link
                }
            }
        }
        return null;
    }
}
