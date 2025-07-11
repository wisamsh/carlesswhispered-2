( function ( $, undefined ) {
	/**
	 *  ConditionalLogicFieldSetting
	 *
	 *  description
	 *
	 *  @date	3/2/18
	 *  @since	ACF 5.6.5
	 *
	 *  @param	type $var Description. Default.
	 *  @return	type Description.
	 */

	var ConditionalLogicFieldSetting = acf.FieldSetting.extend( {
		type: '',
		name: 'conditional_logic',
		events: {
			'change .conditions-toggle': 'onChangeToggle',
			'click .add-conditional-group': 'onClickAddGroup',
			'focus .condition-rule-field': 'onFocusField',
			'change .condition-rule-field': 'onChangeField',
			'change .condition-rule-operator': 'onChangeOperator',
			'click .add-conditional-rule': 'onClickAdd',
			'click .remove-conditional-rule': 'onClickRemove',
		},

		$rule: false,
		scope: function ( $rule ) {
			this.$rule = $rule;
			return this;
		},

		ruleData: function ( name, value ) {
			return this.$rule.data.apply( this.$rule, arguments );
		},

		$input: function ( name ) {
			return this.$rule.find( '.condition-rule-' + name );
		},

		$td: function ( name ) {
			return this.$rule.find( 'td.' + name );
		},

		$toggle: function () {
			return this.$( '.conditions-toggle' );
		},

		$control: function () {
			return this.$( '.rule-groups' );
		},

		$groups: function () {
			return this.$( '.rule-group' );
		},

		$rules: function () {
			return this.$( '.rule' );
		},

		$tabLabel: function () {
			return this.fieldObject.$el.find('.conditional-logic-badge');
		},

		$conditionalValueSelect: function () {
			return this.$( '.condition-rule-value' );
		},

		open: function () {
			var $div = this.$control();
			$div.show();
			acf.enable( $div );
		},

		close: function () {
			var $div = this.$control();
			$div.hide();
			acf.disable( $div );
		},

		render: function () {
			// show
			if ( this.$toggle().prop( 'checked' ) ) {
				this.$tabLabel().addClass('is-enabled');
				this.renderRules();
				this.open();

				// hide
			} else {
				this.$tabLabel().removeClass('is-enabled');
				this.close();
			}
		},

		renderRules: function () {
			// vars
			var self = this;

			// loop
			this.$rules().each( function () {
				self.renderRule( $( this ) );
			} );
		},

		renderRule: function ( $rule ) {
			this.scope( $rule );
			this.renderField();
			this.renderOperator();
			this.renderValue();
		},

		renderField: function () {
			// vars
			var choices = [];
			var validFieldTypes = [];
			var cid = this.fieldObject.cid;
			var $select = this.$input( 'field' );

			// loop
			acf.getFieldObjects().map( function ( fieldObject ) {
				// vars
				var choice = {
					id: fieldObject.getKey(),
					text: fieldObject.getLabel(),
				};

				// bail early if is self
				if ( fieldObject.cid === cid ) {
					choice.text += ' ' + acf.__( '(this field)' );
					choice.disabled = true;
				}

				// get selected field conditions
				var conditionTypes = acf.getConditionTypes( {
					fieldType: fieldObject.getType(),
				} );

				// bail early if no types
				if ( ! conditionTypes.length ) {
					choice.disabled = true;
				}

				// calculate indents
				var indents = fieldObject.getParents().length;
				choice.text = '- '.repeat( indents ) + choice.text;

				// append
				choices.push( choice );
			} );

			// allow for scenario where only one field exists
			if ( ! choices.length ) {
				choices.push( {
					id: '',
					text: acf.__( 'No toggle fields available' ),
				} );
			}

			// render
			acf.renderSelect( $select, choices );

			// set
			this.ruleData( 'field', $select.val() );
		},

		renderOperator: function () {
			// bail early if no field selected
			if ( ! this.ruleData( 'field' ) ) {
				return;
			}

			// vars
			var $select = this.$input( 'operator' );
			var val = $select.val();
			var choices = [];

			// set saved value on first render
			// - this allows the 2nd render to correctly select an option
			if ( $select.val() === null ) {
				acf.renderSelect( $select, [
					{
						id: this.ruleData( 'operator' ),
						text: '',
					},
				] );
			}

			// get selected field
			var $field = acf.findFieldObject( this.ruleData( 'field' ) );
			var field = acf.getFieldObject( $field );

			// get selected field conditions
			var conditionTypes = acf.getConditionTypes( {
				fieldType: field.getType(),
			} );

			// html
			conditionTypes.map( function ( model ) {
				choices.push( {
					id: model.prototype.operator,
					text: model.prototype.label,
				} );
			} );

			// render
			acf.renderSelect( $select, choices );

			// set
			this.ruleData( 'operator', $select.val() );
		},

		renderValue: function () {
			// bail early if no field selected
			if ( ! this.ruleData( 'field' ) || ! this.ruleData( 'operator' ) ) {
				return;
			}

			var $select = this.$input( 'value' );
			var $td = this.$td( 'value' );
			var currentVal = $select.val();
			var savedValue = this.$rule[0].getAttribute( 'data-value' );

			// get selected field
			var $field = acf.findFieldObject( this.ruleData( 'field' ) );
			var field = acf.getFieldObject( $field );
			// get selected field conditions
			var conditionTypes = acf.getConditionTypes( {
				fieldType: field.getType(),
				operator: this.ruleData( 'operator' ),
			} );

			var conditionType = conditionTypes[ 0 ].prototype;
			var choices = conditionType.choices( field );
			let $newSelect;
			if ( choices instanceof jQuery && !! choices.data( 'acfSelect2Props' ) ) {
				$newSelect = $select.clone();
				// If converting from a disabled input, we need to convert it to an active select.
				if ( $newSelect.is( 'input' ) ) {
					var classes = $select.attr( 'class' );
					const $rebuiltSelect = $( '<select></select>' ).addClass( classes ).val( savedValue );
					$newSelect = $rebuiltSelect;
				}

				acf.addAction( 'acf_conditional_value_rendered', function() {
					acf.newSelect2( $newSelect, choices.data( 'acfSelect2Props' ) );
				});
			} else if ( choices instanceof Array ) {
				this.$conditionalValueSelect().removeClass( 'select2-hidden-accessible' );
				$newSelect = $( '<select></select>' );
				acf.renderSelect( $newSelect, choices );
			} else {
				this.$conditionalValueSelect().removeClass( 'select2-hidden-accessible' );
				$newSelect = $( choices );
			}

			// append
			$select.detach();
			$td.html( $newSelect );

			// timeout needed to avoid browser bug where "disabled" attribute is not applied
			setTimeout( function () {
				[ 'class', 'name', 'id' ].map( function ( attr ) {
					$newSelect.attr( attr, $select.attr( attr ) );
				} );
				$select.val( savedValue );
				acf.doAction( 'acf_conditional_value_rendered' );
			}, 0 );
			// select existing value (if not a disabled input)
			if ( ! $newSelect.prop( 'disabled' ) ) {
				acf.val( $newSelect, currentVal, true );
			}

			// set
			this.ruleData( 'value', $newSelect.val() );
		},

		onChangeToggle: function () {
			this.render();
		},

		onClickAddGroup: function ( e, $el ) {
			this.addGroup();
		},

		addGroup: function () {
			// vars
			var $group = this.$( '.rule-group:last' );

			// duplicate
			var $group2 = acf.duplicate( $group );

			// update h4
			$group2.find( 'h4' ).text( acf.__( 'or' ) );

			// remove all tr's except the first one
			$group2.find( 'tr' ).not( ':first' ).remove();

			// Find the remaining tr and render
			var $tr = $group2.find( 'tr' );
			this.renderRule( $tr );

			// save field
			this.fieldObject.save();
		},

		onFocusField: function ( e, $el ) {
			this.renderField();
		},

		onChangeField: function ( e, $el ) {
			// scope
			this.scope( $el.closest( '.rule' ) );

			// set data
			this.ruleData( 'field', $el.val() );

			// render
			this.renderOperator();
			this.renderValue();
		},

		onChangeOperator: function ( e, $el ) {
			// scope
			this.scope( $el.closest( '.rule' ) );

			// set data
			this.ruleData( 'operator', $el.val() );

			// render
			this.renderValue();
		},

		onClickAdd: function ( e, $el ) {
			// duplicate
			var $rule = acf.duplicate( $el.closest( '.rule' ) );

			// render
			this.renderRule( $rule );
		},

		onClickRemove: function ( e, $el ) {
			// vars
			var $rule = $el.closest( '.rule' );

			// save field
			this.fieldObject.save();

			// remove group
			if ( $rule.siblings( '.rule' ).length == 0 ) {
				$rule.closest( '.rule-group' ).remove();
			}

			// remove
			$rule.remove();
		},
	} );

	acf.registerFieldSetting( ConditionalLogicFieldSetting );

	/**
	 *  conditionalLogicHelper
	 *
	 *  description
	 *
	 *  @date	20/4/18
	 *  @since	ACF 5.6.9
	 *
	 *  @param	type $var Description. Default.
	 *  @return	type Description.
	 */

	var conditionalLogicHelper = new acf.Model( {
		actions: {
			duplicate_field_objects: 'onDuplicateFieldObjects',
		},

		onDuplicateFieldObjects: function ( children, newField, prevField ) {
			// vars
			var data = {};
			var $selects = $();

			// reference change in key
			children.map( function ( child ) {
				// store reference of changed key
				data[ child.get( 'prevKey' ) ] = child.get( 'key' );

				// append condition select
				$selects = $selects.add( child.$( '.condition-rule-field' ) );
			} );

			// loop
			$selects.each( function () {
				// vars
				var $select = $( this );
				var val = $select.val();

				// bail early if val is not a ref key
				if ( ! val || ! data[ val ] ) {
					return;
				}

				// modify selected option
				$select.find( 'option:selected' ).attr( 'value', data[ val ] );

				// set new val
				$select.val( data[ val ] );
			} );
		},
	} );
} )( jQuery );
