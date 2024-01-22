import Chance from 'chance';
const chance = new Chance();

describe('Admin - User', () => {

    before(() => {
        console.log(cy.registerAdminIfNeeded());
    });

    after(() => {
        
    });

    it('Can view user listing page', () => {
        //
    });

    it('Can search user listing page', () => {
        //
    });

    it('Can view user profile page', () => {
        //
    });

    it('Can update user profile', () => {
        //
    });

});