// const { test, expect, BrowserContext, Page } = require("@playwright/test");
// const {
//   searchResults_testData,
// } = require("../test_data/searchResults_testData");
// const { youtubePageObject } = require("../page_objects/youtubePageObject");
// const { Helpers } = require("../utils/common");
// const { youtubeSelectors } = require("../selectors/youtubeSelectors");
// const { googleSelectors } = require("../selectors/googleSelector");
// const { googleTestData } = require("../test_data/googleTestData");

// test.describe(
//   "YouTube Search Results Smoke Test",
//   {
//     //  tag: [`@Sample`, `@SearchResults`, `@smoke`],
//   },
//   async () => {
//     /** @type {Helpers} */
//     let helpers;

//     test.beforeEach(async ({ page }, testInfo) => {
//       helpers = new Helpers(page);
//     });

//     test(`Search Bar should be functional`, async ({ page }, testInfo) => {
//       await test.step(`Given User is in YouTube main page`, async () => {
//         await page.goto(process.env.GOOGLE_URL);
//       });

//       await test.step(`When User inputs a keyword(s) in the search bar`, async () => {
//         await helpers.assertElements([googleSelectors.google_sign], true);
//         await page.locator(googleSelectors.google_sign).click();
//         await helpers.delay(2000);
//         await page.fill(googleSelectors.input_Email, googleTestData.email);
//         await helpers.delay(2000);
//         await page.locator(googleSelectors.next_button).click();
//         await helpers.delay(2000);
//         await page.fill(
//           googleSelectors.input_Password,
//           googleTestData.password
//         );
//         await page.locator(googleSelectors.next_button).click();
//       });
//     });
//   }
// );
