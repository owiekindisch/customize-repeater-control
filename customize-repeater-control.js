(function( api, wp, $ ) {
	'use strict';

	api.RepeaterControl = api.RepeaterControl || {};

	api.RepeaterControl = api.Control.extend({
		STATE: [],

		ready: function ready() {
			var control = this;

			_.bindAll( this, 'addRepeaterRow', 'createRowSetting', 'normalizeSaveRequestQuery' );

			this.fieldQueryPattern = new RegExp( '^' + control.id + '\\[(\\d+)\\]\\[(\\S+)\\]$' );
			this.rowContainer = this.container.find( '.customize-repeater-fields' );
			this.columnContainer = $( '<li class="customize-repeater-field"><ul></ul></li>' )
			this.btnNew = this.container.find( '.customize-add-repeater-field' );

			this.setupRepeaterRows();

			// Remove repeater fields from save request (@todo: Is there a better way?)
			api.bind( 'save-request-params', this.normalizeSaveRequestQuery );

			this.btnNew.on( 'click', this.addRepeaterRow );
		},

		setupRepeaterRows: function () {
			var control = this, rows;

			try {
				rows = JSON.parse( control.setting.get() );
			} catch (e) {
				return false;
			}

			$.each( rows, function(index, value) {
				control.addRepeaterRow( rows );
			} );
		},

		addRepeaterRow: function (event) {
			var control = this, Constructor, Control, options, size, index, setting, stateField, initialState;

			if ( ! ( event instanceof Event ) ) {
				initialState = event;
			}

			size = control.STATE.push( {} );
			index = size - 1;
			stateField = control.STATE[ index ];

			$.each( control.params.fields, function (key, field) {
				var id, defaultValue = '';

				if ( initialState && initialState[ index ] && initialState[ index ].hasOwnProperty( key ) ) {
					defaultValue = initialState[ index ][ key ];
				}

				// Set id
				id = control.id + '[' + index + ']' + '[' + key + ']';

				// Create new setting
				setting = control.createRowSetting( id, defaultValue );
				stateField[ key ] = setting.get();

				// Watch setting
				setting.bind( _.bind( control.watchFieldValue, {control: control, index: index, key: key} ) );

				// Reset field arguments
				field.args = _.extend( field.args, {
					content: null,
					priority: 10 + index,
					settings: {
						'default': id
					}
				} );

				Constructor = api[ field.control ] || api.Control;
				options = _.extend( { params: field.args }, field.args );
				Control = new Constructor( id, options );

				api.control.add( Control );
				setting.preview();
			} );
		},

		createRowSetting: function (id, defaultValue) {
			var control = this, setting;

			setting = api.create( id, id, {}, {
				type: 'repeater',
				transport: control.settings.default.transport,
				previewer: api.previewer
			} );
			setting.set( defaultValue );

			return setting;
		},

		watchFieldValue: function (value) {
			this.control.STATE[ this.index ][ this.key ] = value;
			this.control.setting.set( JSON.stringify( this.control.STATE ) );
		},

		normalizeSaveRequestQuery: function (query) {
			var control = this, changes = JSON.parse( query.customized );

			$.each(changes, function(key, value) {
				if ( control.fieldQueryPattern.exec( key ) ) {
					delete changes[ key ];
				}
			});

			query.customized = JSON.stringify( changes );
		}
	});

	$.extend( api.controlConstructor, {
		repeater: api.RepeaterControl
	} );
})( wp.customize, wp, jQuery );