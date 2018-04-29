const { __ } = wp.i18n;

const { Fragment } = wp.element;

const { PanelBody } = wp.components;

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
			<Fragment>
				<InspectorControls>
					<PanelBody title={ __('Settings') }>
						<UrlInput
							value={ url }
							onChange={ onChangeURL }
						/>
					</PanelBody>
				</InspectorControls>
				<div
					className={ className }
				>
					{ __('Please set up the settings of the widget from the right sidebar.') }
				</div>
			</Fragment>
		);
	},

	save() {
		return null;
	},
});