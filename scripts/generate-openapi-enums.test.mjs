import assert from 'node:assert/strict'
import test from 'node:test'
import { generateEnumSource } from './openapi-enum-generator.mjs'

function generate(spec, declarations = '') {
    return generateEnumSource(spec, declarations)
}

function emptySpec() {
    return { components: { schemas: {} }, paths: {} }
}

test('generates named string enums', () => {
    const source = generate({
        ...emptySpec(),
        components: {
            schemas: {
                BuildStatus: { type: 'string', enum: ['pending', 'complete'] },
            },
        },
    })

    assert.match(source, /export const BuildStatus =/)
    assert.match(source, /"complete": "complete"/)
})

test('discovers enums nested in properties, array items, and composed schemas', () => {
    const source = generate({
        ...emptySpec(),
        components: {
            schemas: {
                Build: {
                    type: 'object',
                    properties: {
                        state: { type: 'string', enum: ['ready', 'failed'] },
                        events: { type: 'array', items: { type: 'string', enum: ['added', 'removed'] } },
                        metadata: { type: 'object', additionalProperties: { type: 'string', enum: ['public', 'private'] } },
                        result: { oneOf: [{ type: 'string', enum: ['success', 'failure'] }] },
                        priority: { anyOf: [{ type: 'string', enum: ['low', 'high'] }] },
                        visibility: { allOf: [{ type: 'string', enum: ['visible', 'hidden'] }] },
                    },
                },
            },
        },
    })

    assert.match(source, /export const BuildState =/)
    assert.match(source, /export const BuildEventsItem =/)
    assert.match(source, /export const BuildMetadataAdditionalProperty =/)
    assert.match(source, /export const BuildResultOneOf1 =/)
    assert.match(source, /export const BuildPriorityAnyOf1 =/)
    assert.match(source, /export const BuildVisibilityAllOf1 =/)
})

test('discovers inline request and response enums', () => {
    const source = generate({
        ...emptySpec(),
        paths: {
            '/v3/builds': {
                post: {
                    operationId: 'minecraftBuild.store',
                    requestBody: {
                        content: {
                            'application/json': {
                                schema: { type: 'object', properties: { status: { type: 'string', enum: ['queued'] } } },
                            },
                        },
                    },
                    responses: {
                        '201': {
                            content: {
                                'application/json': {
                                    schema: { type: 'object', properties: { status: { type: 'string', enum: ['created'] } } },
                                },
                            },
                        },
                    },
                },
            },
        },
    })

    assert.match(source, /export const MinecraftBuildStoreRequestStatus =/)
    assert.match(source, /export const MinecraftBuildStoreResponseStatus =/)
})

test('disambiguates response enums by status code and media type', () => {
    const source = generate({
        ...emptySpec(),
        paths: {
            '/v3/builds': {
                get: {
                    operationId: 'build.show',
                    responses: {
                        '200': {
                            content: {
                                'application/json': { schema: { type: 'string', enum: ['ready'] } },
                                'application/foo-bar': { schema: { type: 'string', enum: ['foo-bar'] } },
                                'application/foo.bar': { schema: { type: 'string', enum: ['foo_bar'] } },
                                'text/plain': { schema: { type: 'string', enum: ['queued'] } },
                            },
                        },
                        '404': {
                            content: {
                                'application/json': { schema: { type: 'string', enum: ['missing'] } },
                            },
                        },
                    },
                },
            },
        },
    })

    assert.match(source, /export const BuildShowResponse200ApplicationJson =/)
    assert.match(source, /export const BuildShowResponse200ApplicationFooBarMediaType1 =/)
    assert.match(source, /export const BuildShowResponse200ApplicationFooBarMediaType2 =/)
    assert.match(source, /export const BuildShowResponse200TextPlain =/)
    assert.match(source, /export const BuildShowResponse404 =/)
})

test('discovers path and operation parameter enums and response headers', () => {
    const source = generate({
        ...emptySpec(),
        paths: {
            '/v3/builds/{id}': {
                parameters: [{ name: 'scope', in: 'path', schema: { type: 'string', enum: ['public', 'private'] } }],
                get: {
                    operationId: 'build.show',
                    parameters: [{ name: 'view', in: 'query', schema: { type: 'string', enum: ['full', 'compact'] } }],
                    responses: {
                        '200': {
                            headers: { 'X-Visibility': { schema: { type: 'string', enum: ['visible', 'hidden'] } } },
                        },
                    },
                },
            },
        },
    })

    assert.match(source, /export const V3BuildsIdPathScope =/)
    assert.match(source, /export const BuildShowParameterView =/)
    assert.match(source, /export const BuildShowResponseHeaderXVisibility =/)
})

