<?php
/**
 * @var string $nonce
 * @var string $fields
 * @var string $tab
 * @var string $color_scheme_options
 * @var string $error
 */
$this->layout( 'layouts/settings', compact( 'tab' ) );
?>

<sp-dialog>
    <h1 slot="heading">
		<?php esc_html_e( 'FAQ', 'bible-plugin' ); ?>
    </h1>
    <sp-accordion>
        <sp-accordion-item label="Would should I report an error or bug?">
            <div>
                <p>
					<?php esc_html_e( 'If you encounter an error or bug, please report it to us at our GitHub by clicking the button below.', 'bible-plugin' ); ?>
                </p>

                <sp-button variant="cta" href="https://github.com/ajpartner/The-Bible-Plugin/issues">
					<?php esc_html_e( 'Report an error', 'bible-plugin' ); ?>
                </sp-button>
            </div>
        </sp-accordion-item>
        <sp-accordion-item label="How should I request support?">
            <div>
                <p>
					<?php esc_html_e( 'Fill out the support form on our website. You can expect a response within 48 business days.', 'bible-plugin' ); ?>
                </p>

                <sp-button variant="cta" href="<?php echo esc_url( "https://thebibleplugin.com#contact" ) ?>">
					<?php esc_html_e( 'Request support', 'bible-plugin' ); ?>
                </sp-button>
            </div>
        </sp-accordion-item>
        <sp-accordion-item label="Where should I submit feature requests?">
            <div>
                <p>
					<?php esc_html_e( 'Fill out the support form on our website. You can expect a response within 48 business days.', 'bible-plugin' ); ?>
                </p>

                <sp-button variant="cta" href="<?php echo esc_url( "https://thebibleplugin.com#contact" ) ?>">
					<?php esc_html_e( 'Request a feature', 'bible-plugin' ); ?>
                </sp-button>
            </div>
        </sp-accordion-item>
        <sp-accordion-item label="How can I request a language or translation be added?">
            <div>
                <p>
					<?php esc_html_e( 'The bible reader uses Bible Brains by Faith Comes by Hearing. We recommend you contact them to request additional translations be added the Bible Reader.', 'bible-plugin' ); ?>
                </p>

                <p>
					<?php esc_html_e( 'We rely on community support to add additional languages to the Bible Plugin UI. If you are a translator or you would like more information on how you can get involved, please contact us using the contact form on our website.', 'bible-plugin' ); ?>
                </p>

                <sp-button variant="cta"
                           href="<?php echo esc_url( "https://www.faithcomesbyhearing.com/audio-bible-resources/bible-brain" ) ?>">
					<?php esc_html_e( 'Bible Brains', 'bible-plugin' ); ?>
                </sp-button>

                <sp-button variant="cta" href="<?php echo esc_url( "https://thebibleplugin.com#contact" ) ?>">
					<?php esc_html_e( 'The Bible Plugin', 'bible-plugin' ); ?>
                </sp-button>
            </div>
        </sp-accordion-item>
    </sp-accordion>
</sp-dialog>

<sp-dialog>
    <h1 slot="heading">
		<?php esc_html_e( 'About', 'bible-plugin' ); ?>
    </h1>

    <div class="cards">
        <sp-card heading="Reaching Asia">
            <img slot="preview" src="https://reachingasia.com/wp-content/uploads/2021/06/asiacompassyellow.jpg"
                 alt="Reaching Asia"/>

            <p slot="description"><?php echo esc_html( 'We love Asia and are dedicated to seeing all Asian peoples reached with the love of God and Gospel of hope found in Jesus Christ. Reaching Asia, Inc. is a Christian 501(c)(3) non-profit corporation based in the United States. Our Board of Directors oversees the operations of Reaching Asia with an aim to facilitate the work of Believers and like-minded organizations serving in Asia.
', 'bible-plugin' ); ?></p>

            <div slot="footer">
                <sp-button href="<?php echo esc_url( "https://reachingasia.com/" ) ?>">
					<?php esc_html_e( 'About', 'bible-plugin' ); ?>
                </sp-button>
                <sp-button href="<?php echo esc_url( "https://reachingasia.com/give/" ) ?>">
					<?php esc_html_e( 'Give', 'bible-plugin' ); ?>
                </sp-button>
            </div>
        </sp-card>

        <sp-card heading="Bible Plugin">
            <img slot="preview" src="https://thebibleplugin.com/wp-content/uploads/2023/06/tbphomeabout.jpg"
                 alt="Reaching Asia"/>

            <p slot="description"><?php esc_html_e( 'Putting the Word in WordPress.  The Bible Plugin provides a simple Bible reader in WordPress sites with the Bible text accessed through available Bible APIs.  Localization and focus is a priority of this plugin with admin settings allowing for selection of language, Bible version, number of Bible versions available to the end-user, and design elements (fonts, colors).  Simplicity and ease of use for the end-user is the second top priority with the goal of an intuitive interface.  Initial development of the plugin will be based on the Bible Brain / DBPv4 API but with the intention of expanding to other Bible APIs in subsequent releases.  Additional information is available in the SRS document on GitHub.
', 'bible-plugin' ); ?></p>

            <div slot="footer">
                <sp-buttons>
                    <sp-button href="<?php echo esc_url( "https://thebibleplugin.com" ) ?>">
						<?php esc_html_e( 'About', 'bible-plugin' ); ?>
                    </sp-button>
                    <sp-button href="<?php echo esc_url( "https://github.com/ajpartner/The-Bible-Plugin" ) ?>">
						<?php esc_html_e( 'GitHub', 'bible-plugin' ); ?>
                    </sp-button>
                    <sp-button href
                               ="<?php echo esc_url( "https://thebibleplugin.com/" ) ?>">
						<?php esc_html_e( 'Give', 'bible-plugin' ); ?>
                    </sp-button>
                </sp-buttons>
            </div>
        </sp-card>
    </div>
</sp-dialog>

