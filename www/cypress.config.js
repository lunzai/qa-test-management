const { defineConfig } = require('cypress')

module.exports = defineConfig({
  projectId: "vwa3za",
  video: true,
  env: {
    apiUrl: 'http://api.qa.test',
    domain: 'example.Com',
    existingUserEmail: 'user@example.com',
    testUser: {
      email: 'tech+cypress@example.com',
      password: 'cypresstester',
      display_name: 'Cypress Test',
    },
    testAdmin: {
      email: 'tech+cypressadmin@example.com',
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
