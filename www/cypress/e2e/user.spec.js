import Chance from 'chance';
import { beforeUpdate } from 'svelte';
const chance = new Chance();
const loginUrl = '/user/login';
const registerUrl = '/user/register';
const profileUrl = '/profile';
const wcDomain = Cypress.env('domain');
const notWcDomain = `not.${wcDomain}`;
const existingUserEmail = Cypress.env('existingUserEmail');
const notWcEmail = chance.email({ notWcDomain });
const unixTimestamp = chance.hammertime();
let testUser = Cypress.env('testUser');
let registerUser = {
    display_name: chance.name(),
    job_role: chance.profession(),
    email: `${unixTimestamp}+cypress@${wcDomain}`,
    password: Cypress.env('testPassword')
};

// const userEmail = Cypress.env('user').email;
// const userPassword = Cypress.env('user').password;


describe('User Module', () => {

    before(() => {
        cy.cleanUpDb();
        cy.registerUserIfNeeded();
    });

    after(() => {
        cy.cleanUpDb();
        cy.cleanUpDb(registerUser);
    });

    it('Login validation works', () => {
        cy.visit(loginUrl);
        cy.wait(500);
        cy.get('button[type=submit]').as('submit');
        cy.get('[data-cy=input-email]').as('email');
        cy.get('[data-cy=input-password]').as('password');
        
        // submit empty form, should stay in login
        cy.get('@submit')
            .click();
        cy.url()
            .should('contain', loginUrl);
        
        // email - invalid email
        cy.get('@email')
            .type('definitely.invalid.email');
        cy.get('[data-cy=input-email-error]').as('emailError')
            .should('be.visible');

        // email - ok
        cy.get('@email')
            .clear()
            .type(existingUserEmail);
        cy.get('@emailError')
            .should('not.exist');

        // password - less than 8 char
        cy.get('@password')
            .type('1234567');
        cy.get('[data-cy=input-password-error]').as('passwordError')
            .should('be.visible');

        // password - ok
        cy.get('@password')
            .clear()
            .type('12345678');
        cy.get('@passwordError')
            .should('not.exist');

        // login with invalid credentials
        cy.get('@submit')
            .click();
        cy.get('[data-cy=alert-danger]')
            .should('be.visible');
        cy.location('pathname')
            .should('eq', loginUrl);
    });

    it('Registration validation works', () => {
        cy.visit(registerUrl);
        cy.wait(500);

        cy.get('button[type="submit"]').as('submit');
        cy.get('[data-cy=input-display_name]').as('name');
        cy.get('[data-cy=input-email]').as('email');
        cy.get('[data-cy=input-password]').as('password');
        
        // submit empty form, should stay in login
        cy.get('@submit')
            .click();
        cy.location('pathname')
            .should('eq', registerUrl);
        
        // name - less than 2 char
        cy.get('@name')
            .type('A');
        cy.get('[data-cy=input-display_name-error]').as('nameError')
            .should('be.visible');

        // name - ok
        cy.get('@name')
            .clear()
            .type('Whatever');
        cy.get('@nameError')
            .should('not.exist');

        // password - less than 8 char
        cy.get('@password')
            .type('1234567');
        cy.get('[data-cy=input-password-error]').as('passwordError')
            .should('be.visible');

        // password - ok
        cy.get('@password')
            .clear()
            .type('12345678');
        cy.get('@passwordError')
            .should('not.exist');

        // email - invalid email
        cy.get('@email')
            .type('definitely.invalid.email');
        cy.get('[data-cy=input-email-error]').as('emailError')
            .should('be.visible');

        // email - not allowed email domain
        cy.get('@email')
            .clear()
            .type(notWcEmail);
        cy.get('[data-cy=input-email-error]').as('emailError')
            .should('be.visible');

        // email - existed email
        cy.get('@email')
            .clear()
            .type(existingUserEmail);
        cy.get('@emailError')
            .should('not.exist');
        cy.get('@submit')
            .click();
        cy.get('@emailError')
            .should('be.visible');

        // still in register page
        cy.location('pathname')
            .should('eq', registerUrl);
    });

    it('Guest can register', () => {
        cy.visit(registerUrl);
        cy.wait(500);

        // register
        cy.get('[data-cy=input-display_name]')
            .type(registerUser.display_name);
        cy.get('[data-cy=input-email]')
            .type(registerUser.email);
        cy.get('[data-cy=input-password]')
            .type(registerUser.password);
        cy.get('button[type="submit"]')
            .click();
        cy.location('pathname')
            .should('eq', '/');
        cy.get('[data-cy=alert-success]')
            .should('be.visible');
        cy.get('[data-cy=user-menu-trigger]')
            .should('contain', registerUser.display_name);
        
        // check registration
        cy.visit(profileUrl);
        cy.get('[data-cy=card]').within(() => {
            cy.contains(registerUser.display_name);
            cy.contains(registerUser.email);
        });
    });

    it('Guest can login', () => {
        cy.visit(loginUrl);
        cy.wait(500);

        cy.get('[data-cy=input-email]')
            .type(testUser.email);
        cy.get('[data-cy=input-password]')
            .type(testUser.password);
        cy.get('button[type=submit]')
            .click();
        cy.location('pathname')
            .should('eq', '/');
        cy.get('[data-cy=alert-success]')
            .should('be.visible')
            .should('contain', testUser.display_name);
        cy.get('[data-cy=user-menu-trigger]')
            .should('contain', testUser.display_name);
    });

    it('User can update profile', () => {
        cy.login();
        cy.visit('/profile');
        cy.wait(500);

        // check profile page
        cy.get('[data-cy=card]').within(() => {
            cy.contains(testUser.display_name);
            cy.contains(testUser.email);
        });

        // update profile
        cy.contains('Update')
            .click();
        
        cy.get('[data-cy=input-display_name]')
            .type('1');

        cy.get('button[type="submit"]')
            .should('be.visible')
            .click();
        
        // check profile page after update
        cy.get('[data-cy=card]').within(() => {
            cy.contains(testUser.display_name + '1');
            cy.contains(testUser.email);
        });
        cy.contains('Update');

    });

    it('User can logout', () => {
        cy.login();
        cy.visit('/');
        cy.wait(500);

        cy.get('[data-cy=user-menu-trigger]')
            .click({ force: true });
        cy.get('[href="/user/logout"]')
            .should('be.visible')
            .click({ force: true });
        
        cy.location('pathname')
            .should('eq', '/user/login');
        
    });

});