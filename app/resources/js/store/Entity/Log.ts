export interface Log {
    turn: number,
    summary?: LogSummary,
    texts: LogText[][],
    rawTexts: LogText[][]
}

export interface LogSummary {
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

/**
 * PHPのコントローラから渡されてくるLogのデータ
 */
export interface LogProps {
    data: string[],
    turn: number
}

/**
 * PHPのコントローラから渡されてくるSummaryのデータ
 */
export interface SummaryProps {
    turn: number,
    foods: number,
    funds: number,
    resources: number,
    population: number,
    development_points: number,
}

export class LogParser {
    parse(logs: LogProps[], summaries?: SummaryProps[]) {
        const result: Log[] = []
        for (const turnLog of logs) {
            const texts: LogText[][] = [];
            const rawTexts: LogText[][] = [];

            // dataのparse
            for (const line of turnLog.data) {
                const parsed: LogText[] = JSON.parse(line);
                if (parsed[1].text === "収支") continue; // 収支のログを削除
                const contexts: LogText[] = parsed.filter(txt => txt.text !== '');// 空文字のログを削除
                texts.push(contexts.slice(1));
                rawTexts.push(contexts);
            }

            const data: Log = {
                turn: turnLog.turn,
                texts: texts.reverse(),
                rawTexts: rawTexts.reverse()
            }

            if (summaries !== null && summaries !== undefined && summaries.length > 0) {
                const summary = summaries?.find(summary => summary.turn === turnLog.turn);
                if(summary !== null && summary !== undefined) {
                    data.summary = {
                        foods: summary.foods,
                        funds: summary.funds,
                        resources: summary.resources,
                        population: summary.population,
                        points: summary.development_points
                    }
                }
            }
            result.push(data)
        }
        return result;
    }
}
