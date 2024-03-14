import "../css/plugin.css";

import '@spectrum-web-components/theme/sp-theme.js';
import '@spectrum-web-components/theme/src/themes.js';
import '@spectrum-web-components/banner/sp-banner.js';
import "./web-components/content.js"
import "./web-components/verse.js"
import "./web-components/audio.js"
import "./web-components/video.js"
import {loaded} from "./_helpers.js";

loaded(() => {
    document.querySelectorAll('.tbp-cloak').forEach((el) => el.classList.remove('tbp-cloak'));
});
