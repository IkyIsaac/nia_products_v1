/**
 * Editor UI for nia/cta-banner. Plain wp.element (no JSX/build step —
 * ARCHITECTURE.md §2). Dynamic block: markup comes from render.php.
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	var el = element.createElement;
	var registerBlockType = blocks.registerBlockType;
	var useBlockProps = blockEditor.useBlockProps;
	var RichText = blockEditor.RichText;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var SelectControl = components.SelectControl;
	var ToggleControl = components.ToggleControl;
	var __ = i18n.__;

	registerBlockType( 'nia/cta-banner', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'p-8 bg-surface-container-low border border-dashed border-outline-variant text-center',
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
						{ title: __( 'CTA Banner Settings', 'nia-core' ) },
						el( SelectControl, {
							label: __( 'Background variant', 'nia-core' ),
							value: attributes.variant,
							options: [
								{ label: __( 'Light (page background)', 'nia-core' ), value: 'light' },
								{ label: __( 'Inverse (dark)', 'nia-core' ), value: 'inverse' },
								{ label: __( 'Primary container (pale gold)', 'nia-core' ), value: 'primary-container' },
							],
							onChange: set( 'variant' ),
						} ),
						el( ToggleControl, {
							label: __( 'Show decorative blurred blobs', 'nia-core' ),
							checked: attributes.showDecorative,
							onChange: set( 'showDecorative' ),
						} ),
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
						} )
					)
				),
				el( 'p', { className: 'nia-block-editor-notice' }, __( 'Nia CTA Banner', 'nia-core' ) ),
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
