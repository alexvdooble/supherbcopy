var $ = jQuery.noConflict();
var timer;
var waitTime = 1000;

jQuery( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
    // Fires whenever variation selects are changed
    console.log( 'woocommerce_variation_select_change' );

	if( jQuery('.variable-product-price-holder').length ){
		setTimeout( function(){

			var selected_variable_value = jQuery('select[name="attribute_size"]').val();
            console.log('selected_variable_value: ', selected_variable_value);
			if( selected_variable_value ) {
				var price_html = jQuery('.woocommerce-variation-price').html();
				jQuery('.variable-product-price-holder').html(price_html);
			} else {
				jQuery('.variable-product-price-holder').html('');
			}

            var variation_form = jQuery('.variations_form');
            var variations_data = jQuery.parseJSON( variation_form.attr('data-product_variations'));
            jQuery.each( variations_data, function(key, item){
                console.log(item);
                if( item.attributes['attribute_size']  === selected_variable_value ) {
                    if( ! item.is_in_stock ) {
                        if( jQuery('.AjxPrdText').length ) {
                            jQuery('.AjxPrdText').append('<div class="variable-out-of-stock-message">המוצר חסר במלאי</div>');
                        } else {
                            jQuery('.variable-product-price-holder').append('<div class="variable-out-of-stock-message">המוצר חסר במלאי</div>');
                        }
                    } else {
                        jQuery('.variable-out-of-stock-message').remove();
                    }
                }
            });

		}, 250);
	}

} );

jQuery(document).on('click', '.closeMiniCartWindow', function(e){
	e.preventDefault();
	closeTopSearch();
	jQuery('.js_subBoxWrap.active').removeClass('active');
});

jQuery(document).on('click', '.single_add_to_cart_button', function(e){
    if( jQuery(this).hasClass('disabled') || jQuery(this).hasClass('wc-variation-selection-needed') ) {
        e.preventDefault();
    }
});

jQuery(document).on('click', '.removeFromWishlistBtn', function(e){
    var _this      = jQuery(this);
    _this.parents('tr').addClass('removing');
    var product_id = jQuery(this).attr('data-id');
    ajax_remove_from_wishlist(_this, product_id);
});

$(document).ready(function () {

	ajax_form();
	apply_coupon();
	ajax_load_more();
	ajax_quick_view();
	ajax_add_to_cart();
	ajax_register();
	ajax_login();
	ajax_recommended_products_btn();
	filter_products();
	filter_posts();
	toggle_chat();
	trigger_cart_change();
	on_mail_sent();
	//notice_animation_init();

	$(document).on('focus', '.ui-autocomplete-input', function (e) {
		$(this).autocomplete("search");
		console.log('ss');
	});

	var widgetOptions = {
		 apiKey    : "34880dd8-4813-4fcc-af68-910a38c9a416",
		 snippetId : "a788223318b3a0a46d07",
		 field1    : siteObject.currentpagetitle,
	};

	(function(n){var u=function(){GlassixWidgetClient&&typeof GlassixWidgetClient=="function"?(window.widgetClient=new GlassixWidgetClient(n),widgetClient.attach(),window.glassixWidgetScriptLoaded&&window.glassixWidgetScriptLoaded()):f()},f=function(){r.onload=u;r.src="https://cdn.glassix.net/clients/widget.1.2.min.js";i.parentNode.removeChild(t);i.parentNode.insertBefore(r,i)},i=document.getElementsByTagName("script")[0],t=document.createElement("script"),r;(t.async=!0,t.type="text/javascript",t.crossorigin="anonymous",t.id="glassix-widget-script",r=t.cloneNode(),t.onload=u,t.src="https://cdn.glassix.com/clients/widget.1.2.min.js",!document.getElementById(t.id)&&document.body)&&(i.parentNode.insertBefore(t,i),t.onerror=f)})(widgetOptions)

	// filter_stores();
});

function notice_animation_init(){
	if ( $('.woocommerce-message').length ) {
		setTimeout( function(){
			$('.home .woocommerce-message').fadeOut();
		}, 5000);
	}
}

function ajax_quick_view(){

	$('body').on('click', '.eyeBtn', function(e){
		e.preventDefault();

		var productId = $(this).attr('data-product'),
			loader    = $(this).parents('.hppItemInner').find('.loader');

		if ( productId ) {

			loader.fadeIn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: siteObject.ajaxurl,
				data: {
					'action': 'ajax_quick_view',
					'product': productId,
				},

				success: function (response) {
					loader.fadeOut();

					if ( response.ok) {
						yBox(response.html);

					}

				}
			});
		}
	});

}

