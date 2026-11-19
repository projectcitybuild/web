import ts from 'typescript'

function normalizeEnumName(suggestedName) {
    const parts = String(suggestedName).match(/[A-Za-z0-9_$]+/g) ?? []
    let name = parts.map((part) => part[0].toUpperCase() + part.slice(1)).join('')

    if (name === '') {
        name = 'GeneratedEnum'
    }

    if (!/^[A-Za-z_$]/.test(name)) {
        name = `_${name}`
    }

    return name
}

function getMemberNames(schema, name, source) {
    const enumNames = schema['x-enumNames']

    if (enumNames !== undefined && (!Array.isArray(enumNames) || enumNames.length !== schema.enum.length || !enumNames.every((memberName) => typeof memberName === 'string'))) {
        throw new Error(`String enum at ${source} has invalid case names: ${name}`)
    }

    const memberNames = schema.enum.map((value, index) => {
        const caseName = enumNames?.[index]
        return caseName
            ? caseName.toLowerCase().replace(/_([a-z0-9])/g, (_, character) => character.toUpperCase())
            : value.replace(/_([a-z0-9])/g, (_, character) => character.toUpperCase())
    })

    if (!memberNames.every((memberName) => /^[A-Za-z_$][\w$]*$/.test(memberName))) {
        throw new Error(`String enum at ${source} contains a case name that is not a valid TypeScript identifier: ${name}`)
    }

    if (new Set(memberNames).size !== memberNames.length) {
        throw new Error(`String enum at ${source} contains duplicate case names: ${name}`)
    }

    return memberNames
}

function decodePointerPart(value) {
    return value.replace(/~1/g, '/').replace(/~0/g, '~')
}

function resolveComponentReference(spec, value, collection) {
    const match = value?.$ref?.match(new RegExp(`^#/components/${collection}/(.+)$`))

    if (!match) {
        return value
    }

    return spec.components?.[collection]?.[decodePointerPart(match[1])] ?? value
}

