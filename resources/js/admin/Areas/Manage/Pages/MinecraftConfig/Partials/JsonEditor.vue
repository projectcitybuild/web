<script setup lang="ts">
import { basicSetup, EditorView } from 'codemirror'
import { HighlightStyle, syntaxHighlighting, syntaxTree } from '@codemirror/language'
import { json } from '@codemirror/lang-json'
import type { SyntaxNode } from '@lezer/common'
import { tags } from '@lezer/highlight'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

const jsonHighlighting = syntaxHighlighting(HighlightStyle.define([
    { tag: tags.propertyName, color: 'var(--json-editor-property)' },
    { tag: tags.string, color: 'var(--json-editor-string)' },
    { tag: tags.number, color: 'var(--json-editor-number)' },
    { tag: [tags.bool, tags.null], color: 'var(--json-editor-literal)' },
    { tag: [tags.brace, tags.squareBracket, tags.separator], color: 'var(--json-editor-punctuation)' },
]))

const props = defineProps<{
    modelValue: string,
    ariaLabel: string,
}>()

const emit = defineEmits<{
    'update:modelValue': [value: string],
    'select-keys': [keys: string[]],
}>()

const editorElement = ref<HTMLDivElement | null>(null)
let editor: EditorView | null = null

onMounted(() => {
    if (!editorElement.value) {
        return
    }

    editor = new EditorView({
        doc: props.modelValue,
        extensions: [
            basicSetup,
            json(),
            jsonHighlighting,
            EditorView.lineWrapping,
            EditorView.contentAttributes.of({ 'aria-label': props.ariaLabel }),
            EditorView.updateListener.of((update) => {
                if (update.docChanged) {
                    emit('update:modelValue', update.state.doc.toString())
                }

                if (update.docChanged || update.selectionSet) {
                    emit('select-keys', getSelectedKeys(update.view))
                }
            }),
            EditorView.theme({
                '&': {
                    backgroundColor: 'var(--json-editor-background)',
                    color: 'var(--json-editor-foreground)',
                    fontSize: '13px',
                    minHeight: '26rem',
                },
                '.cm-content': {
                    fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
                    padding: '1rem 0',
                },
                '.cm-line': {
                    padding: '0 1rem',
                },
                '.cm-gutters': {
                    backgroundColor: 'var(--json-editor-gutter-background)',
                    color: 'var(--json-editor-gutter-foreground)',
                    borderRight: '1px solid var(--json-editor-gutter-border)',
                },
                '.cm-activeLine': {
                    backgroundColor: 'var(--json-editor-active-line)',
                },
                '.cm-activeLineGutter': {
                    backgroundColor: 'var(--json-editor-active-gutter)',
                },
                '.cm-cursor': {
                    borderLeftColor: 'var(--json-editor-cursor)',
                },
                '.cm-selectionBackground, &.cm-focused .cm-selectionBackground': {
                    backgroundColor: 'var(--json-editor-selection)',
                },
            }),
        ],
        parent: editorElement.value,
    })
})

function getSelectedKeys(view: EditorView): string[] {
    const selection = view.state.selection.main

    if (selection.empty) {
        const key = getSelectedKey(view)
        return key ? [key] : []
    }

    const startLine = view.state.doc.lineAt(selection.from)
    const endLine = view.state.doc.lineAt(selection.to)
    const from = startLine.from
    const to = selection.to === endLine.from && selection.to > selection.from
        ? view.state.doc.line(endLine.number - 1).to
        : endLine.to
    const keys = new Set<string>()

    syntaxTree(view.state).iterate({
        from,
        to,
        enter(node) {
            const syntaxNode = node.node

            if (syntaxNode.name === 'String' && syntaxNode.parent?.name !== 'PropertyName') {
                keys.add(JSON.stringify(getStringPath(syntaxNode, view)))
            }
        },
    })

    return [...keys]
}

