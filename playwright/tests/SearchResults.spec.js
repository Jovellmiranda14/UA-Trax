const { test, expect, BrowserContext, Page } = require("@playwright/test");
const {
  searchResults_testData,
} = require("../test_data/searchResults_testData");
const { youtubePageObject } = require("../page_objects/youtubePageObject");
const { Helpers } = require("../utils/common");
const { youtubeSelectors } = require("../selectors/youtubeSelectors");

test.describe(
  "YouTube Search Results Smoke Test",
  {
    tag: [`@Sample`, `@SearchResults`, `@smoke`],
  },
  async () => {
    /** @type {Helpers} */
    let helpers;

    test.beforeEach(async ({ page }, testInfo) => {
      helpers = new Helpers(page);
    });

    test(`Search Bar should be functional`, async ({ page }, testInfo) => {
      await test.step(`Given User is in YouTube main page`, async () => {
        await page.goto(process.env.SAMPLE_URL);
      });

      await test.step(`When User inputs a keyword(s) in the search bar`, async () => {
        await page
          .locator(youtubeSelectors.field_SearchBar)
          .fill(searchResults_testData.keyword);
      });

      await test.step(`And User clicks the search button`, async () => {
        await page.locator(youtubeSelectors.button_Search).click();
      });

      await test.step(`Then User should be navigated to the search results page`, async () => {
        await page.waitForURL(new RegExp(`/results`));
      });

      await test.step(`And User should see videos related to the search result`, async () => {
        await helpers.assertElements(
          [
            youtubeSelectors.text_VideoTitle(searchResults_testData.vidTitle),
            youtubeSelectors.container_OfVideo(searchResults_testData.vidTitle),
          ],
          true
        );
        await helpers.screenshotAndAttach(testInfo, `1. Test`);
      });
      await test.step(`And User should see videos related to the search result`, async () => {
        await page.click(
          youtubeSelectors.text_VideoTitle(searchResults_testData.vidTitle)
        );
        await helpers.assertElements([youtubeSelectors.comment], true);
        await helpers.delay(5000);
        await helpers.screenshotAndAttach(testInfo, `1. Test`);
      });
    });
  }
);
