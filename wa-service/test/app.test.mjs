import assert from "node:assert/strict";
import { once } from "node:events";
import test from "node:test";

import { createApp, getMissingEnvKeys } from "../index.js";

const fullEnv = {
    VERIFY_TOKEN: "verify-token",
    WHATSAPP_TOKEN: "wa-token",
    PHONE_NUMBER_ID: "123456789",
    N8N_WEBHOOK_URL: "http://127.0.0.1:5678/webhook/wa-incoming",
    META_GRAPH_VERSION: "v18.0",
    PORT: "0",
};

function createSilentLogger() {
    return {
        info() {},
        error() {},
    };
}

async function withServer(app, callback) {
    const server = app.listen(0);
    await once(server, "listening");

    try {
        const address = server.address();
        return await callback(`http://127.0.0.1:${address.port}`);
    } finally {
        await new Promise((resolve, reject) => {
            server.close((error) => {
                if (error) {
                    reject(error);
                    return;
                }

                resolve();
            });
        });
    }
}

test("getMissingEnvKeys reports missing wa-service dependencies", () => {
    assert.deepEqual(getMissingEnvKeys({}), [
        "VERIFY_TOKEN",
        "WHATSAPP_TOKEN",
        "PHONE_NUMBER_ID",
        "N8N_WEBHOOK_URL",
    ]);
});

test("health endpoint returns ok when runtime env is complete", async () => {
    const app = createApp({
        env: fullEnv,
        logger: createSilentLogger(),
    });

    await withServer(app, async (baseUrl) => {
        const response = await fetch(`${baseUrl}/health`);
        const payload = await response.json();

        assert.equal(response.status, 200);
        assert.equal(payload.status, "ok");
        assert.deepEqual(payload.missing_env, []);
    });
});

test("webhook verification echoes the challenge for the configured token", async () => {
    const app = createApp({
        env: fullEnv,
        logger: createSilentLogger(),
    });

    await withServer(app, async (baseUrl) => {
        const response = await fetch(
            `${baseUrl}/webhook?hub.mode=subscribe&hub.verify_token=${fullEnv.VERIFY_TOKEN}&hub.challenge=12345`,
        );
        const body = await response.text();

        assert.equal(response.status, 200);
        assert.equal(body, "12345");
    });
});

test("incoming messages are forwarded to n8n and replied via Meta Graph", async () => {
    const calls = [];
    const httpClient = {
        async post(url, payload, options = {}) {
            calls.push({ url, payload, options });

            if (url === fullEnv.N8N_WEBHOOK_URL) {
                return {
                    data: {
                        reply: "Status surat Anda: selesai.",
                    },
                };
            }

            return { data: { success: true } };
        },
    };

    const app = createApp({
        env: fullEnv,
        httpClient,
        logger: createSilentLogger(),
    });

    await withServer(app, async (baseUrl) => {
        const response = await fetch(`${baseUrl}/webhook`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                entry: [
                    {
                        changes: [
                            {
                                value: {
                                    messages: [
                                        {
                                            from: "6281234567890",
                                            text: {
                                                body: "cek status surat REG-ABC12345",
                                            },
                                        },
                                    ],
                                },
                            },
                        ],
                    },
                ],
            }),
        });

        assert.equal(response.status, 200);
        assert.equal(calls.length, 2);
        assert.equal(calls[0].url, fullEnv.N8N_WEBHOOK_URL);
        assert.equal(
            calls[1].url,
            "https://graph.facebook.com/v18.0/123456789/messages",
        );
        assert.equal(calls[1].payload.to, "6281234567890");
        assert.equal(
            calls[1].payload.text.body,
            "Status surat Anda: selesai.",
        );
        assert.equal(
            calls[1].options.headers.Authorization,
            "Bearer wa-token",
        );
    });
});
