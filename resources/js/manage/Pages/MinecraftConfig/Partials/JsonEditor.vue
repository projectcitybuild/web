<script setup lang="ts">
import { basicSetup, EditorView } from 'codemirror'
import { syntaxTree } from '@codemirror/language'
import { json } from '@codemirror/lang-json'
import type { SyntaxNode } from '@lezer/common'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

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
                    backgroundColor: '#f8fafc',
                    color: '#111827',
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
                    backgroundColor: '#f1f5f9',
                    color: '#64748b',
                    borderRight: '1px solid #e2e8f0',
                },
                '.cm-activeLine': {
                    backgroundColor: '#eef2f5',
                },
                '.cm-activeLineGutter': {
                    backgroundColor: '#e2e8f0',
                },
                '.cm-cursor': {
                    borderLeftColor: '#0f766e',
                },
                '.cm-selectionBackground, &.cm-focused .cm-selectionBackground': {
                    backgroundColor: '#a5f3fc',
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
    height: min(60rem, max(26rem, calc(100dvh - 22rem)));
    overflow: hidden;
}

.json-editor :deep(.cm-editor) {
    height: 100%;
    outline: none;
}

.json-editor :deep(.cm-scroller) {
    overflow: auto;
}
</style>
