module.exports = {
    apps: [
        {
            name: "wa-service",
            script: "./index.js",
            cwd: __dirname,
            interpreter: "node",
            exec_mode: "fork",
            instances: 1,
            autorestart: true,
            watch: false,
            max_memory_restart: "256M",
            env: {
                NODE_ENV: "production",
                PORT: 3001,
            },
        },
    ],
};
