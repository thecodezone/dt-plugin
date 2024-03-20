import "../css/plugin.css";
import "plyr/dist/plyr.css";

import '@spectrum-web-components/theme/sp-theme.js';
import '@spectrum-web-components/theme/src/themes.js';
import '@spectrum-web-components/banner/sp-banner.js';
import '@spectrum-web-components/action-bar/sp-action-bar.js';
import '@spectrum-web-components/progress-circle/sp-progress-circle.js';
import '@spectrum-web-components/action-button/sp-action-button.js';
import '@spectrum-web-components/action-group/sp-action-group.js';
import '@spectrum-web-components/action-menu/sp-action-menu.js';

import "./web-components/content.js"
import "./web-components/verse.js"
import "./web-components/audio.js"
import "./web-components/video.js"
import "./web-components/reader.js"
import "./web-components/bible.js"
import "./web-components/player.js"

import {loaded} from "./helpers.js";

loaded(() => {
    document.querySelectorAll('.tbp-cloak').forEach((el) => el.classList.remove('tbp-cloak'));
});
