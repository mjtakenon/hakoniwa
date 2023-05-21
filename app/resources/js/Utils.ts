export const stringEquals = (val1: string|null|undefined, val2: string|null|undefined): boolean => {
    if (val1 === null || val1 === undefined) val1 = ""; else val1 = val1.trim();
    if (val2 === null || val2 === undefined) val2 = ""; else val2 = val2.trim();

    return val1 === val2;
}
