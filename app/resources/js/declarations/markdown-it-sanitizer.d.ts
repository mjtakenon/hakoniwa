declare module 'markdown-it-sanitizer' {
  import { PluginSimple, PluginWithParams, PluginWithOptions } from 'markdown-it'
  const sanitizer: PluginSimple | PluginWithParams | PluginWithOptions
  export default sanitizer
}
