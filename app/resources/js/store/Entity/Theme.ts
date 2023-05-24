export type ThemeType = 'light' | 'dark'

export interface Theme{
    name: string,
    themeClass: string
    type: ThemeType
}

export const defaultTheme: Theme = {
    name: "light",
    themeClass: "theme-light",
    type: "light"
}