function ajax_mini_cart_edit_item_quantity( _this, item_key, amount ){

	if(timer)clearTimeout(timer);

	timer = setTimeout(function(){

		_this.parents('.mini_cart_item').addClass('loading');

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: siteObject.ajaxurl,
			data: {
				'action': 'ajax_mini_cart_edit_item_quantity',
				'item_key': item_key,
				'amount': amount
			},

			success: function (response) {
				_this.parents('.mini_cart_item').removeClass('loading');
					if ( response.fragments ) {

	                jQuery.each(response.fragments, function(key, value) {
	                    jQuery(key).replaceWith(value);
	                });

	            }

			}
		});
	}, 1000);

}

function ajax_login(){
	//Register popup - Next step
	$('.woocommerce-form-login').on('submit',function(e){
		e.preventDefault();
		var _this =  $(this);
		var data = _this.serialize();

		if ( ! _this.valid() ) {
			return;
		}
		_this.find('.loader').fadeIn();
		_this.find('span.error-message').html('');
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: siteObject.ajaxurl,
			data: {
				'action': 'ajax_login',
				'args':data,
			},

			success: function (results) {
				_this.find('.loader').fadeOut();
				if ( results.status === 'error' ) {
					_this.find('span.error-message').html(results.error );
				}else{
					window.location.href = siteObject.homeurl;
				}

			}
		});
	});
	//Register popup - Skip step
	$('body').on('click','.js_skipRegPopStep',function(){
		var stepWrap = $(this).closest('.regPopSteps');
		stepWrap.removeClass('active').next('.regPopSteps').addClass('active');
	});
}
function ajax_register(){
	//Register popup - Next step
	$('body').on('click','.js_nextRegPopStep, .regStepsBtn',function(){
		var stepWrap = $(this).closest('.regPopSteps');
		if($('form',stepWrap).valid() ){

			var step = $('form',stepWrap).hasClass('register-step-2') ? 'step-2' : 'step-1';
			var data = $('form',stepWrap).serialize();
			$('.register-error').empty();

			$('form',stepWrap).find('.loader').fadeIn();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: siteObject.ajaxurl,
				data: {
					'action': 'ajax_register',
					'args':data,
					'step': step
				},

				success: function (results) {
					$('form',stepWrap).find('.loader').fadeOut();
					if ( results.ok) {

						if ( step == 'step-1') {
							$('.register-step-2 [name="user_id"]').val( results.user_id );

						}else{
							$('.thanksStepText b').html(results.email);
						}

						stepWrap.removeClass('active').next('.regPopSteps').addClass('active');
					}else{
						$('.register-error').html(results.message);
					}

				}
			});
		}
	});
	//Register popup - Skip step
	$('body').on('click','.js_skipRegPopStep',function(){
		var stepWrap = $(this).closest('.regPopSteps');
		stepWrap.removeClass('active').next('.regPopSteps').addClass('active');
	});
}

function ajax_remove_from_wishlist( _this, product_id ){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: siteObject.ajaxurl,
        data: {
            'action'    : 'ajax_recommended_product',
            'add'       : '',
            'product_id': product_id
        },

        success: function (results) {
            if ( results.ok) {
                _this.parents('tr').fadeOut(100, function(){
                    jQuery(this).remove();
                })
            } else {
                _this.parents('tr').removeClass('removing');
            }
        }
    });
}

function ajax_recommended_products_btn(){

	$('.open-login').on('click', function(e){
		e.preventDefault();
		$('body').addClass('mobileMenuIsOpen').find('.personalAreaBtn').addClass('active');
	});

	$('body').on('click','.js_addToProds',function(){

		var _this = $(this);
		var addProduct = true;

		if ( _this.hasClass('active') ) {
			addProduct = false;
		}

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: siteObject.ajaxurl,
			data: {
				'action'    : 'ajax_recommended_product',
				'add'       : addProduct,
				'product_id': _this.attr('data-product')
			},

			success: function (results) {
				if ( results.ok) {
					if ( addProduct ) {
						_this.addClass('active');
					}else{
						_this.removeClass('active');
					}
				}
			}
		});
	});
}

function trigger_cart_change(){
	var timeout;

	$('body').on('click','.plusMinusWrap button' ,function(){

		if ( timeout !== undefined ) {
			clearTimeout( timeout );
		}

		timeout = setTimeout(function() {
			$("[name='update_cart']").prop('disabled', false).trigger("click");
		}, 1000 ); // 1 second delay, half a second (500) seems comfortable too

	});
}

