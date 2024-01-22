// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This is will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })
import "cypress-localstorage-commands";

const apiUrl = Cypress.env('apiUrl');

Cypress.Commands.add('registerUserIfNeeded', (user = Cypress.env('testUser')) => {
    return cy.request({
        method: 'POST',
        url: `${apiUrl}/user/create`,
        body: user
    });
});

Cypress.Commands.add('registerAdminIfNeeded', (user = Cypress.env('testAdmin')) => {
    const { email, password } = user;
    // ignore success or not
    const response = cy.request({
        method: 'POST',
        url: `${apiUrl}/user/create`,
        body: user
    });
    cy.getLoginData(email, password).then(res => {
        if (res.token && res.user) {
            cy.request({
                method: 'PUT',
                url: `${apiUrl}/user/assign-role/${res.user.id}`,
                body: { role: user.role },
                auth: { bearer: res.token }
            });
        }
    });
    return response;
});

Cypress.Commands.add('login', (user = Cypress.env('testUser')) => {
    const { email, password } = user;
    return cy.request({
        method: 'POST',
        url: `/auth/login`,
        body: { email, password }
    });
});

Cypress.Commands.add('loginAdmin', (user = Cypress.env('testAdmin')) => {
    const { email, password } = user;
    return cy.request({
        method: 'POST',
        url: `/auth/login`,
        body: { email, password }
    });
});

Cypress.Commands.add('cleanUpDb', (user = Cypress.env('testUser')) => {
    const { email, password } = user;
    cy.getLoginData(email, password).then(res => {
        if (res.token) {
            return cy.request({
                method: 'POST',
                url: `${apiUrl}/cypress/clean-up`,
                auth: { bearer: res.token }
            });
        }
    });
});

Cypress.Commands.add('getLoginData', (email, password) => {
    return cy.request({
        method: 'POST',
        url: `${apiUrl}/user/auth`,
        body: { email, password }
    })
    .then(res => {
        return res.body.data;
    });
});