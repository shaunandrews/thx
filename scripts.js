var thx;
(function($) {

	thx = {
		init: function() {
			$( '.w3-sidebar-widgets .w3-widgets' ).sortable({
				handle: '.w3-widget-header',
				revert: true,
				axis: 'y',
				sort: function( event, ui ) {
					$( '.ui-sortable-placeholder' ).html( 'Move here...' );
				}
			});

			$( '.add-a-widget' ).on( 'click', function() {
				$( '.wp-modal-backdrop' ).fadeIn( 'fast' );
				$( '.wp-modal' ).fadeIn( 'fast' );
			});

			$( '.wp-modal-close, .wp-modal-backdrop' ).on( 'click', function() {
				w3Widgets.closeModal();
			});

			$( '.w3-available-widgets .w3-widget' ).on( 'click', function() {
				var widget = $( this ),
					widgetSettings = $( '.wp-modal-sidebar' );
				
				$( '.w3-available-widgets .selected' ).removeClass('selected');
				widget.addClass( 'selected' );
				widgetSettings.html('').html( widget.html() );
			});

			$( '.wp-modal-sidebar' ).on( 'click', '.w3-widget-save', function(event) {
				event.preventDefault();
				var widget = $('.wp-modal-sidebar').html(),
					sidebar = $( '.w3-sidebar:visible' );

				sidebar.find( '.w3-widgets' ).append( '<li class="w3-widget just-added">' + widget + '</li>' );
				sidebar.find( '.just-added' ).effect("highlight", {}, 3000).removeClass( 'just-added' );
				sidebar.find( '.w3-blank' ).remove();

				w3Widgets.closeModal();
				w3Widgets.countWidgets();
			});

			$( '.w3-tab' ).click( function() {
				$( '.w3-tabs .active' ).removeClass( 'active' );
				$( this ).toggleClass( 'active' );
				var sidebar = $( this ).data( 'sidebar' );

				$( '.w3-sidebars .active' ).removeClass( 'active' );
				$( '.w3-sidebars #' + sidebar ).addClass( 'active' );
			});

			$( '.w3-widgets' ).on( 'click', '.w3-widget-edit', function() {
				$( this ).parent().parent().toggleClass( 'editing' );
				$( this ).parent().next().slideToggle( 'fast' );

				if ( $( this ).parent().parent().hasClass( 'editing' ) ) {
					$( this ).html( 'Cancel' );
				}
				else {
					$( this ).html( 'Edit' );
				}
			});

			w3Widgets.countWidgets();
		},
	}

	$(document).ready(function($){ thx.init(); });

})(jQuery);