export function generateEnumSource(spec, declarations = '') {
    const enums = new Map()

    function registerEnum(suggestedName, schema, source) {
        if (schema?.type !== 'string' || !Array.isArray(schema.enum) || schema.enum.length === 0 || !schema.enum.every((value) => typeof value === 'string')) {
            return
        }

        const name = normalizeEnumName(suggestedName)
        const memberNames = getMemberNames(schema, name, source)
        const definition = JSON.stringify({ values: schema.enum, memberNames })
        const existing = enums.get(name)

        if (existing && existing.definition !== definition) {
            throw new Error(`Conflicting string enum name ${name} at ${source}; it was already defined at ${existing.source}`)
        }

        if (!existing) {
            enums.set(name, { definition, memberNames, schema, source })
        }
    }

    function collectSchema(schema, suggestedName, source) {
        if (!schema || typeof schema !== 'object' || Array.isArray(schema) || schema.$ref) {
            return
        }

        registerEnum(suggestedName, schema, source)

        for (const [propertyName, propertySchema] of Object.entries(schema.properties ?? {})) {
            collectSchema(propertySchema, `${suggestedName}${normalizeEnumName(propertyName)}`, `${source}.properties.${propertyName}`)
        }

        if (Array.isArray(schema.items)) {
            schema.items.forEach((item, index) => collectSchema(item, `${suggestedName}Item${index + 1}`, `${source}.items[${index}]`))
        } else if (schema.items) {
            collectSchema(schema.items, `${suggestedName}Item`, `${source}.items`)
        }

        if (schema.additionalProperties && typeof schema.additionalProperties === 'object') {
            collectSchema(schema.additionalProperties, `${suggestedName}AdditionalProperty`, `${source}.additionalProperties`)
        }

        for (const composition of ['oneOf', 'anyOf', 'allOf']) {
            for (const [index, composedSchema] of (schema[composition] ?? []).entries()) {
                collectSchema(composedSchema, `${suggestedName}${normalizeEnumName(composition)}${index + 1}`, `${source}.${composition}[${index}]`)
            }
        }
    }

    function collectContent(content, suggestedName, source) {
        for (const [mediaType, media] of Object.entries(content ?? {})) {
            collectSchema(media?.schema, suggestedName, `${source}.content.${mediaType}.schema`)
        }
    }

    function collectParameter(parameter, suggestedName, source) {
        const resolvedParameter = resolveComponentReference(spec, parameter, 'parameters')

        if (!resolvedParameter) {
            return
        }

        const parameterName = `${suggestedName}${normalizeEnumName(resolvedParameter.name ?? 'Parameter')}`
        collectSchema(resolvedParameter.schema, parameterName, `${source}.schema`)
        collectContent(resolvedParameter.content, parameterName, source)
    }

    function collectRequestBody(requestBody, suggestedName, source) {
        const resolvedRequestBody = resolveComponentReference(spec, requestBody, 'requestBodies')
        collectContent(resolvedRequestBody?.content, suggestedName, source)
    }

    function collectResponse(response, suggestedName, source) {
        const resolvedResponse = resolveComponentReference(spec, response, 'responses')

        if (!resolvedResponse) {
            return
        }

        collectContent(resolvedResponse.content, suggestedName, source)

        for (const [headerName, header] of Object.entries(resolvedResponse.headers ?? {})) {
            const resolvedHeader = resolveComponentReference(spec, header, 'headers')
            collectSchema(resolvedHeader?.schema, `${suggestedName}Header${normalizeEnumName(headerName)}`, `${source}.headers.${headerName}.schema`)
            collectContent(resolvedHeader?.content, `${suggestedName}Header${normalizeEnumName(headerName)}`, `${source}.headers.${headerName}`)
        }
    }

    for (const [schemaName, schema] of Object.entries(spec.components?.schemas ?? {})) {
        collectSchema(schema, schemaName, `components.schemas.${schemaName}`)
    }

    for (const [parameterName, parameter] of Object.entries(spec.components?.parameters ?? {})) {
        collectParameter(parameter, `${normalizeEnumName(parameterName)}Parameter`, `components.parameters.${parameterName}`)
    }

    for (const [requestBodyName, requestBody] of Object.entries(spec.components?.requestBodies ?? {})) {
        collectRequestBody(requestBody, `${normalizeEnumName(requestBodyName)}Request`, `components.requestBodies.${requestBodyName}`)
    }

    for (const [responseName, response] of Object.entries(spec.components?.responses ?? {})) {
        collectResponse(response, `${normalizeEnumName(responseName)}Response`, `components.responses.${responseName}`)
    }

    for (const [headerName, header] of Object.entries(spec.components?.headers ?? {})) {
        collectSchema(header?.schema, `${normalizeEnumName(headerName)}Header`, `components.headers.${headerName}.schema`)
        collectContent(header?.content, `${normalizeEnumName(headerName)}Header`, `components.headers.${headerName}`)
    }

    for (const [apiPath, pathItem] of Object.entries(spec.paths ?? {})) {
        const pathName = normalizeEnumName(apiPath.replace(/[{}]/g, ''))

        for (const parameter of pathItem.parameters ?? []) {
            collectParameter(parameter, `${pathName}Path`, `paths.${apiPath}.parameters`)
        }

        for (const [method, operation] of Object.entries(pathItem)) {
            if (!['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'].includes(method)) {
                continue
            }

            const operationName = normalizeEnumName(operation.operationId ?? `${method}_${apiPath}`)

            for (const parameter of operation.parameters ?? []) {
                collectParameter(parameter, `${operationName}Parameter`, `paths.${apiPath}.${method}.parameters`)
            }

            collectRequestBody(operation.requestBody, `${operationName}Request`, `paths.${apiPath}.${method}.requestBody`)

            for (const [statusCode, response] of Object.entries(operation.responses ?? {})) {
                collectResponse(response, `${operationName}Response`, `paths.${apiPath}.${method}.responses.${statusCode}`)
            }
        }
    }

    const sourceFile = ts.createSourceFile('generated.d.ts', declarations, ts.ScriptTarget.Latest, true)

    function collectStringUnions(node) {
        if (ts.isTypeAliasDeclaration(node) && ts.isUnionTypeNode(node.type)) {
            const values = node.type.types.map((type) => {
                if (!ts.isLiteralTypeNode(type) || !ts.isStringLiteral(type.literal)) {
                    return null
                }

                return type.literal.text
            })

            if (values.every((value) => value !== null) && new Set(values).size === values.length) {
                const memberNames = values.map((value) => value.replace(/_([a-z0-9])/g, (_, character) => character.toUpperCase()))
                const areValidMemberNames = memberNames.every((memberName) => /^[A-Za-z_$][\w$]*$/.test(memberName))

                if (areValidMemberNames && new Set(memberNames).size === memberNames.length) {
                    registerEnum(node.name.text, { type: 'string', enum: values }, `generated.d.ts type ${node.name.text}`)
                }
            }
        }

        ts.forEachChild(node, collectStringUnions)
    }

    collectStringUnions(sourceFile)

    if (enums.size === 0) {
        throw new Error('No string enums found in the OpenAPI document or Laravel Data declarations.')
    }

    const generatedDeclarations = [...enums]
        .sort(([left], [right]) => left < right ? -1 : left > right ? 1 : 0)
        .map(([name, { schema, memberNames }]) => {
            const values = schema.enum
                .map((value, index) => `    ${JSON.stringify(memberNames[index])}: ${JSON.stringify(value)},`)
                .join('\n')

            return `export const ${name} = {\n${values}\n} as const`
        })

    return [
        '// This file is auto-generated by scripts/generate-openapi-enums.mjs. Do not edit directly.',
        '',
        ...generatedDeclarations.flatMap((declaration) => [declaration, '']),
    ].join('\n')
}
