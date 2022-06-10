// WordPress dependencies
const { __ } = wp.i18n;

const { Fragment } = wp.element;

const { registerBlockType } = wp.blocks;

const { InspectorControls } = wp.editor;

const { withState } = wp.compose;

const { apiFetch } = wp;

const { 
	PanelBody,
	TextControl,
	Button,
	ToggleControl,
	RangeControl
} = wp.components;

//  Import Styling
import './editor.css';

// Show form fields for configuring an RSS feed that will be rendered on the front-end.
export const edit = ( { attributes, className, setAttributes, setState, error, validated } ) => {
	const { url, numberOfPosts, showDescription, showDate, showContent } = attributes;

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

	const onChangeShowContent = () => {
		setAttributes( { showContent: !showContent } );
	};	

	const validateURL = () => {
		setState( { error: false, validated: false } );
		
		apiFetch( { 
			path: '/gutenbergrssfeed/v2/validateFeedUrl/?url=' + url,
			method: 'GET'
		}).then( ( response ) => {
			if(!response.success) {

				setState( { error: true } );				
				return;
			} 
			
			setState( { validated: true } );
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
						variant="secondary"
						onClick={ validateURL }
						type="submit">
						{ __( 'Validate' ) }
					</Button>
					{ url && <p>{ __('The feed output is only visible on the front-end.') }</p> }
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
						<ToggleControl
							label={ __('Display post content') }
							checked={ showContent }
							onChange={ onChangeShowContent }
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
	title: __('RSS Feed'),

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
		},
		showContent: {
			type: 'boolean',
			default: false
		}		
	},

	edit: withState( { validated: false, error: false } ) ( edit ),

	save: save,
});