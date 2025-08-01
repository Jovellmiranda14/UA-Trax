// @ts-check
const { defineConfig, devices } = require("@playwright/test");
/**
 * Read environment variables from file.
 * https://github.com/motdotla/dotenv
 */
require("dotenv").config({
  path: process.env.test_env ? `./.env.${process.env.test_env}` : "./.env",
  // debug: true,
  quiet: true,
  // override: true,
});

/**
 * @see https://playwright.dev/docs/test-configuration
 */
module.exports = defineConfig({
  /* Set test timeout. Formula : mins * seconds * milliseconds */
  timeout: 10.5 * 60 * 1000,
  /* Test directory */
  testDir: "./tests",
  /* Run tests in files in parallel */
  fullyParallel: true,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: 1,
  /* Opt out of parallel tests on CI. */
  workers: 4,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: [["list"], ["html", { open: "on-failure" }]],
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    // Second * millisecond
    actionTimeout: 25 * 1000,
    // Used for HTTP 401 challenge
    httpCredentials: {
      username: process.env.CHALLENGE_USER || "",
      password: process.env.CHALLENGE_PASS || "",
    },

    /* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
    // trace: 'on-first-retry',

    /* Show browser when running if set to false */
    headless: false,
    screenshot: "only-on-failure",
    video: "retain-on-failure",
  },

  /* Configure projects for major browsers */
  projects: [
    {
      name: "chromium",
      use: {
        ...devices["Desktop Chrome"],
        viewport: { width: 1720, height: 1280 },
      },
    },

    // {
    //     name: 'firefox',
    //     use: {
    //         ...devices['Desktop Firefox'],
    //         viewport: { width: 1920, height: 1080 },
    //     },
    // },

    // {
    //     name: 'webkit',
    //     use: {
    //         ...devices['Desktop Safari'],
    //         viewport: { width: 1366, height: 768 },
    //     },
    // },

    /* Test against mobile viewports. */
    // {
    //   name: 'Mobile Chrome',
    //   use: { ...devices['Pixel 5'] },
    // },
    // {
    //   name: 'Mobile Safari',
    //   use: { ...devices['iPhone 12'] },
    // },

    /* Test against branded browsers. */
    // {
    //   name: 'Microsoft Edge',
    //   use: { ...devices['Desktop Edge'], channel: 'msedge' },
    // },
    // {
    //   name: 'Google Chrome',
    //   use: { ...devices['Desktop Chrome'], channel: 'chrome' },
    // },
  ],

  /* Run your local dev server before starting the tests */
  // webServer: {
  //   command: 'npm run start',
  //   url: 'http://127.0.0.1:3000',
  //   reuseExistingServer: !process.env.CI,
  // },
});