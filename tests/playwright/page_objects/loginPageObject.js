class LoginPage {
    constructor(page) {
        this.page = page;
        this.emailInput = page.locator('//input[@type="email"]');
        this.passwordInput = page.locator('//input[@type="password"]');
        this.loginButton = page.locator('//button[@type="submit"]');
    }
    async loginAsRole(role) {
        let email, password;
        switch (role) {
            case 'facilitysuperadmin':
                email = process.env.FACILITY_SUPERADMIN_EMAIL;
                password = process.env.FACILITY_SUPERADMIN_PASSWORD;
                break;
            case 'equipmentsuperadmin':
                email = process.env.EQUIPMENT_SUPERADMIN_EMAIL;
                password = process.env.EQUIPMENT_SUPERADMIN_PASSWORD;
                break;
            case 'equipment_admin_labcustodian':
                email = process.env.EQUIPMENT_ADMIN_LABCUSTODIAN_EMAIL;
                password = process.env.EQUIPMENT_ADMIN_LABCUSTODIAN_PASSWORD;
                break;
            case 'equipment_admin_omiss':
                email = process.env.EQUIPMENT_ADMIN_OMISS_EMAIL;
                password = process.env.EQUIPMENT_ADMIN_OMISS_PASSWORD;
                break;
            case 'facility_admin':
                email = process.env.FACILITY_ADMIN_EMAIL;
                password = process.env.FACILITY_ADMIN_PASSWORD;
                break;
            case 'user':
                email = process.env.USER_EMAIL;
                password = process.env.USER_PASSWORD;
                break;
            default:
                throw new Error(`Unsupported role: ${role}`);
        }
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.loginButton.click();
    }
}
module.exports = { LoginPage };
