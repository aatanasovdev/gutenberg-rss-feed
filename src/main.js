const { __ } = wp.i18n;

const { Fragment } = wp.element;

const { 
	PanelBody,
	TextControl,
	Button,
	withState,
	RangeControl
} = wp.components;

const {
	registerBlockType,
	InspectorControls,
	UrlInput,
	source
} = wp.blocks;

export const edit = ( { attributes, className, setAttributes, setState, error, validated } ) => {
	const { url, numberOfPosts } = attributes;

	const onChangeNumber = newNumberOfPosts => {
		setAttributes( { numberOfPosts: newNumberOfPosts } );
	} 

	const onChangeURL = newURL => {
		setAttributes( { url: newURL } );
	};			

	const validateURL = () => {
		setState( { error: false, validated: false } );

		wp.apiRequest({
			url: wpApiSettings.validateFeedUrl,
			data: {
				url: url
			},
			type: 'GET',
			dataType: 'json'
		}).done( ( response ) => {
			if(!response.success) {

				setState( { error: true } );
				return;
			} 
			
			setState( { validated: true } );
		}).fail( () => {
			alert('Something went wrong!')
		});
	};

	return (
		<Fragment>
			<div				
				className={ className }
			>
				<div class="components-base-control">
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
					{ error && <p>{ __( 'Sorry, either your feed is not a valid one or the URL is incorrect.' ) }</p> }
					{ !error && validated && <p>{ __( 'Feed validated successfully.' ) }</p> }
				</div>
				<div class="components-base-control">
					<RangeControl
						label={ __( 'Number of posts' ) }
						value={ numberOfPosts }
						min='1'
						max='50'
						onChange={ onChangeNumber }
					/>		
				</div>
			</div>
		</Fragment>
	);
}

export const save = ( props ) => {
	return null;
}

registerBlockType('gutenberg-widget-block/rss-feed', {
	title: 'RSS Feed',

	icon: 'rss',

	category: 'widgets',

	attributes: {
		numberOfPosts: {
			type: 'integer',
		},		
		url: {
			type: 'string',
		}
	},
	edit: withState( { validated: false, error: false } ) ( edit ),

	save: save,
});