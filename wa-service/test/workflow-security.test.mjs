import assert from "node:assert/strict";
import { readFile, readdir } from "node:fs/promises";
import test from "node:test";

const workflowDirectory = new URL("../n8n/", import.meta.url);
const sensitiveHeader = /^(?:authorization|proxy-authorization|x-api-key|api-key|apikey|x-auth-token)$/i;

function checkHeaders(value, location) {
    if (!value || typeof value !== "object") return;

    if (typeof value.name === "string" && sensitiveHeader.test(value.name.trim())) {
        assert.fail(`${location}: authentication headers must use n8n Credentials`);
    }

    for (const [key, child] of Object.entries(value)) {
        // Also reject headers supplied as a JSON object/string instead of name/value pairs.
        assert.ok(!sensitiveHeader.test(key.trim()), `${location}: use n8n Credentials`);
        if (key === "jsonHeaders" && typeof child === "string" && child.trim()) {
            let headers;
            try {
                headers = JSON.parse(child);
            } catch {
                assert.fail(`${location}: JSON headers must be inspectable; use Credentials for authentication`);
            }
            checkHeaders(headers, location);
        } else {
            checkHeaders(child, location);
        }
    }
}

test("shared workflows keep authentication headers in n8n Credentials", async () => {
    const files = (await readdir(workflowDirectory)).filter((file) => file.endsWith(".json"));
    assert.ok(files.length > 0, "workflow fixtures must exist");

    for (const file of files) {
        const workflow = JSON.parse(await readFile(new URL(file, workflowDirectory), "utf8"));
        for (const node of workflow.nodes) {
            checkHeaders(node.parameters, `${file}: ${node.name}`);
        }
    }
});

test("WAHA workflow requires a Header Auth credential after import", async () => {
    const workflow = JSON.parse(await readFile(new URL("workflow-n8n.json", workflowDirectory), "utf8"));
    const node = workflow.nodes.find((item) => item.name === "HTTP Request WAHA1");
    assert.ok(node, "WAHA sending node must exist");
    assert.equal(node.parameters.authentication, "genericCredentialType");
    assert.equal(node.parameters.genericAuthType, "httpHeaderAuth");
});

test("header checks reject credentials without printing their values", () => {
    for (const parameters of [
        { headerParameters: { parameters: [{ name: "x-API-key", value: "test-only" }] } },
        { jsonHeaders: JSON.stringify({ Authorization: "test-only" }) },
        { jsonHeaders: "={{ $json.headers }}" },
    ]) {
        assert.throws(() => checkHeaders(parameters, "fixture"), (error) => {
            assert.ok(!error.message.includes("test-only"));
            return true;
        });
    }
    assert.doesNotThrow(() => checkHeaders({ headerParameters: { parameters: [{ name: "Content-Type", value: "application/json" }] } }, "fixture"));
});
