export const youtubeSelectors = {
    field_SearchBar: `//input[@class="ytSearchboxComponentInput yt-searchbox-input title"]`,
    button_Search: `//button[contains(@class, "ytSearchboxComponentSearchButton")]`,
    comment:`//span[normalize-space(text())='He just pulls up.']`
    // This is a dynamic xpath
    // text_VideoTitle: (title) =>
    //     `//yt-formatted-string[text()="${title}"]`,

    // container_OfVideo: (title) =>
    //     `//yt-formatted-string[text()="${title}"]/ancestor::div[@id="dismissible"]`,
    
}