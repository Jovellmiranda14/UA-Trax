const { Page, expect } = require("@playwright/test");
const { Helpers } = require("../utils/common");

export class youtubePageObject {
    constructor(page) {
        /** @type {Page} */
        this.page = page;
        this.helpers = new Helpers(page);
    }

    /**
     * Inputs a keyword in the searchbar and clicks the search button
     * @param {string} keyword - what keyword to search for
     */
    async searchFor(keyword) {
        await this.page.locator(youtubeSelectors.field_SearchBar).fill(searchResults_testData.keyword);
        await this.page.locator(youtubeSelectors.button_Search).click();
    }
}