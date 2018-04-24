const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blocks.InspectorControls;

registerBlockType('gutenberg-widget-block/external-rss-feed', {
	title: __('External RSS Feed'),

	icon: 'rss',

	category: 'widgets',

    attributes: {
        url: {
            type: 'string',
        },
    },

	edit() {
		let info = __('Set up the widget using the block settings on the right sidebar.');

		return <p>{ info }</p>;
	},

	save() {
		return null;
	},
});