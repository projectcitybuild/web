<script setup lang="ts">
import { basicSetup, EditorView } from 'codemirror'
import { json } from '@codemirror/lang-json'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps<{
    modelValue: string,
    ariaLabel: string,
}>()

const emit = defineEmits<{
    'update:modelValue': [value: string],
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