function getSelectedKey(view: EditorView): string | null {
    const position = view.state.selection.main.head
    const tree = syntaxTree(view.state)

    for (const side of [-1, 1] as const) {
        const node = tree.resolve(position, side)
        const propertyName = findAncestor(node, 'PropertyName')

        if (propertyName?.parent) {
            const valueNode = propertyName.parent.lastChild
            const stringNode = valueNode ? findFirstString(valueNode) : null

            if (stringNode) {
                return JSON.stringify(getStringPath(stringNode, view))
            }
        }

        const stringNode = findAncestor(node, 'String')

        if (stringNode) {
            return JSON.stringify(getStringPath(stringNode, view))
        }
    }

    return null
}

function findAncestor(node: SyntaxNode | null, name: string): SyntaxNode | null {
    while (node && node.name !== name) {
        node = node.parent
    }

    return node
}

function findFirstString(node: SyntaxNode): SyntaxNode | null {
    if (node.name === 'String') {
        return node
    }

    for (let child = node.firstChild; child; child = child.nextSibling) {
        const stringNode = findFirstString(child)

        if (stringNode) {
            return stringNode
        }
    }

    return null
}

function getStringPath(stringNode: SyntaxNode, view: EditorView): string[] {
    const path: string[] = []
    let child = stringNode

    for (let parent = child.parent; parent; child = parent, parent = parent.parent) {
        if (parent.name === 'Property') {
            const propertyName = findAncestor(parent.firstChild, 'PropertyName')

            if (propertyName) {
                path.push(JSON.parse(view.state.sliceDoc(propertyName.from, propertyName.to)))
            }
        } else if (parent.name === 'Array') {
            let index = 0

            for (
                let sibling = parent.firstChild;
                sibling && (sibling.from !== child.from || sibling.to !== child.to);
                sibling = sibling.nextSibling
            ) {
                if (isJsonValue(sibling.name)) {
                    index++
                }
            }

            path.push(String(index))
        }
    }

    return path.reverse()
}

function isJsonValue(name: string): boolean {
    return ['Array', 'False', 'Null', 'Number', 'Object', 'String', 'True'].includes(name)
}

watch(() => props.modelValue, (value) => {
    if (!editor || editor.state.doc.toString() === value) {
        return
    }

    editor.dispatch({
        changes: {
            from: 0,
            to: editor.state.doc.length,
            insert: value,
        },
    })
})

onBeforeUnmount(() => editor?.destroy())
</script>

<template>
    <div ref="editorElement" class="json-editor" />
</template>

<style scoped>
.json-editor {
    --json-editor-background: #f8fafc;
    --json-editor-foreground: #111827;
    --json-editor-gutter-background: #f1f5f9;
    --json-editor-gutter-foreground: #64748b;
    --json-editor-gutter-border: #e2e8f0;
    --json-editor-active-line: #eef2f5;
    --json-editor-active-gutter: #e2e8f0;
    --json-editor-cursor: #0f766e;
    --json-editor-selection: #a5f3fc;
    --json-editor-property: #1d4ed8;
    --json-editor-string: #15803d;
    --json-editor-number: #b45309;
    --json-editor-literal: #7c3aed;
    --json-editor-punctuation: #475569;
    height: min(60rem, max(26rem, calc(100dvh - 22rem)));
    overflow: hidden;
}

:global(html.dark .json-editor) {
    --json-editor-background: #282c34;
    --json-editor-foreground: #abb2bf;
    --json-editor-gutter-background: #21252b;
    --json-editor-gutter-foreground: #636d83;
    --json-editor-gutter-border: #181a1f;
    --json-editor-active-line: #2c313c;
    --json-editor-active-gutter: #2c313a;
    --json-editor-cursor: #528bff;
    --json-editor-selection: #3e4451;
    --json-editor-property: #e06c75;
    --json-editor-string: #98c379;
    --json-editor-number: #d19a66;
    --json-editor-literal: #56b6c2;
    --json-editor-punctuation: #abb2bf;
}

.json-editor :deep(.cm-editor) {
    height: 100%;
    outline: none;
}

.json-editor :deep(.cm-scroller) {
    overflow: auto;
}
</style>