function ajax_add_to_cart(){
    $(document).on('click', '.hpProductItemForm .button, .pContent .single_add_to_cart_button', function (e) {
        e.preventDefault();

        if( jQuery(this).hasClass('disabled') ){
            return;
        }

	  var $thisbutton = $(this),
        $form        = $thisbutton.closest('form.cart'),
        id           = $thisbutton.val(),
        product_qty  = $form.find('input[name=quantity]').val() || 1,
        product_id   = $form.find('input[name=product_id]').val() || id,
        variation_id = $form.find('input[name=variation_id]').val() || 0;

        var data = {
            action      : 'woocommerce_ajax_add_to_cart',
            product_id  : product_id,
            product_sku : '',
            quantity    : product_qty,
            variation_id: variation_id,
        };

	  $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

	  $.ajax({
		  type: 'post',
		  url: wc_add_to_cart_params.ajax_url,
		  data: data,
		  beforeSend: function (response) {
			  $thisbutton.removeClass('added').addClass('loading');
		  },
		  complete: function (response) {
			  $thisbutton.addClass('added').removeClass('loading');
		  },
		  success: function (response) {

			  if (response.error && response.product_url) {
				  window.location = response.product_url;
				  return;
			  } else {
				  $('.MiniCartBtnWrap').addClass('active');

				  if ( $('.closeYbox').length) {
				  	$('.closeYbox').click();
				  }
				  $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
			  }
		  },
	  });

	  return false;
  });
}

function on_mail_sent(){
	$(".wpcf7").on( 'wpcf7mailsent', function( event ){
	    $(this).find('[type="text"],[type="tel"],[type="email"],textarea, select').addClass('valIsEmpty');
	});
}


function toggle_chat(){
	$(document).on('click','.chat-btn', function(e){
		e.preventDefault();
		widgetClient.setWidgetVisiblity(true);

	});
}

function apply_coupon(){
	$('body').on('keyup','.haveCouponForm input', function(e){
		var button = $(this).parents('.haveCouponForm').find('button');

		if ( $(this).val() ) {
			button.prop('disabled', false);
		}else{
			button.prop('disabled', true);

		}
	});

	$('body').on('click','.haveCouponForm button', function(e){
		e.preventDefault();
		var input = $(this).parents('.haveCouponForm').find('[name="Coupon-Code"]');

		if ( input.val() ) {
			$('[name="coupon_code"]').val( input.val() );
			$('.checkout_coupon').submit();
		}
	});
	$('.apply-coupon-btn').on('click', function(e){
		e.preventDefault();
		var _this = $(this);
		var coupon = _this.attr('data-coupon');
		$('.coupon-error').remove();
		$('.dealsAndCouponsSection .woocommerce-message').remove();

		if ( coupon ) {
			_this.parents('li').addClass('loading');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: siteObject.ajaxurl,
				data: {
					'action': 'ajax_apply_coupon',
					'coupon': coupon,
				},

				success: function (results) {
					_this.parents('li').removeClass('loading');
					$('<span class="woocommerce-message">' + results.message +  '</span>').hide().appendTo('.dealsAndCouponsSection').fadeIn();
					setTimeout(function(){
						$('.dealsAndCouponsSection .woocommerce-message').fadeOut(300, function(){
							$(this).remove();
						});
					}, 5000);

				}
			});
		}

	});
}

function filter_stores(){
	$('.storesBoxSearchInput').on('keyup', function(){

		var val = $(this).val();

		$('.stores-list > li').each(function(){
			var _this = $(this);
			var city = _this.attr('data-city');
			if (city.indexOf(val) >= 0){
				$(this).show();
			}else{
				$(this).hide();

			}
		});
	});
}

function filter_posts(){

	$('.blogSection .pFilterGroup [type="checkbox"]').on('change', function(){
		var types = new Array(),
			categories = new Array(),
			load = $('.btn-load-more').data('load'),
			offset = $('.btn-load-more').data('offset');


		if(timer)clearTimeout(timer);
		$('.productsLeftFilters .loader').fadeIn();

		timer = setTimeout(function(){
			$('[name="type"]:checked').each(function(){
	  			types.push( $(this).val() );
	  		});

	  		$('[name="categories"]:checked').each(function(){
	  			categories.push( $(this).val() );
	  		});

	  		$.ajax({
	  			type: 'POST',
	  			dataType: 'json',
	  			url: siteObject.ajaxurl,
	  			data: {
	  				'action': 'ajax_filter_posts',
	  				'types': types,
	  				'categories': categories,
	  			},

	  			success: function (results) {
					$('body').removeClass('filtersOpen');
					$('.productsLeftFilters .loader').fadeOut();
	  				if ( results.ok )  {
	  					jQuery('.blogArticlesList').html( results.html );

						if ( ! results.more) {
							$('.loadMoreWrap').hide();

						}else{
							$('.loadMoreWrap').show();

						}


						$('.btn-load-more').attr('data-offset', 8);
	  				}
	  			}
	  		});
	  	},waitTime);

	});
}

