module.exports = {
    apps: [
        {
            name: "qaapp",
            script: "npm",
            args: "start",
            cwd: "/var/www/qa/app/www",
            watch: false,
            env: {
                NODE_ENV: "production"
            }
        }
    ]
}
