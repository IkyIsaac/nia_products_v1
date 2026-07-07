/**
 * Editor UI for nia/benefit-grid. Plain wp.element (no JSX/build step —
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
	var TextareaControl = components.TextareaControl;
	var Button = components.Button;
	var __ = i18n.__;

	registerBlockType( 'nia/benefit-grid', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'p-8 bg-surface-container-low border border-dashed border-outline-variant',
			} );
			var items = attributes.items || [];

			function set( key ) {
				return function ( value ) {
					var change = {};
					change[ key ] = value;
					setAttributes( change );
				};
			}

			function updateItem( index, field, value ) {
				var next = items.slice();
				next[ index ] = Object.assign( {}, next[ index ], ( function () {
					var o = {};
					o[ field ] = value;
					return o;
				} )() );
				setAttributes( { items: next } );
			}

			function removeItem( index ) {
				var next = items.slice();
				next.splice( index, 1 );
				setAttributes( { items: next } );
			}

			function addItem() {
				setAttributes( {
					items: items.concat( [ { icon: 'eco', title: '', body: '' } ] ),
				} );
			}

			return el(
				'div',
				blockProps,
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Benefits', 'nia-core' ) },
						items.map( function ( item, index ) {
							return el(
								'div',
								{ key: index, className: 'nia-repeater-item' },
								el( TextControl, {
									label: __( 'Material Symbol icon name', 'nia-core' ),
									value: item.icon,
									onChange: function ( v ) {
										updateItem( index, 'icon', v );
									},
								} ),
								el( TextControl, {
									label: __( 'Title', 'nia-core' ),
									value: item.title,
									onChange: function ( v ) {
										updateItem( index, 'title', v );
									},
								} ),
								el( TextareaControl, {
									label: __( 'Body', 'nia-core' ),
									value: item.body,
									onChange: function ( v ) {
										updateItem( index, 'body', v );
									},
								} ),
								el(
									Button,
									{
										isDestructive: true,
										variant: 'secondary',
										className: 'nia-repeater-item__remove',
										onClick: function () {
											removeItem( index );
										},
									},
									__( 'Remove', 'nia-core' )
								)
							);
						} ),
						el(
							Button,
							{ variant: 'primary', className: 'nia-repeater-add', onClick: addItem },
							__( 'Add benefit', 'nia-core' )
						)
					)
				),
				el( 'p', { className: 'nia-block-editor-notice' }, __( 'Nia Benefit Grid', 'nia-core' ) ),
				el( RichText, {
					tagName: 'span',
					className: 'font-label-lg uppercase tracking-[0.2em] mb-2 block',
					value: attributes.eyebrow,
					onChange: set( 'eyebrow' ),
					placeholder: __( 'Eyebrow…', 'nia-core' ),
					allowedFormats: [],
				} ),
				el( RichText, {
					tagName: 'h2',
					className: 'font-headline-lg text-headline-lg mb-2',
					value: attributes.heading,
					onChange: set( 'heading' ),
					placeholder: __( 'Heading…', 'nia-core' ),
				} ),
				el( RichText, {
					tagName: 'p',
					className: 'font-body-lg mb-4',
					value: attributes.intro,
					onChange: set( 'intro' ),
					placeholder: __( 'Intro copy…', 'nia-core' ),
				} ),
				el(
					'div',
					{ style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(160px, 1fr))', gap: '12px' } },
					items.map( function ( item, index ) {
						return el(
							'div',
							{ key: index, className: 'nia-repeater-item', style: { textAlign: 'center' } },
							el( 'span', { className: 'material-symbols-outlined text-primary' }, item.icon ),
							el( 'p', { style: { fontWeight: 600 } }, item.title || __( '(untitled)', 'nia-core' ) )
						);
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n );
