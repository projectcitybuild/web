import { router } from '@inertiajs/vue3'
import { onBeforeUnmount } from 'vue'

interface FormState {
    isDirty: boolean,
    processing: boolean,
}

export function useUnsavedChangesWarning(form: FormState): void {
    const removeInertiaListener = router.on('before', (event) => {
        if (event.detail.visit.method !== 'get' || !form.isDirty || form.processing) {
            return
        }

        return window.confirm('You have unsaved changes. Are you sure you want to leave this page?')
    })

    function warnBeforeUnload(event: BeforeUnloadEvent): void {
        if (!form.isDirty || form.processing) {
            return
        }

        event.preventDefault()
        event.returnValue = ''
    }

    if (typeof window !== 'undefined') {
        window.addEventListener('beforeunload', warnBeforeUnload)
    }

    onBeforeUnmount(() => {
        removeInertiaListener()
        if (typeof window !== 'undefined') {
            window.removeEventListener('beforeunload', warnBeforeUnload)
        }
    })
}
