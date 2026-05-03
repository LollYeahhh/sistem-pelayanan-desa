import express from "express";
import axios from "axios";
import dotenv from "dotenv";

dotenv.config();

const app = express();
app.use(express.json());

const PORT = process.env.PORT;

app.get("/webhook", (req, res) => {
    const mode = req.query["hub.mode"];
    const token = req.query["hub.verify_token"];
    const challenge = req.query["hub.challenge"];

    if (mode && token === process.env.VERIFY_TOKEN) {
        console.log("Webhook verified");
        return res.status(200).send(challenge);
    }

    return res.sendStatus(403);
});

app.post("/webhook", async (req, res) => {
    console.log("DATA MASUK:");
    console.log(JSON.stringify(req.body, null, 2));

    try {
        const message = req.body.entry?.[0]?.changes?.[0]?.value?.messages?.[0];

        if (message) {
            const sender = message.from;
            const text = message.text?.body;

            console.log("Pesan masuk:", text);

            let reply = "Pesan Anda sudah diterima.";

            if (text && process.env.N8N_WEBHOOK_URL) {
                try {
                    const response = await axios.post(process.env.N8N_WEBHOOK_URL, {
                        sender,
                        message: text,
                    });

                    reply = response.data?.reply ?? reply;
                } catch (n8nError) {
                    console.error("N8N ERROR:");
                    console.error(
                        JSON.stringify(
                            n8nError.response?.data ?? n8nError.message,
                            null,
                            2,
                        ),
                    );
                    reply = "Maaf, chatbot sedang bermasalah. Silakan coba lagi.";
                }
            }

            console.log("KIRIM KE WA:", {
                to: sender,
                reply,
            });

            await axios.post(
                `https://graph.facebook.com/v18.0/${process.env.PHONE_NUMBER_ID}/messages`,
                {
                    messaging_product: "whatsapp",
                    to: sender,
                    text: { body: reply },
                },
                {
                    headers: {
                        Authorization: `Bearer ${process.env.WHATSAPP_TOKEN}`,
                        "Content-Type": "application/json",
                    },
                },
            );
        }

        return res.sendStatus(200);
    } catch (error) {
        console.error("ERROR DETAIL:");
        console.error(JSON.stringify(error.response?.data ?? error.message, null, 2));
        return res.sendStatus(500);
    }
});

app.listen(PORT, () => {
    console.log(`Server jalan di port ${PORT}`);
});
