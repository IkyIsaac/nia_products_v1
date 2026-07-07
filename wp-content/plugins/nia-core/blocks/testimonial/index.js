/**
 * Editor UI for nia/testimonial. Plain wp.element (no JSX/build step —
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
	var TextareaControl = components.TextareaControl;
	var RangeControl = components.RangeControl;
	var Button = components.Button;
	var __ = i18n.__;

	registerBlockType( 'nia/testimonial', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'p-8 bg-surface-container-low border border-dashed border-outline-variant',
			} );
			var items = attributes.items || [];

			function updateItem( index, field, value ) {
				var next = items.slice();
				var patch = {};
				patch[ field ] = value;
				next[ index ] = Object.assign( {}, next[ index ], patch );
				setAttributes( { items: next } );
			}

			function removeItem( index ) {
				var next = items.slice();
				next.splice( index, 1 );
				setAttributes( { items: next } );
			}

			function addItem() {
				setAttributes( {
					items: items.concat( [ { quote: '', name: '', role: '', avatarUrl: '', rating: 5 } ] ),
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
						{ title: __( 'Testimonials', 'nia-core' ) },
						items.map( function ( item, index ) {
							return el(
								'div',
								{ key: index, className: 'nia-repeater-item' },
								el( TextareaControl, {
									label: __( 'Quote', 'nia-core' ),
									value: item.quote,
									onChange: function ( v ) {
										updateItem( index, 'quote', v );
									},
								} ),
								el( TextControl, {
									label: __( 'Name', 'nia-core' ),
									value: item.name,
									onChange: function ( v ) {
										updateItem( index, 'name', v );
									},
								} ),
								el( TextControl, {
									label: __( 'Role / location', 'nia-core' ),
									value: item.role,
									onChange: function ( v ) {
										updateItem( index, 'role', v );
									},
								} ),
								el( RangeControl, {
									label: __( 'Star rating', 'nia-core' ),
									value: item.rating || 5,
									min: 1,
									max: 5,
									onChange: function ( v ) {
										updateItem( index, 'rating', v );
									},
								} ),
								el( MediaUploadCheck, {},
									el( MediaUpload, {
										onSelect: function ( media ) {
											updateItem( index, 'avatarUrl', media.url );
										},
										allowedTypes: [ 'image' ],
										render: function ( obj ) {
											return el(
												Button,
												{ onClick: obj.open, variant: 'secondary' },
												item.avatarUrl ? __( 'Replace avatar', 'nia-core' ) : __( 'Select avatar', 'nia-core' )
											);
										},
									} )
								),
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
							__( 'Add testimonial', 'nia-core' )
						)
					)
				),
				el( 'p', { className: 'nia-block-editor-notice' }, __( 'Nia Testimonial', 'nia-core' ) ),
				el( RichText, {
					tagName: 'h2',
					className: 'font-display-lg text-headline-lg mb-4',
					value: attributes.heading,
					onChange: function ( v ) {
						setAttributes( { heading: v } );
					},
					placeholder: __( 'Section heading (optional)…', 'nia-core' ),
				} ),
				items.map( function ( item, index ) {
					return el(
						'blockquote',
						{ key: index, style: { fontStyle: 'italic', marginBottom: '8px' } },
						item.quote || __( '(empty quote)', 'nia-core' ),
						el( 'footer', {}, item.name )
					);
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n );