function filter_products(){

	$('.sortingList a').on('click', function(e){
		e.preventDefault();
		var atts = jQuery.parseJSON( jQuery('.productsSection').attr('data-filters') );
		var sort = jQuery(this).attr('data-sort');
		if ( sort === 'price-desc' ) {
			atts.ordering.order = 'desc';
		}else{
			atts.ordering.order = 'asc';
		}

		atts.ordering.orderby = sort;

		ajax_results( atts );
	});

	$('.productsSection .pFilterGroup [type="checkbox"]').on('change', function(){
		var atts = jQuery.parseJSON( jQuery('.productsSection').attr('data-filters') );

		if(timer)clearTimeout(timer);

		timer = setTimeout(function(){
			ajax_results( atts );

		},waitTime);
	});
}

function ajax_results( atts ){
	var atts = atts;
	var formats = new Array();
	var lifestyles = new Array();

	$('[name="pa_format"]:checked').each(function(){
		formats.push( $(this).val() );
	});

	$('[name="lifestyle"]:checked').each(function(){
		lifestyles.push($(this).val());
	});
	$('.productsLeftFilters .loader').fadeIn();
	$('.productsRight').addClass('loading');

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: siteObject.ajaxurl,
		data: {
			'action': 'ajax_filter_products',
			'formats': formats,
			'lifestyles': lifestyles,
			'atts': atts,
		},

		success: function (results) {
			$('.productsLeftFilters .loader').fadeOut();
			$('.productsRight').removeClass('loading');

			$('body').removeClass('filtersOpen');

			if ( results.ok )  {
				jQuery('.productsRight').html( results.html );

				if ( results.total > 12) {
					if ( $('.loadMoreWrap').length ) {
						$('.loadMoreWrap').show();

					}
				}else{
					if ( $('.loadMoreWrap').length ) {
						$('.loadMoreWrap').hide();

					}
				}
			}
		}
	});
}


function ajax_load_more() {
	$('.btn-load-more').click(function (e) {
		btn = $(this);
		append = $(this).data('append');
		search = $(this).data('search');
		query = $(this).data('query');
		load = $(this).data('load');
		offset = $(this).data('offset');
		template = $(this).data('template');

		var article_types = new Array();
		var article_categories = new Array();
		var formats = new Array();
		var lifestyles = new Array();

		if ( template == 'post-archive') {


			$('[name="type"]:checked').each(function(){
				article_types.push( $(this).val() );
			});

			$('[name="categories"]:checked').each(function(){
				article_categories.push( $(this).val() );
			});
		}else{
			$('[name="pa_format"]:checked').each(function(){
				formats.push( $(this).val() );
			});

			$('[name="lifestyle"]:checked').each(function(){
				lifestyles.push( $(this).val() );
			});
		}


		e.preventDefault();
		$('.loadMoreWrap .loader').fadeIn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: siteObject.ajaxurl,
			data: {
				'action': 'ajax_load_more',
				'args': query,
				'offset': offset,
				'load': load,
				'template': template,
				'lifestyles': lifestyles,
				'formats': formats,
				'article_types': article_types,
				'article_categories': article_categories,
				'search': search,



			},

			success: function (results) {
				$(append).append(results.html);
				$('.loadMoreWrap .loader').fadeOut();

				if ( results.result_count && $('.resultsText').length) {
					$('.resultsText').html(results.result_count);
				}
				if (!results.more) {
					$('.loadMoreWrap').hide();
				}
			}
		});

		offset = $(this).data('offset') + load;
		btn.attr('data-offset', offset).data('offset', offset);
	});
}


function ajax_form() {
	$('.form-ajax').on('submit', function (e) {
		e.preventDefault();

		var $form = $(this);
		var $status = $form.find('.form-status');
		var action = $form.attr('action');

		$status.html('<div class="loader"></div>');

		$.post({
			dataType: 'json',
			url: siteObject.ajaxurl,
			data: {
				action: action,
				data: $(this).serialize(),
				nonce: siteObject.nonce
			},

			success: function (response) {
				$status.html('<div class="form-message form-message-' + response.status + '">' + response.message + '</div>');

				if ((action == 'ajax_login' || action == 'ajax_register') && response.status === 'success') {
					document.location.href = siteObject.homeurl;
				}
			}
		});
	});
}
