/**
 * Editor UI for nia/newsletter-signup. Plain wp.element (no JSX/build step —
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
	var __ = i18n.__;

	registerBlockType( 'nia/newsletter-signup', {
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
						{ title: __( 'Newsletter Settings', 'nia-core' ) },
						el( SelectControl, {
							label: __( 'Variant', 'nia-core' ),
							value: attributes.variant,
							options: [
								{ label: __( 'Dark (inverse-surface)', 'nia-core' ), value: 'dark' },
								{ label: __( 'Light (surface-container-low)', 'nia-core' ), value: 'light' },
							],
							onChange: set( 'variant' ),
						} ),
						el( TextControl, {
							label: __( 'Button text', 'nia-core' ),
							value: attributes.buttonText,
							onChange: set( 'buttonText' ),
						} ),
						el( TextControl, {
							label: __( 'Microcopy', 'nia-core' ),
							value: attributes.microcopy,
							onChange: set( 'microcopy' ),
						} )
					)
				),
				el( 'p', { className: 'nia-block-editor-notice' }, __( 'Nia Newsletter Signup (UI only — no email backend wired yet)', 'nia-core' ) ),
				el( RichText, {
					tagName: 'span',
					className: 'font-label-lg uppercase tracking-widest mb-2 block',
					value: attributes.eyebrow,
					onChange: set( 'eyebrow' ),
					placeholder: __( 'Eyebrow…', 'nia-core' ),
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
