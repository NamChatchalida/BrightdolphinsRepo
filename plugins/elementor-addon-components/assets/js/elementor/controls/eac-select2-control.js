
/******************************************************************************************
*
* Description: Creation d'un nouveau control
* 
* 
* @since 1.8.9
******************************************************************************************/

jQuery(window).on("elementor:init", function() {
	"use strict";
	
	var ControlSelectChooser = elementor.modules.controls.BaseData.extend({
		
		onReady: function() {
			this.model_cid = 'elementor-control-default-' + this.model.cid;
			this.$control_select = this.$el.find('.eac-select2');
			this.multiple = this.model.get('multiple'),
			this.object_type = this.model.get('object_type'),	// post, page, product, CPT... ou all
			this.query_type = this.model.get('query_type'),		// post, taxonomy ou term
			this.query_taxo = this.model.get('query_taxo'),		// category, post_tag, product_cat, product_tag, pa_xxxxx (attribute: pa_tissu)
			/*this.options = this.model.get('options');
			if(this.options) {
				this.normalSelect();
			} else {*/
				this.loadData = this.waitForAutocomplete();
			//}
		},
		
		get_the_placeholder: function() {
			return {
                id: 0,
                text: this.model.get('placeholder'),
            };
		},
		
		normalSelect: function() {
			this.$control_select.select2({
				placeholder: '',
				allowClear: true,
				dir: elementorCommon.config.isRTL ? 'rtl' : 'ltr',
				multiple: this.multiple,
				minimumResultsForSearch: Infinity,
			});
		},
		
		waitForAutocomplete: function() {
			var that = this;
			this.$control_select.select2({
				placeholder: this.get_the_placeholder(),
				allowClear: true,
				dir: elementorCommon.config.isRTL ? 'rtl' : 'ltr',
				delay: 250,
				multiple: this.multiple,
				//minimumResultsForSearch: Infinity,
				//minimumInputLength: this.multiple === false ? 3 : 0,
				ajax:{
					type: 'POST',
					dataType: 'json',
					cache: false,
					url: eac_autocomplete_search.ajax_url,
					data: function(params) {
						var query = {
							search : params.term || "",
							object_type: that.object_type,
							query_type: that.query_type,
							query_taxo: that.query_taxo,
							action: eac_autocomplete_search.ajax_action,
							nonce: eac_autocomplete_search.ajax_nonce,
						};
						return query
					},
					processResults: function(data) {
						var result = JSON.parse(data['data']);
						return{	results: result	}
					},
				},
				initSelection: function(element, callback) {
					var elementorSearch = that.getControlValue(); // getControlValue getCurrentValue
					callback({id: '', text: ''});
					
					if(elementorSearch) {
						jQuery.ajax({
							type: 'POST',
							dataType: 'json',
							cache: false,
							url: eac_autocomplete_search.ajax_url,
							data: {
								search: elementorSearch,
								object_type: that.object_type,
								query_type: that.query_type,
								query_taxo: that.query_taxo,
								action: eac_autocomplete_search.ajax_action_reload,
								nonce: eac_autocomplete_search.ajax_nonce,
							},
						}).done(function(response) {
							if(response.success) {
								var result = JSON.parse(response['data']);
								var eacSelect2Options = '';
								jQuery.each(result, function(index, data) {
									var key = data.id;
									var value = data.text;
									if(element.find("option[value='" + key + "']").length === 0) {
										eacSelect2Options += '<option selected="selected" value="' + key + '">' + value + '</option>';
									}
								});
								if(eacSelect2Options !== '') {
									element.append(eacSelect2Options).trigger('change');
								}
							}
						});
					}
				},
			});
		},
		
		onBeforeDestroy() {
			this.$control_select.select2('destroy');
		},
	});
	elementor.addControlView('eac-select2', ControlSelectChooser);
});