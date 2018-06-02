const { __ } = wp.i18n;

const { Fragment } = wp.element;

const validUrl = require('valid-url');

const { 
	PanelBody,
	TextControl,
	Button
} = wp.components;

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
		const error = false;

		const { url } = attributes;

		const onChangeURL = newURL => {
			setAttributes( { url: newURL } );
		};

		const validateURL = () => {
			// if(validUrl.isWebUri('http://google.com') === 'undefined') {
			// 	error = true;
			// }
		};

		return (
			<Fragment>
				<div				
					className={ className }
				>
					<TextControl
						label={ __( 'Feed URL' ) }
						placeholder={ __( 'Type the URL of your RSS feed.' ) }
						value={ url }
						type="url"
						onChange={ onChangeURL }
					/>
					<Button
						isLarge
						onClick={ validateURL }
						type="submit">
						{ __( 'Validate' ) }
					</Button>
					{ error && <p className="components-placeholder__error">{ __( 'Sorry, either your feed is not a valid one or the URL is incorrect.' ) }</p> }										
				</div>
			</Fragment>
		);
	},

	save() {
		return null;
	},
});