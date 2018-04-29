const { __ } = wp.i18n;

const {
	registerBlockType,
	InspectorControls,
	UrlInput,
	source,
} = wp.blocks;

registerBlockType('gutenberg-widget-block/rss-feed', {
	title: 'RSS Feed',

	icon: 'rss',

	category: 'widgets',

	attributes: {
		content: {
			type: 'array',
			source: 'children',
			selector: 'p',
		},
		url: {
			type: 'string',
		},
	},

	edit( { attributes, className, setAttributes } ) {
		const { url } = attributes;

		const onChangeURL = newURL => {
			setAttributes( { url: newURL } );
		};

		return (
			<div>
				<InspectorControls>
					<UrlInput
						value={ url }
						label="Test"
						onChange={ onChangeURL }
					/>
				</InspectorControls>
				<div
					className={ className }
				>
					{ __('Please set up the settings of the widget from the right sidebar.') }
				</div>
			</div>
		);
	},

	save() {
		return null;
	},
});