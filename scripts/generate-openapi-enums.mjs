import { readFile, writeFile } from 'node:fs/promises'
import { generateEnumSource } from './openapi-enum-generator.mjs'

const specUrl = new URL('../docs/openapi/v3.json', import.meta.url)
const declarationsUrl = new URL('../resources/js/generated/generated.d.ts', import.meta.url)
const outputUrl = new URL('../resources/js/generated/openapi-enums.ts', import.meta.url)
const spec = JSON.parse(await readFile(specUrl, 'utf8'))
const declarations = await readFile(declarationsUrl, 'utf8')

await writeFile(outputUrl, generateEnumSource(spec, declarations))
