// WordPress dependencies
const { __ } = wp.i18n;

const { Fragment } = wp.element;

const { 
	PanelBody,
	TextControl,
	Button,
	withState,
	ToggleControl,
	RangeControl
} = wp.components;

const {
	registerBlockType,
	UrlInput,
	source
} = wp.blocks;

const {
	InspectorControls
} = wp.editor;

//  Import Styling
import './editor.scss';

// Show form fields for configuring an RSS feed that will be rendered on the front-end.
export const edit = ( { attributes, className, setAttributes, setState, error, validated } ) => {
	const { url, numberOfPosts, showDescription, showDate } = attributes;

	const onChangeNumber = newNumberOfPosts => {
		setAttributes( { numberOfPosts: newNumberOfPosts } );
	} 

	const onChangeURL = newURL => {
		setAttributes( { url: newURL } );
	};			

	const onChangeShowDescription = () => {
		setAttributes( { showDescription: !showDescription } );
	};

	const onChangeShowDate = () => {
		setAttributes( { showDate: !showDate } );
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
				<div class="custom-block-section">
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
					{ error && <p class="block-error-message">{ __( 'Sorry, either your feed is not a valid one or the URL is incorrect.' ) }</p> }
					{ !error && validated && <p class="block-success-message">{ __( 'Feed validated successfully.' ) }</p> }
				</div>
				<InspectorControls>
					<PanelBody title={ __( 'RSS Feed Settings' ) }>
						<RangeControl
							label={ __( 'Number of posts to be shown on the front-end' ) }
							value={ numberOfPosts }
							min='1'
							max='50'
							onChange={ onChangeNumber }
						/>
						<ToggleControl
							label={ __('Display post description') }
							checked={ showDescription }
							onChange={ onChangeShowDescription }
						/>
						<ToggleControl
							label={ __('Display post date') }
							checked={ showDate}
							onChange={ onChangeShowDate }
						/>													
					</PanelBody>
				</InspectorControls>
			</div>
		</Fragment>
	);
}

// Rendering in PHP
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
			default: 10
		},		
		url: {			
			type: 'string'
		},
		showDescription: {
			type: 'boolean',
			default: false
		},
		showDate: {
			type: 'boolean',
			default: false
		}
	},

	edit: withState( { validated: false, error: false } ) ( edit ),

	save: save,
});