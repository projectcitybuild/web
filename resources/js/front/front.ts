// Inform Vite where static assets are located
import.meta.glob([
    '../../images/**',
]);

// Server feed copy-to-clipboard
document.querySelectorAll(`[data-server-address]`).forEach(element => {
    let value = element.getAttribute("data-server-address")
    element.addEventListener("click", () => copyToClipboard(value))
})

function copyToClipboard(text: string) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard')
    })
}