test('resolves component parameter references with escaped pointer names', () => {
    const source = generate({
        ...emptySpec(),
        components: {
            schemas: {},
            parameters: {
                'Build/Status': { name: 'state', in: 'query', schema: { type: 'string', enum: ['ready', 'failed'] } },
            },
        },
        paths: {
            '/builds': {
                get: {
                    operationId: 'build.index',
                    parameters: [{ $ref: '#/components/parameters/Build~1Status' }],
                },
            },
        },
    })

    assert.match(source, /export const BuildIndexParameterState =/)
})

test('generates Laravel Data string unions', () => {
    const source = generate(emptySpec(), 'type ApplicationStatus = "pending" | "approved";')

    assert.match(source, /export const ApplicationStatus =/)
    assert.match(source, /"approved": "approved"/)
})

test('normalizes enum member casing without losing word boundaries', () => {
    const source = generate({
        ...emptySpec(),
        components: {
            schemas: {
                NamingStyle: {
                    type: 'string',
                    enum: ['in_progress', 'UPPER_SNAKE_CASE', 'PascalCase', 'camelCase', 'kebab-case', 'XMLHttpRequest'],
                },
            },
        },
    })

    for (const memberName of ['inProgress', 'upperSnakeCase', 'pascalCase', 'camelCase', 'kebabCase', 'xmlHttpRequest']) {
        assert.match(source, new RegExp(`    "${memberName}":`))
    }
})

test('reports invalid and colliding normalized enum members', () => {
    assert.throws(() => generate({
        ...emptySpec(),
        components: {
            schemas: {
                InvalidMember: { type: 'string', enum: ['not.valid'] },
            },
        },
    }), /contains a case name that is not a valid TypeScript identifier/)

    assert.throws(() => generate({
        ...emptySpec(),
        components: {
            schemas: {
                CollidingMembers: { type: 'string', enum: ['in_progress', 'inProgress'] },
            },
        },
    }), /contains duplicate case names/)

    const source = generate(emptySpec(), 'type ValidMember = "valid_value" | "valid"; type WebManagePermission = "web.manage.roles.view" | "web.manage.roles.edit";')

    assert.match(source, /export const ValidMember =/)
    assert.doesNotMatch(source, /export const WebManagePermission =/)
})

test('deduplicates equivalent definitions after normalizing names', () => {
    const source = generate({
        ...emptySpec(),
        components: {
            schemas: {
                'Build-Status': { type: 'string', enum: ['pending', 'complete'] },
                'Build Status': { type: 'string', enum: ['pending', 'complete'] },
            },
        },
    })

    assert.equal(source.match(/export const BuildStatus =/g)?.length, 1)
})

test('emits enums in deterministic name order', () => {
    const schemas = {
        ZebraStatus: { type: 'string', enum: ['ready', 'failed'] },
        AlphaStatus: { type: 'string', enum: ['open', 'closed'] },
    }
    const first = generate({ ...emptySpec(), components: { schemas } })
    const second = generate({ ...emptySpec(), components: { schemas: Object.fromEntries(Object.entries(schemas).reverse()) } })

    assert.equal(first, second)
    assert.ok(first.indexOf('export const AlphaStatus') < first.indexOf('export const ZebraStatus'))
})

test('reports conflicting definitions assigned the same normalized name', () => {
    assert.throws(() => generate({
        ...emptySpec(),
        components: {
            schemas: {
                'Build-Status': { type: 'string', enum: ['pending', 'complete'] },
                'Build Status': { type: 'string', enum: ['pending', 'failed'] },
            },
        },
    }), /Conflicting string enum name BuildStatus .*components\.schemas\.Build Status/)
})

test('ignores non-string and mixed-value enums', () => {
    const source = generate({
        ...emptySpec(),
        components: {
            schemas: {
                NumericStatus: { type: 'integer', enum: [0, 1] },
                MixedStatus: { type: 'string', enum: ['ready', 1] },
                StringStatus: { type: 'string', enum: ['ready', 'failed'] },
            },
        },
    })

    assert.doesNotMatch(source, /export const NumericStatus =/)
    assert.doesNotMatch(source, /export const MixedStatus =/)
    assert.match(source, /export const StringStatus =/)
})
