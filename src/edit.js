/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { 
	PanelBody,
	TextControl,
	Button,
	ToggleControl,
	RangeControl

} from '@wordpress/components';	

/**
 * Internal dependencies
 */
import Message from './message';

export default function edit( { attributes, setAttributes } ) {
	const [ error, setError ] = useState( false );
	const [ validated, setValidated ] = useState( false );

	const { 
		url, 
		numberOfPosts, 
		showDescription, 
		showDate, showContent 
	} = attributes;

	const onChangeNumber = newNumberOfPosts => {
		setAttributes( { numberOfPosts: newNumberOfPosts } );
	} 

	const onChangeURL = newURL => {
		setAttributes( { url: newURL } );
	};			

	const toggleAttribute = propName => {
		return () => {
			setAttributes( { [ propName ]: ! attributes[ propName ] } );
		}
	}

	const validateURL = () => {
		setError( false );
		setValidated( false );

		apiFetch( { 
			path: '/gutenbergrssfeed/v2/validateFeedUrl/?url=' + url,
			method: 'GET'
		}).then( ( response ) => {
			if( ! response.success ) {
				setError( true );
				return;
			} 
			
			setValidated( true );
		});
	};

	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<div class="custom-block-section">
				<TextControl
					label={ __( 'Feed URL' ) }
					placeholder={ __( 'Type the URL of your RSS feed.' ) }
					value={ url }
					type="url"
					onChange={ onChangeURL }
				/>		
				<Button
					variant="primary"
					onClick={ validateURL }
					type="submit">
					{ __( 'Fetch' ) }
				</Button>				
				<Message error={ error } validated={ validated } />
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
						label={ __( 'Display post description' ) }
						checked={ showDescription }
						onChange={ toggleAttribute( 'showDescription' ) }
					/>
					<ToggleControl
						label={ __( 'Display post date' ) }
						checked={ showDate}
						onChange={ toggleAttribute( 'showDate' ) }
					/>	
					<ToggleControl
						label={ __( 'Display post content' ) }
						checked={ showContent }
						onChange={ toggleAttribute( 'showContent' ) }
					/>	
				</PanelBody>
			</InspectorControls>
		</div>
	);
}