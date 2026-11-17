import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import type { DefineComponent } from 'vue'
import AdminAppLayout from './shared/Layouts/Admin/AdminAppLayout.vue'
import { adminAreas, isAdminPathActive } from './shared/Layouts/Admin/useAdminNavigation'

type PageModule = {
    default: DefineComponent
}

const pages = import.meta.glob<PageModule>(
    ['./manage/Pages/**/*.vue', './review/Pages/**/*.vue', './audit/Pages/**/*.vue']
)

createInertiaApp({
    resolve: (name, page) => {
        if (!page) {
            throw new Error(`Cannot resolve administrative page "${name}" without page URL.`)
        }

        const area = adminAreas.find(area => isAdminPathActive(page.url, area.href))?.id

        if (!area) {
            throw new Error(`Cannot determine administrative area for "${page.url}".`)
        }

        const loadPage = pages[`./${area}/Pages/${name}.vue`]

        if (!loadPage) {
            throw new Error(`Unable to resolve administrative page "${name}" for "${area}".`)
        }

        return loadPage().then(pageModule => {
            const component = pageModule.default
            component.layout = component.layout || AdminAppLayout

            return component
        })
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
