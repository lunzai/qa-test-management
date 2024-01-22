const { defineConfig } = require('cypress')

module.exports = defineConfig({
  projectId: "vwa3za",
  video: true,
  env: {
    apiUrl: 'http://api.qa.test',
    domain: 'whitecoat.global',
    existingUserEmail: 'hl@whitecoat.global',
    testUser: {
      email: 'tech+cypress@whitecoat.global',
      password: 'cypresstester',
      display_name: 'Cypress Test',
    },
    testAdmin: {
      email: 'tech+cypressadmin@whitecoat.global',
      password: 'cypressadmin',
      display_name: 'Cypress Admin',
      role: 'Admin',
    },
    testPassword: 'test1234',
  },
  e2e: {
    // We've imported your old cypress plugins here.
    // You may want to clean this up later by importing these.
    setupNodeEvents(on, config) {
      return require('./cypress/plugins/index.js')(on, config)
    },
    baseUrl: 'http://qa.test:3000',
    specPattern: 'cypress/e2e/**/*.{js,jsx,ts,tsx}',
  },
})
