/**
 * Editor UI for nia/hero. Plain wp.element (no JSX/build step —
 * ARCHITECTURE.md §2). Dynamic block: markup comes from render.php.
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	var el = element.createElement;
	var registerBlockType = blocks.registerBlockType;
	var useBlockProps = blockEditor.useBlockProps;
	var RichText = blockEditor.RichText;
	var InspectorControls = blockEditor.InspectorControls;
	var MediaUpload = blockEditor.MediaUpload;
	var MediaUploadCheck = blockEditor.MediaUploadCheck;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var Button = components.Button;
	var __ = i18n.__;

	registerBlockType( 'nia/hero', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'p-8 bg-surface-container-low border border-dashed border-outline-variant',
			} );

			function set( key ) {
				return function ( value ) {
					var change = {};
					change[ key ] = value;
					setAttributes( change );
				};
			}

			return el(
				'div',
				blockProps,
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Hero Settings', 'nia-core' ) },
						el( TextControl, {
							label: __( 'Primary button text', 'nia-core' ),
							value: attributes.primaryText,
							onChange: set( 'primaryText' ),
						} ),
						el( TextControl, {
							label: __( 'Primary button URL', 'nia-core' ),
							value: attributes.primaryUrl,
							onChange: set( 'primaryUrl' ),
						} ),
						el( TextControl, {
							label: __( 'Secondary button text', 'nia-core' ),
							value: attributes.secondaryText,
							onChange: set( 'secondaryText' ),
						} ),
						el( TextControl, {
							label: __( 'Secondary button URL', 'nia-core' ),
							value: attributes.secondaryUrl,
							onChange: set( 'secondaryUrl' ),
						} ),
						el( MediaUploadCheck, {},
							el( MediaUpload, {
								onSelect: function ( media ) {
									setAttributes( {
										imageUrl: media.url,
										imageId: media.id,
										imageAlt: media.alt || '',
									} );
								},
								allowedTypes: [ 'image' ],
								render: function ( obj ) {
									return el(
										Button,
										{ onClick: obj.open, variant: 'secondary' },
										attributes.imageUrl
											? __( 'Replace hero image', 'nia-core' )
											: __( 'Select hero image', 'nia-core' )
									);
								},
							} )
						)
					)
				),
				el( 'p', { className: 'nia-block-editor-notice' }, __( 'Nia Hero', 'nia-core' ) ),
				attributes.imageUrl
					? el( 'img', {
							src: attributes.imageUrl,
							style: { maxWidth: '240px', display: 'block', marginBottom: '12px' },
					  } )
					: null,
				el( RichText, {
					tagName: 'div',
					className: 'font-label-lg uppercase tracking-[0.2em] mb-2',
					value: attributes.eyebrow,
					onChange: set( 'eyebrow' ),
					placeholder: __( 'Eyebrow label…', 'nia-core' ),
					allowedFormats: [],
				} ),
				el( RichText, {
					tagName: 'h2',
					className: 'font-display-lg text-headline-lg mb-2',
					value: attributes.heading,
					onChange: set( 'heading' ),
					placeholder: __( 'Heading…', 'nia-core' ),
				} ),
				el( RichText, {
					tagName: 'p',
					className: 'font-body-lg',
					value: attributes.body,
					onChange: set( 'body' ),
					placeholder: __( 'Body copy…', 'nia-core' ),
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n );
