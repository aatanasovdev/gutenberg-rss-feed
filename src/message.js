/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function Message(props) {
	const messages = {
		error: __( 'Sorry, either your feed is not a valid one or the URL is incorrect.' ),
		validated: __( 'Feed validated successfully. The feed output is only visible on the front-end.' )
	}
	return(
		<div class="block-message">
			{ props.error && <p class="block-message-error">{ messages.error }</p> }
			{ !props.error && props.validated && <p class="block-message-success">{ messages.validated }</p> }		
		</div>
	)
}