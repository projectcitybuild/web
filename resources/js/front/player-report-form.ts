const fileDropzones = Array.from(document.querySelectorAll<HTMLElement>('[data-file-dropzone]'))

if (fileDropzones.length > 0) {
    const overlay = document.createElement('div')
    overlay.className = 'pointer-events-none fixed inset-0 z-50 hidden place-items-center bg-gray-950/70 p-6 text-center backdrop-blur-sm'
    overlay.setAttribute('aria-hidden', 'true')

    const overlayMessage = document.createElement('span')
    overlayMessage.className = 'rounded-xl border border-blue-500 bg-gray-900 px-6 py-4 text-lg font-semibold text-gray-100 shadow-xl'
    overlayMessage.textContent = 'Drop files inside the attachment area'
    overlay.append(overlayMessage)
    document.body.append(overlay)

    let dragDepth = 0

    function isFileDrag(event: DragEvent): boolean {
        return Array.from(event.dataTransfer?.types ?? []).includes('Files')
    }

    function setDraggingState(isDragging: boolean): void {
        overlay?.classList.toggle('hidden', !isDragging)

        if (!isDragging) {
            fileDropzones.forEach(dropzone => {
                delete dropzone.dataset.dragging
            })
        }
    }

    document.addEventListener('dragenter', event => {
        if (!isFileDrag(event)) {
            return
        }
        dragDepth += 1
        setDraggingState(true)
    })

    document.addEventListener('dragover', event => {
        if (!isFileDrag(event)) {
            return
        }

        event.preventDefault()

        if (event.dataTransfer) {
            event.dataTransfer.dropEffect = 'copy'
        }
    })

    document.addEventListener('dragleave', event => {
        if (!isFileDrag(event)) {
            return
        }

        dragDepth = Math.max(0, dragDepth - 1)

        if (dragDepth === 0) {
            setDraggingState(false)
        }
    })

    document.addEventListener('drop', event => {
        if (!isFileDrag(event)) {
            return
        }
        event.preventDefault()
        dragDepth = 0
        setDraggingState(false)
    })

    fileDropzones.forEach(dropzone => {
        const input = dropzone.querySelector<HTMLInputElement>('[data-file-dropzone-input]')
        const fileNames = dropzone.querySelector<HTMLElement>('[data-file-dropzone-file-names]')

        if (!input) {
            return
        }
        dropzone.addEventListener('dragenter', event => {
            if (!isFileDrag(event)) {
                return
            }
            event.preventDefault()
            dropzone.dataset.dragging = 'true'
        })

        dropzone.addEventListener('dragover', event => {
            if (isFileDrag(event)) {
                event.preventDefault()
            }
        })

        dropzone.addEventListener('dragleave', event => {
            if (!isFileDrag(event)) {
                return
            }
            if (event.relatedTarget instanceof Node && dropzone.contains(event.relatedTarget)) {
                return
            }
            delete dropzone.dataset.dragging
        })

        dropzone.addEventListener('drop', event => {
            if (!isFileDrag(event) || !event.dataTransfer) {
                return
            }
            event.preventDefault()
            delete dropzone.dataset.dragging

            const transfer = new DataTransfer()

            Array.from(input.files ?? []).forEach(file => transfer.items.add(file))
            Array.from(event.dataTransfer.files).forEach(file => transfer.items.add(file))
            input.files = transfer.files
            input.dispatchEvent(new Event('change', { bubbles: true }))
        })

        input.addEventListener('change', () => {
            if (!fileNames) {
                return
            }
            const names = Array.from(input.files ?? []).map(file => file.name)
            fileNames.textContent = names.join(', ')
            fileNames.classList.toggle('hidden', names.length === 0)
        })
    })
}

const form = document.querySelector<HTMLFormElement>('[data-player-report-form]')

if (form) {
    const steps = Array.from(form.querySelectorAll<HTMLElement>('[data-report-step]'))
    const previousButton = form.querySelector<HTMLButtonElement>('[data-report-previous]')
    const nextButton = form.querySelector<HTMLButtonElement>('[data-report-next]')
    const submitButton = form.querySelector<HTMLButtonElement>('[data-report-submit]')
    const counter = form.querySelector<HTMLElement>('[data-step-counter]')
    const progress = form.querySelector<HTMLElement>('[data-step-progress]')
    let activeStep = 0

    form.noValidate = true

    function showStep(index: number, focus = false): void {
        activeStep = index

        steps.forEach((step, stepIndex) => {
            step.hidden = stepIndex !== activeStep
        })

        if (previousButton) {
            previousButton.hidden = activeStep === 0
        }

        if (nextButton) {
            nextButton.hidden = activeStep === steps.length - 1
        }

        if (submitButton) {
            submitButton.hidden = activeStep !== steps.length - 1
        }

        if (counter) {
            counter.textContent = `Step ${activeStep + 1} of ${steps.length}`
        }

        if (progress) {
            progress.style.width = `${((activeStep + 1) / steps.length) * 100}%`
        }

        if (focus) {
            steps[activeStep]?.querySelector<HTMLElement>('input, textarea, button')?.focus()
        }
    }

    function focusFirstInvalidField(): boolean {
        const invalidField = form?.querySelector<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>(':invalid')
        if (!invalidField) {
            return false
        }

        const stepIndex = steps.findIndex(step => step.contains(invalidField))
        if (stepIndex >= 0) {
            showStep(stepIndex)
        }

        invalidField.reportValidity()
        return true
    }

    nextButton?.addEventListener('click', () => {
        const invalidField = steps[activeStep]?.querySelector<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>(':invalid')
        if (invalidField) {
            invalidField.reportValidity()
            return
        }

        showStep(Math.min(activeStep + 1, steps.length - 1), true)
    })

    previousButton?.addEventListener('click', () => {
        showStep(Math.max(activeStep - 1, 0), true)
    })

    form.addEventListener('submit', event => {
        if (focusFirstInvalidField()) {
            event.preventDefault()
        }
    })

    const firstErrorStep = steps.findIndex(step => step.querySelector('[aria-invalid="true"], .text-red-600'))
    showStep(form.dataset.hasErrors === '1' && firstErrorStep >= 0 ? firstErrorStep : 0)
}
