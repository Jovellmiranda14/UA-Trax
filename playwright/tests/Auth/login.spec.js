const { test, expect } = require("@playwright/test");
const { Helpers } = require("../../utils/common");
const { loginSelectors } = require("../../selectors/LoginSelector");
const { LoginPage } = require("../../page_objects/loginPageObject");

const all_role = [
    'facilitysuperadmin',
    'equipmentsuperadmin',
    'equipment_admin_labcustodian',
    'equipment_admin_omiss',
    'facility_admin',
    'user',
];

test.describe(
    "Login Page UI Validation for All Roles",
    { tags: ['@smoke'] },
    () => {
        all_role.forEach((role) => {
            test(`Login UI and role-based login for '${role}'`, async ({ page }, testInfo) => {
                const helpers = new Helpers(page);
                const loginPage = new LoginPage(page);

                await test.step("Given the user is on the login page", async () => {
                    await page.goto(process.env.BASE_URL);
                    await helpers.delay(1000);
                    await helpers.assertElements([loginSelectors.Img_logo], true);
                });

                await test.step("When the login form is displayed", async () => {
                    await helpers.assertElements([
                        loginSelectors.label_email,
                        loginSelectors.email_Input,
                        loginSelectors.label_password,
                        loginSelectors.input_password,
                        loginSelectors.label_SignIn,
                        loginSelectors.label_RememberMe,
                        loginSelectors.checkbox_RememberMe,
                        loginSelectors.label_ForgotPassword,
                        loginSelectors.show_eyeicon,
                    ], true);
                });

                await test.step("And the user toggles the password visibility", async () => {
                    await page.click(loginSelectors.show_eyeicon);
                    await helpers.assertElements([loginSelectors.hidden_eyeicon], true);
                    await page.click(loginSelectors.hidden_eyeicon);
                });

                await test.step(`And the user logs in using the role '${role}'`, async () => {
                    await loginPage.loginAsRole(role);
                    await helpers.delay(2000);
                    await helpers.screenshotAndAttach(testInfo, `After Login - ${role}`);
                });
            });
        });
    }
);
