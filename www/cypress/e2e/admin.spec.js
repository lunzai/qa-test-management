import Chance from 'chance';
const chance = new Chance();

describe('Admin Access', () => {

    before(() => {
        console.log(cy.registerAdminIfNeeded());
    });

    after(() => {
        
    });

    it('Login as admin', () => {
        //
    });

    it('See admin menu and sub-menu', () => {
        //
    });

});