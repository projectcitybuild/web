import { createApp } from 'vue/dist/vue.esm-bundler';

// Inform Vite where static assets are located
import.meta.glob([
    '../../images/**',
]);

const app = createApp({});
app.mount('#app');

const dropdownLinks = document.querySelectorAll(".nav-dropdown")

dropdownLinks.forEach(node => {
    node.addEventListener("click", () => {
        console.log(node.classList)

        // Collapse every other submenu first
        // FIXME: optimize this later
        dropdownLinks.forEach(otherNode => {
            if (!node.isEqualNode(otherNode)) {
                otherNode.nextElementSibling.classList.remove("active")
            }
        })

        let menu = node.nextElementSibling
        menu.classList.toggle("active")
    })
})

// Server feed copy-to-clipboard
document.querySelectorAll(`[data-server-address]`).forEach(element => {
    let value = element.getAttribute("data-server-address")
    element.addEventListener("click", () => copyToClipboard(value))
})

function copyToClipboard(text: string) {
    navigator.clipboard.writeText(text)
        .then(() => {
            alert('Copied to clipboard')
        })
}
