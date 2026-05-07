import express from "express";
import axios from "axios";
import dotenv from "dotenv";
import { pathToFileURL } from "node:url";

dotenv.config();

export const REQUIRED_ENV_KEYS = [
    "VERIFY_TOKEN",
    "WHATSAPP_TOKEN",
    "PHONE_NUMBER_ID",
    "N8N_WEBHOOK_URL",
];

export function getRuntimeConfig(env = process.env) {
    return {
        port: Number(env.PORT || 3001),
        verifyToken: String(env.VERIFY_TOKEN ?? ""),
        whatsappToken: String(env.WHATSAPP_TOKEN ?? ""),
        phoneNumberId: String(env.PHONE_NUMBER_ID ?? ""),
        n8nWebhookUrl: String(env.N8N_WEBHOOK_URL ?? ""),
        metaGraphVersion: String(env.META_GRAPH_VERSION ?? "v18.0"),
    };
}

export function getMissingEnvKeys(env = process.env) {
    return REQUIRED_ENV_KEYS.filter((key) => String(env[key] ?? "").trim() === "");
}

function createLogger(logger = console) {
    return {
        info: logger.info?.bind(logger) ?? logger.log?.bind(logger) ?? (() => {}),
        error: logger.error?.bind(logger) ?? logger.log?.bind(logger) ?? (() => {}),
    };
}

function buildGraphMessagesUrl(config) {
    return `https://graph.facebook.com/${config.metaGraphVersion}/${config.phoneNumberId}/messages`;
}

export function createApp({ env = process.env, httpClient = axios, logger = console } = {}) {
    const app = express();
    const log = createLogger(logger);
    const config = getRuntimeConfig(env);

    app.use(express.json());

    app.get("/health", (req, res) => {
        const missingEnv = getMissingEnvKeys(env);

        return res.status(missingEnv.length === 0 ? 200 : 503).json({
            status: missingEnv.length === 0 ? "ok" : "degraded",
            service: "wa-service",
            missing_env: missingEnv,
            n8n_webhook_configured: config.n8nWebhookUrl !== "",
            meta_graph_version: config.metaGraphVersion,
        });
    });

    app.get("/webhook", (req, res) => {
        const mode = req.query["hub.mode"];
        const token = req.query["hub.verify_token"];
        const challenge = req.query["hub.challenge"];

        if (mode && token === config.verifyToken) {
            log.info("Webhook verified");
            return res.status(200).send(challenge);
        }

        return res.sendStatus(403);
    });

    app.post("/webhook", async (req, res) => {
        log.info("DATA MASUK:");
        log.info(JSON.stringify(req.body, null, 2));

        try {
            const message = req.body.entry?.[0]?.changes?.[0]?.value?.messages?.[0];

            if (message) {
                const sender = message.from;
                const text = message.text?.body;

                log.info("Pesan masuk: " + (text ?? "-"));

                let reply = "Pesan Anda sudah diterima.";

                if (text && config.n8nWebhookUrl) {
                    try {
                        const response = await httpClient.post(config.n8nWebhookUrl, {
                            sender,
                            message: text,
                        });

                        reply = response.data?.reply ?? reply;
                    } catch (n8nError) {
                        log.error("N8N ERROR:");
                        log.error(
                            JSON.stringify(
                                n8nError.response?.data ?? n8nError.message,
                                null,
                                2,
                            ),
                        );
                        reply = "Maaf, chatbot sedang bermasalah. Silakan coba lagi.";
                    }
                }

                log.info(
                    JSON.stringify(
                        {
                            to: sender,
                            reply,
                        },
                        null,
                        2,
                    ),
                );

                await httpClient.post(
                    buildGraphMessagesUrl(config),
                    {
                        messaging_product: "whatsapp",
                        to: sender,
                        text: { body: reply },
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${config.whatsappToken}`,
                            "Content-Type": "application/json",
                        },
                    },
                );
            }

            return res.sendStatus(200);
        } catch (error) {
            log.error("ERROR DETAIL:");
            log.error(JSON.stringify(error.response?.data ?? error.message, null, 2));
            return res.sendStatus(500);
        }
    });

    return app;
}

export function startServer(options = {}) {
    const env = options.env ?? process.env;
    const logger = options.logger ?? console;
    const log = createLogger(logger);
    const { port } = getRuntimeConfig(env);
    const app = createApp({ ...options, env, logger });

    return app.listen(port, () => {
        log.info(`Server jalan di port ${port}`);
    });
}

if (process.argv[1] && pathToFileURL(process.argv[1]).href === import.meta.url) {
    startServer();
}
