module.exports = {
    apps: [
        {
            name: "wc-qaapp",
            script: "npm",
            args: "start",
            cwd: "/var/www/segi/tcs3223/app/www",
            watch: false,
            env: {
                NODE_ENV: "production"
            }
        }
    ]
}