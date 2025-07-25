export const loginSelectors = {
    label_email: '//span[contains(.,"Email address*")]',
    email_Input: '//input[@type="email"]',
    label_password: '//span[contains(.,"Password*")]',
    input_password: '//input[@type="password"]',
    label_RememberMe: '//span[normalize-space(text())="Remember me"]',
    checkbox_RememberMe: '//input[@type="checkbox"]',
    show_eyeicon: '//button[@title="Show password"]',
    hidden_eyeicon: '//button[@title="Hide password"]',
    label_ForgotPassword: '//span[normalize-space(text())="Forgot password?"]',
    label_SignIn: '//h1[normalize-space(text())="Sign in"]',
    loginButton: '//button[@type="submit"]',
    Img_logo: `//img[@alt='UA-Trax logo']`,
}