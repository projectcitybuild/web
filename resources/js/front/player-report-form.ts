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
