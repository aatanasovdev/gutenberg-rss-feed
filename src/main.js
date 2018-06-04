const { __ } = wp.i18n;

const { Fragment } = wp.element;

const { 
	PanelBody,
	TextControl,
	Button,
	withAPIData
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
		error: {
			type: 'boolean',
			default: false
		}
	},

	edit( { attributes, className, setAttributes } ) {
		const { url, error } = attributes;

		const onChangeURL = newURL => {
			setAttributes( { url: newURL } );
		};			

		const validateURL = () => {

			setAttributes( { error: false } );

			wp.apiRequest({
				url: wpApiSettings.validateFeedUrl,
				data: {
					url: url
				},
				type: 'GET',
				dataType: 'json'
			}).done( ( response ) => {
				if(!response.success) {
					setAttributes( { error: true } );
				}
			}).fail( () => {
				alert('Something went wrong!')
			});
		};

		return (
			<Fragment>
				<div				
					className={ className }
				>
					<TextControl
						label={ __( 'Feed URL', 'gutenberg-rss-feed' ) }
						placeholder={ __( 'Type the URL of your RSS feed.', 'gutenberg-rss-feed' ) }
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
					{ error && <p className="components-placeholder__error">{ __( 'Sorry, either your feed is not a valid one or the URL is incorrect.', 'gutenberg-rss-feed' ) }</p> }

				</div>
			</Fragment>
		);
	},

	save() {
		return null;
	},
});