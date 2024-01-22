import Chance from 'chance';
const chance = new Chance();

describe('Guest Access', () => {

    it('Redirect guest to login page', () => {
        cy.visit('/');
        cy.url().should('include', '/user/login');
    });

    it('Can navigate between login, register', () => {
        cy.visit('/user/login');
        
        cy.get('a[href="/user/register"]')
            .should('be.visible')
            .click();
        cy.url().should('include', '/user/register');
        
        cy.get('a[href="/user/login"]')
            .should('be.visible')
            .click();    
        cy.url().should('include', '/user/login');
        // TODO: add forget password page and test
    });

    it('Check login form', () => {
        cy.visit('/user/login');
        cy.contains('h1', 'Login');
        cy.get('form').within($form => {
            cy.contains('Email');
            cy.contains('Password');
            //cy.contains('a', 'Forget Password');
            cy.contains('button', 'Login');
            cy.contains('a', 'Register');
        });
    });

    it('Check register form', () => {
        cy.visit('/user/register');
        cy.contains('h1', 'Register');
        cy.get('form').within($form => {
            cy.contains('Name');
            cy.contains('Email');
            cy.contains('Password');
            cy.contains('button', 'Register');
            cy.contains('a', 'Cancel');
        });
    });

});