/**
 * Styles
 */
import '../css/theme.css';
import '../css/admin.css';

/**
 * Dependencies
 */
import Alpine from 'alpinejs'
import {loaded} from './_helpers';

/**
 * Spectrum Web Components
 */
import '@spectrum-web-components/badge/sp-badge.js';
import '@spectrum-web-components/button/sp-button.js';
import '@spectrum-web-components/button-group/sp-button-group.js';
import '@spectrum-web-components/checkbox/sp-checkbox.js';
import '@spectrum-web-components/divider/sp-divider.js';
import '@spectrum-web-components/field-group/sp-field-group.js';
import '@spectrum-web-components/field-label/sp-field-label.js';
import '@spectrum-web-components/help-text/sp-help-text.js';
import '@spectrum-web-components/icons-workflow/icons/sp-icon-key.js';
import '@spectrum-web-components/infield-button/sp-infield-button.js';
import '@spectrum-web-components/link/sp-link.js';
import '@spectrum-web-components/menu/sp-menu-group.js';
import '@spectrum-web-components/menu/sp-menu-item.js';
import '@spectrum-web-components/picker/sp-picker.js';
import '@spectrum-web-components/tabs/sp-tab.js';
import '@spectrum-web-components/tabs/sp-tab-panel.js';
import '@spectrum-web-components/tabs/sp-tabs.js';
import '@spectrum-web-components/textfield/sp-textfield.js';
import '@spectrum-web-components/theme/sp-theme.js';
import '@spectrum-web-components/theme/src/themes.js';
import '@spectrum-web-components/theme/src/express/themes.js';
import '@spectrum-web-components/toast/sp-toast.js';
import '@spectrum-web-components/action-bar/sp-action-bar.js';
import '@spectrum-web-components/banner/sp-banner.js';
import '@spectrum-web-components/icon/sp-icon.js';
import '@spectrum-web-components/search/sp-search.js';


/**
 * Custom Web Components
 */
import './web-components/multi-picker.js'
import './web-components/alert-banner.js'

/**
 * Alpine Components
 */
import './alpine-components/bible-brains-form.js';

loaded(() => {
    document.querySelectorAll('.br-cloak').forEach((el) => el.classList.remove('br-cloak'));
});

window.Alpine = Alpine
Alpine.start()