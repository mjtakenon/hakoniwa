export const stringEquals = (val1: string|null|undefined, val2: string|null|undefined): boolean => {
    if (val1 === null || val1 === undefined) val1 = ""; else val1 = val1.trim();
    if (val2 === null || val2 === undefined) val2 = ""; else val2 = val2.trim();

    return val1 === val2;
}

export const formatDate = (date: Date): string => {
    return date.getFullYear() + "年" + (date.getMonth()+1) + "月" + date.getDate() + "日  " +
        (date.getHours() < 10 ? "0" + date.getHours() : date.getHours()) + ":" +
        (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes());
}
