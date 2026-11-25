const copyAddressButton = document.querySelector<HTMLButtonElement>('button[data-server-address]')
const copyAddressStatus = document.querySelector<HTMLElement>('[data-copy-address-status]')
const copyAddressLabel = copyAddressButton?.querySelector<HTMLElement>('[data-copy-address-label]')

if (copyAddressButton && copyAddressStatus && copyAddressLabel) {
    copyAddressButton.addEventListener('click', async () => {
        const serverAddress = copyAddressButton.dataset.serverAddress

        if (!serverAddress) {
            console.error('The Minecraft server address is missing from the copy button.')
            copyAddressStatus.textContent = 'The server address is unavailable. Please copy pcbmc.co manually.'

            return
        }

        try {
            await navigator.clipboard.writeText(serverAddress)
            copyAddressLabel.textContent = 'Copied!'
            copyAddressStatus.textContent = 'Address copied. Paste it into Minecraft Java Edition to connect.'

            window.setTimeout(() => {
                copyAddressLabel.textContent = 'Copy address'
            }, 2000)
        } catch (error) {
            console.error('Unable to copy the Minecraft server address.', error)
            copyAddressStatus.textContent = `Clipboard access failed. Please copy ${serverAddress} manually.`
        }
    })
}
