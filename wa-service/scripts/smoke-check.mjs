import dotenv from "dotenv";

import { getMissingEnvKeys } from "../index.js";

dotenv.config();

const args = new Map();

for (let index = 2; index < process.argv.length; index += 1) {
    const key = process.argv[index];
    const value = process.argv[index + 1];

    if (key.startsWith("--")) {
        args.set(key, value && !value.startsWith("--") ? value : "");
    }
}

const baseUrl = args.get("--url") || process.env.WA_SERVICE_BASE_URL || "";
const challenge = "smoke-check";
const missingEnv = getMissingEnvKeys(process.env);

if (missingEnv.length > 0) {
    console.error("Missing required wa-service environment variables:");
    console.error(missingEnv.join(", "));
    process.exit(1);
}

console.log("Environment check passed.");

if (!baseUrl) {
    console.log("Skipping HTTP smoke check because --url or WA_SERVICE_BASE_URL is not set.");
    process.exit(0);
}

const normalizedBaseUrl = baseUrl.replace(/\/+$/, "");

const healthResponse = await fetch(`${normalizedBaseUrl}/health`);

if (!healthResponse.ok) {
    console.error(`Health check failed with status ${healthResponse.status}.`);
    process.exit(1);
}

const healthPayload = await healthResponse.json();

if (healthPayload.status !== "ok") {
    console.error("Health check returned a degraded payload:");
    console.error(JSON.stringify(healthPayload, null, 2));
    process.exit(1);
}

const verifyResponse = await fetch(
    `${normalizedBaseUrl}/webhook?hub.mode=subscribe&hub.verify_token=${encodeURIComponent(process.env.VERIFY_TOKEN)}&hub.challenge=${challenge}`,
);

const verifyBody = await verifyResponse.text();

if (verifyResponse.status !== 200 || verifyBody !== challenge) {
    console.error("Webhook verification smoke check failed.");
    console.error(`Status: ${verifyResponse.status}`);
    console.error(`Body: ${verifyBody}`);
    process.exit(1);
}

console.log("HTTP smoke check passed.");
