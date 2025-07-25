const { expect, Page } = require('@playwright/test');
const fs = require('fs');
const dayjs = require('dayjs');
const customParseFormat = require('dayjs/plugin/customParseFormat');
dayjs.extend(customParseFormat);
const path = require('path');

class Helpers {
    constructor(page) {
        /** @type {Page} */
        this.page = page;
    }

    /** Helper method to assert a list of elements. It does the following:
     *  1. Loops through a list of selectors
     *  2. Waits to see if element is visible
     *  3. Asserts that the element is visible
     *  4. Optionally, highlights the element.
     *  5. Scrolls into view if not in view
     * @param {string[]} selectorList - selector values; must be an array/list of string(s). If asserting a single element, enclose in []
     * @param {boolean} [highlightElement] - (Optional) true/false whether to highlight the elements shown. Highlighted elements have an orange rectangle drawn on them. Default false.
     */
    async assertElements(selectorList, highlightElement = false) {
        for (const selector of selectorList) {
            const element = this.page.locator(selector).first();
            try {
                await element.waitFor();
            } catch (e) {
                console.log(`Error: The element ${element} was not found.`);
            }
            await expect(element).toBeVisible();
            await this.highlight(element, highlightElement || false);
            await element.scrollIntoViewIfNeeded();
        }
    }

    /** Helper method to assert a list of buttons if they are enabled or disabled
     * @param {string[]} selectorList - selector values; must be an array/list of string(s). If asserting a single element, enclose in []
     * @param {string} mode - whether to assert if the buttons are enabled or disabled; default "enabled"
     */
    async assertButtons(selectorList, mode = "enabled") {
        for (const selector of selectorList) {
            const element = this.page.locator(selector).first();
            if (mode.toLowerCase() == "enabled") {
                await expect(element).toBeEnabled();
            } else if (mode.toLowerCase() == "disabled") {
                await expect(element).toBeDisabled();
            }
        }
    }

    /** A modified version of assertElements() for matching and highlighting selectors that can match multiple elements. Can be used for records or elements where single data testid matches multiple elements.
     * @param {string} selector - selector values that can match multiple elements; note that it should 
     * @param {boolean} [highlightElement] - (Optional) true/false whether to highlight the elements shown. Highlighted elements have an orange rectangle drawn on them. Default false.
     */
    async assertAllMatchingElements(selector, highlightElement = false) {
        const allMatchedElements = await this.page.locator(selector).all();
        for (const element of allMatchedElements) {
            try {
                await element.waitFor();
            } catch (e) {
                console.log(`Error: The element ${element} was not found.`);
            }
            await expect(element).toBeVisible();
            await this.highlight(element, highlightElement || false);
        }
    }

    /** A modified version of assertButtons() for matching and buttons that can match multiple elements. Can be used for records or elements where single data testid matches multiple elements.
     * @param {string} selector - selector values that can match multiple elements; note that it should 
     * @param {string} mode - whether to assert if the buttons are enabled or disabled; default "enabled"
     */
    async assertAllMatchingButtons(selector, mode) {
        const allMatchedElements = await this.page.locator(selector).all();
        for (const element of allMatchedElements) {
            if (mode.toLowerCase() == "enabled") {
                await expect(element).toBeEnabled();
            } else if (mode.toLowerCase() == "disabled") {
                await expect(element).toBeDisabled();
            }
        }
    }

    /** A hard delay method
     * @param {number} time - how long to wait; uses milliseconds (ms). To wait 1 second, use 1000
     */
    delay(time) {
        return new Promise((resolve) => setTimeout(resolve, time));
    }

    /** Used by assertElements primarily; not recommended to call this method directly. 
     * It reads an element's style, then adds an outline so that it is very visible in screenshots. 
     * @param locator - a Locator object (page.locator()) of the element.
     * @param {boolean} flag - whether to highlight the element or not
     */
    async highlight(locator, flag) {
        try {
            if (flag == true) {
                const additionalStyles = 'outline: orange solid 2px !important; outline-offset: -1px !important;';

                await locator.evaluate((element, additionalStyles) => {
                    // Get the existing styles
                    const existingStyles = element.getAttribute('style') || '';

                    // Combine existing styles with additional styles
                    const combinedStyles = `${existingStyles}; ${additionalStyles}`;

                    // Set the combined styles back to the element
                    element.setAttribute('style', combinedStyles.trim());
                }, additionalStyles);
            }
        } catch (e) {
            console.log(`Error: The element to highlight "${locator}" was not found.`);
            throw e;
        }

    }

    /** Helper method to screenshot the browser for test evidence. It also attaches the screenshots to the test's metadata for test reports.
     *  
     * Filenaming format is [Test title]_[image number]; the image number is a simple counter that starts from 1
     * @param {import('playwright/test.js').TestInfo} testInfo - needed by the method so that it can reference the test to attach the screenshot to
     * @param {string} screenshotName - provide the title of the screenshot; defaults to "Screenshot"
    */
    async screenshotAndAttach(testInfo, screenshotName = "Screenshot") {
        const testTitle = testInfo.title.trim().replace(/[\/\\:*?"<>|]/g, '_');
        screenshotName = screenshotName.replace(/[\/\\:*?"<>|]/g, '_');
        let counter = 1;
        let filename = `./test-results/screenshots/${testTitle}/${screenshotName}_${counter}.png`;

        // Check if the file already exists
        while (fs.existsSync(filename)) {
            // If the file exists, increment the counter and modify the filename
            counter++;
            filename = `./test-results/screenshots/${testTitle}/${screenshotName}_${counter}.png`;
        }

        // Screenshot after the step
        const img = await this.page.screenshot({ path: filename, type: "png", animations: "disabled" });
        // Attach screenshot to test scenario
        await testInfo.attach(`${screenshotName}_${counter}`, { body: img, contentType: 'image/png' });
    }

    /**
    /**
     * Generates the current date and time in YYYYMMDDHHmmss format.
     * Useful for creating unique strings based on the current datetime.
     */
    async generateCurrentDatetime() {
        return dayjs().format('YYYYMMDDHHmmss');
    }

}

module.exports = { Helpers }