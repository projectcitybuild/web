import { resolve } from 'node:path';
import { pathToFileURL } from 'node:url';

const sleep = milliseconds => new Promise(resolveSleep => setTimeout(resolveSleep, milliseconds));

export async function verifyDeploymentSha({
    url,
    expectedSha,
    attempts = 10,
    retryDelayMs = 3000,
    timeoutMs = 5000,
    fetchImpl = fetch,
    wait = sleep,
    log = console.log,
}) {
    let actualSha = 'unavailable';
    let lastFailure = 'no successful response';

    for (let attempt = 1; attempt <= attempts; attempt += 1) {
        try {
            const response = await fetchImpl(url, {
                headers: { accept: 'application/json' },
                signal: AbortSignal.timeout(timeoutMs),
            });

            if (!response.ok) {
                lastFailure = `HTTP ${response.status}`;
            } else {
                const payload = await response.json();

                if (typeof payload?.commit !== 'string' || !/^[a-f0-9]{40}$/.test(payload.commit)) {
                    actualSha = 'invalid';
                    lastFailure = 'response did not include a full lowercase commit SHA';
                } else {
                    actualSha = payload.commit;

                    if (actualSha === expectedSha) {
                        log(`Expected deployed SHA: ${expectedSha}`);
                        log(`Actual deployed SHA: ${actualSha}`);

                        return actualSha;
                    }

                    lastFailure = 'commit SHA mismatch';
                }
            }
        } catch (error) {
            lastFailure = error instanceof Error ? error.message : 'request failed';
        }

        if (attempt < attempts) {
            await wait(retryDelayMs);
        }
    }

    log(`Expected deployed SHA: ${expectedSha}`);
    log(`Actual deployed SHA: ${actualSha}`);

    throw new Error(`Deployment version verification failed after ${attempts} attempts (${lastFailure}).`);
}

if (process.argv[1] && import.meta.url === pathToFileURL(resolve(process.argv[1])).href) {
    const [url, expectedSha] = process.argv.slice(2);

    if (!url || !/^[a-f0-9]{40}$/.test(expectedSha ?? '')) {
        console.error('Usage: node scripts/verify-deployment.mjs <version-url> <full-lowercase-commit-sha>');
        process.exitCode = 2;
    } else {
        try {
            await verifyDeploymentSha({ url, expectedSha });
        } catch (error) {
            console.error(error instanceof Error ? error.message : 'Deployment version verification failed.');
            process.exitCode = 1;
        }
    }
}