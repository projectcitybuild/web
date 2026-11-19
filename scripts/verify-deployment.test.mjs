import assert from 'node:assert/strict';
import test from 'node:test';
import { verifyDeploymentSha } from './verify-deployment.mjs';

const expectedSha = '0123456789abcdef0123456789abcdef01234567';

test('accepts a matching SHA after retrying a stale response', async () => {
    const commits = ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', expectedSha];
    const output = [];
    let requestCount = 0;

    const actualSha = await verifyDeploymentSha({
        url: 'https://example.test/version',
        expectedSha,
        attempts: 3,
        fetchImpl: async () => ({
            ok: true,
            json: async () => ({ commit: commits[requestCount++] }),
        }),
        wait: async () => {},
        log: line => output.push(line),
    });

    assert.equal(actualSha, expectedSha);
    assert.equal(requestCount, 2);
    assert.deepEqual(output, [
        `Expected deployed SHA: ${expectedSha}`,
        `Actual deployed SHA: ${expectedSha}`,
    ]);
});

test('fails after repeated mismatched SHA responses', async () => {
    const actualSha = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    const output = [];
    let requestCount = 0;

    await assert.rejects(
        verifyDeploymentSha({
            url: 'https://example.test/version',
            expectedSha,
            attempts: 2,
            fetchImpl: async () => {
                requestCount += 1;

                return { ok: true, json: async () => ({ commit: actualSha }) };
            },
            wait: async () => {},
            log: line => output.push(line),
        }),
        /commit SHA mismatch/,
    );

    assert.equal(requestCount, 2);
    assert.deepEqual(output, [
        `Expected deployed SHA: ${expectedSha}`,
        `Actual deployed SHA: ${actualSha}`,
    ]);
});

test('fails when the version endpoint remains unavailable', async () => {
    const output = [];
    let requestCount = 0;

    await assert.rejects(
        verifyDeploymentSha({
            url: 'https://example.test/version',
            expectedSha,
            attempts: 3,
            fetchImpl: async () => {
                requestCount += 1;

                return { ok: false, status: 503 };
            },
            wait: async () => {},
            log: line => output.push(line),
        }),
        /HTTP 503/,
    );

    assert.equal(requestCount, 3);
    assert.deepEqual(output, [
        `Expected deployed SHA: ${expectedSha}`,
        'Actual deployed SHA: unavailable',
    ]);
});