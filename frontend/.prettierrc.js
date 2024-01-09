/** @type {import("prettier").Config} */
const config = {
  trailingComma: 'none',
  printWidth: 120,
  tabWidth: 2,
  useTabs: false,
  semi: false,
  singleQuote: true,
  endOfLine: 'lf',
  bracketSameLine: true,
  vueIndentScriptAndStyle: false,
  plugins: ['prettier-plugin-tailwindcss']
}

export default config
