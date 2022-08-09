var $ = jQuery.noConflict();

if(window.navigator.userAgent.toLowerCase().indexOf("msie ") > -1 || !!navigator.userAgent.match(/Trident.*rv\:11\./)){
	//if internet Explorer
	document.body.innerHTML = document.getElementById('notSupportedHTML').innerHTML;
}else{
	// MULTI swiper
	document.querySelectorAll('.horizontalSwipe').length && (function(){
		var horizontalSwipe = document.querySelectorAll('.horizontalSwipe:not(.desktopOnly)');
		if(document.querySelector('html').clientWidth > 767){
			horizontalSwipe = document.querySelectorAll('.horizontalSwipe');
		}
		horizontalSwipe.forEach(function(e,i){
			var itemNum			 = (e.getAttribute('data-items') != 'auto') ? parseInt(e.getAttribute('data-items')) : 'auto';
			var autoplay		 = e.getAttribute('data-autoplay') ? parseInt(e.getAttribute('data-autoplay'),10) : false;
			var itemArrows		 = e.getAttribute('data-arrows') ? true : false;
			var speed			 = e.getAttribute('data-speed') ? parseInt(e.getAttribute('data-speed'),10) : 600;
			var margin			 = e.getAttribute('data-margin') ? parseInt(e.getAttribute('data-margin'),10) : 0;
			var loop			 = e.getAttribute('data-loop') ? true : false;
			var itemPagination	 = e.getAttribute('data-pagination') ? true : false;
			var paginationType	 = e.getAttribute('data-pagination-type') ? e.getAttribute('data-pagination-type') : 'bullets';
			var breakpoints_1200 = e.getAttribute('data-breakpoints-1200');
			var breakpoints_1024 = e.getAttribute('data-breakpoints-1024');
			var breakpoints_992	 = e.getAttribute('data-breakpoints-992');
			var breakpoints_768	 = e.getAttribute('data-breakpoints-768');
			var breakpoints_600	 = e.getAttribute('data-breakpoints-600');
			var centeredSlides	 = e.getAttribute('data-center-slides') ? true : false;
			var effect			 = e.getAttribute('data-effect') || 'slide'; // "slide", "fade", "cube", "coverflow" or "flip"
			var startSlideFrom	 = e.getAttribute('data-start') ? parseInt(e.getAttribute('data-start'),10) : 0;
			var rtl				 = (e.getAttribute('data-rtl') == true || !e.getAttribute('data-rtl')) ? true : false;

			// breakpoints - לא כל הפרמטרים עובדים ב
			// effect / pagination / loopedSlides -> breakpoints - לא עובדים ב

			var breakpoints		= {};
			if(breakpoints_1200){  //min-width:992px
				breakpoints['1200'] = JSON.parse(breakpoints_1200);
			}
			if(breakpoints_1024){  //min-width:992px
				breakpoints['1024'] = JSON.parse(breakpoints_1024);
			}
			if(breakpoints_992){  //min-width:992px
				breakpoints['992'] = JSON.parse(breakpoints_992);
			}
			if(breakpoints_768){  //min-width:768px
				breakpoints['768'] = JSON.parse(breakpoints_768);
			}
			if(breakpoints_600){  //min-width:600px
				breakpoints['600'] = JSON.parse(breakpoints_600);
			}

			var wrap = e.closest('.horizontalSwipeWrap');
			var next = wrap &&  wrap.querySelectorAll('.next')
						? wrap.querySelectorAll('.next')
						: e.parentElement.querySelectorAll('.next');
			var prev = wrap && wrap.querySelectorAll('.prev')
						? wrap.querySelectorAll('.prev')
						: e.parentElement.querySelectorAll('.prev');
			var nextBtns = [];
			var prevBtns = [];
			for(var i = 0;i < next.length;i++){
				nextBtns.push(next[i]);
			};
			for(var i = 0;i < prev.length;i++){
				prevBtns.push(prev[i]);
			};

			var swiperConfig = e.getAttribute('data-options') || {
				autoplay				: autoplay ? {
											delay: autoplay
										} : false,
				speed					: speed,
				spaceBetween			: margin,
				slidesPerView			: itemNum,
				updateOnImagesReady		: true,
				preventClicks			: true,
				touchMoveStopPropagation: true,
				rtl						: rtl,
				grabCursor				: true,
				keyboard				: true,
				pagination				: itemPagination ? {
											el 			 : e.querySelector('.swiper-pagination'),
											clickable	 : true,
											renderBullet : function (index, className) {
												return '<button type="button" class="'+className+'"></button>';
											},
											type: paginationType
										} : false,
				navigation				: itemArrows ? {
											nextEl : nextBtns,
											prevEl : prevBtns,
										} : false,
				breakpoints				: breakpoints,
				effect 					: effect,
				loopedSlides			: e.querySelectorAll('.swiper-slide').length,
				loop					: loop,
				centeredSlides			: centeredSlides,
				initialSlide			: startSlideFrom
			};
			var mySwiper = new Swiper(e,swiperConfig);
		});
	}());
	//Accessibility - Focus
	$('html').keyup(function(e){
		if(e.keyCode == 9){ //Tab
			$('body').addClass('showFocus');
		}
	});
	//Scroll to next section
	$('.js_scrollToNextSection').click(function(){
		var scrollTO = $(this).closest('section').next('section').offset().top - $('#header').innerHeight();
		$('html, body').animate({scrollTop : scrollTO},1000);
	});
	//Mobile menu button
	$('.menuBTN').click(function(){
		$('body').toggleClass('mobileMenuIsOpen');
	});
	$('.tabletCloseMenuBtn').click(function(){
		$('body').removeClass('mobileMenuIsOpen');
	});
	//Add class hasSons to category buttons
	$('nav ul ul').each(function(){
		$(this).closest('li').addClass('hasSons').find('>a').after('<button type="button" class="openSubMenu"></button>');
	});
	//Accessibility - Focus on sub menu arrow
	$('.openSubMenu').focus(function(){
		if($('body').hasClass('showFocus'))
			$(this).closest('li').toggleClass('focus');
	});
	$('.openSubMenu').click(function(){
		if($('body').hasClass('showFocus'))
			$(this).closest('li').toggleClass('focus');
	});
	//Accessibility - Close sub menu on blur
	$('li.hasSons > ul > li:first-child > a, li.hasSons > ul > li:last-child > a').blur(function(){
		if(t){
			clearTimeout(t);
		}
		var self = $(this);
		var isFocus = false;
		var t = setTimeout(function(){
			if(self.closest('ul').find('a:focus').length){
				isFocus = true;
			}
			if(!isFocus)
				self.closest('li.focus').removeClass('focus');
		},200);
	});
	//Contrast button
	$('.js_contrastBtn, .tabletContrastBtn').click(function(){
		$('body').toggleClass('showContrast');
	});
	//Shop by
	$('.headerFiltersList > li').hover(function(){
		$('.hflType').not($(this) ).removeClass('active');
		$(this).toggleClass('active');
		if(!$(this).hasClass('active')){
			$('.filtersMegaMenuListBtn.active').trigger('click');
		}
	});
	//Shop by
	var filtersContentTimeout;
	$('.filtersMegaMenuListBtn').hover(function(e){
		if(document.querySelector('html').clientWidth > 991){
			e.preventDefault();
			clearTimeout(filtersContentTimeout);

			var self = $(this);
			var container = self.closest('.filtersMegaMenu');
			$('.filtersMegaMenuListBtn').not(self).removeClass('active');
			self.toggleClass('active');
			if(self.hasClass('active')){
				var content = self.next('.megaMenuItemContent').html();
				container.find('.megaMenuItemContentHere').html(content);
			}else{
				if(!$('.filtersMegaMenuListBtn.active').length){
					container.removeClass('active');
					filtersContentTimeout = setTimeout(function(){
						container.find('.megaMenuItemContentHere').html('');
					},500);
				}
			}
			if($('.filtersMegaMenuListBtn.active').length){
				container.addClass('active');
			}else{
				container.removeClass('active');
			}
		}
	});
	if(jQuery.validator){
		function runValidator(){
			$('form').each(function(){
				$(this).validate({
					ignoreTitle	: true,
					rules: {
						checkPhone: {
							checkPhone: true
						},
						password : {
							minlength : 7,
							required  : true,
							ContainsAtLeastOneDigit: true,
						},
						retype_password : {
							minlength : 7,
							required  : true,
							ContainsAtLeastOneDigit: true,
							equalTo   : $('[name="password"]',this)
						},
						confirm_email : {
							equalTo : $('.emailToConfirm',this)
						}
					}
				});
			});
		};
		$.extend(jQuery.validator.messages, {
			required		: "שדה חובה",
			passwordsMatch	: "הסיסמאות לא תואמות",
			remote			: "דוא”ל קיים במערכת",
			email			: "דוא”ל לא תקין",
			url				: "נא להכניס כתובת אינטרנט - לדוגמה http://www.wikipedia.com",
			date			: "יש להכניס תאריך תקין",
			dateISO			: " להכניס תאריך תקין (ISO)",
			number			: "יש להכניס מספר",
			digits			: "ניתן להכניס ספרות בלבד",
			creditcard		: "מספר כרטיס האשראי שהזנת אינו תקין",
			equalTo			: "השדות אינם זהים",
			accept			: "אנא הזינו ערך עם סיומת חוקית",
			maxlength		: jQuery.validator.format("ניתן להכניס עד {0} תווים."),
			minlength		: jQuery.validator.format("יש להכניס לפחות {0} תווים."),
			rangelength		: jQuery.validator.format("נא להכניס ערך בין {0} ל {1} תווים."),
			range			: jQuery.validator.format("נא להכניס ערך בין {0} ל - {1} תווים."),
			max				: jQuery.validator.format("נא להכניס מספר עד {0}."),
			min				: jQuery.validator.format("נא להכניס מספר החל מ {0}.")
		});
		$.validator.messages.required = function (param, input) {
			if(input.placeholder || input.dataset.defaultPlaceholder){
				return (input.placeholder || input.dataset.defaultPlaceholder) + ' - שדה חובה';
			}else{
				if(input[0])
					return input[0].innerText+' - שדה חובה';
				else
					return 'שדה חובה';
			}
		}
		$.validator.messages.email = function (param, input) {
			if(input.placeholder || input.dataset.defaultPlaceholder){
				return (input.placeholder || input.dataset.defaultPlaceholder) + ' - דוא”ל לא תקין.';
			}else{
				return 'דוא”ל לא תקין.';
			}
		}
		$.validator.messages.digits = function (param, input) {
			if(input.placeholder || input.dataset.defaultPlaceholder){
				return (input.placeholder || input.dataset.defaultPlaceholder) + ' - ניתן להזין מספרים בלבד.';
			}else{
				return 'ניתן להזין מספרים בלבד.';
			}
		}
	}

	jQuery.validator.addMethod("ContainsAtLeastOneDigit", function (value) {
        return /^[a-z]+[0-9]/i.test(value);
	}, 'חובה להכיל מספר ואות אחת לפחות.');
	//add labels to inputs
	document.querySelectorAll('.wpcf7-form-control:not(.wpcf7-submit):not(span)').forEach(function(e,i){
		var placeholder = '';
		if(e.tagName === 'SELECT'){
			placeholder = e.querySelectorAll('option')[0].innerText;
		}else{
			placeholder = e.getAttribute('placeholder');
		}
		var name = e.getAttribute('name');
		var id = name+'Input'+i;
		var title = '';
		e.classList.add('js_forLabel');
		e.setAttribute('id',id);

		var label = document.createElement("label");
		label.innerHTML = '<span>'+placeholder+'</span>';
		label.className = 'inputsLabel hideInputsLabel';
		label.setAttribute('for',id);
		insertAfter(e,label);
	});
	function insertAfter(referenceNode,newNode){
		referenceNode.parentNode.insertBefore(newNode,referenceNode.nextSibling);
	};

	//************************
	//placeholder
	function showLabels(){
		document.querySelectorAll('.js_forLabel').forEach(function(field,i){
			field.addEventListener("change", function(){
				checkIfVal(field);
			});
			checkIfVal(field);
		});
		document.querySelectorAll('.hideInputsLabel').forEach(function(label,i){
			label.classList.remove('hideInputsLabel');
		});
	};
	function checkIfVal(field){
		if(field.tagName === 'SELECT'){
			if(field.value.length && field.firstElementChild.innerText != field.value){
				field.classList.remove('valIsEmpty');
			}else{
				field.classList.add('valIsEmpty');
			}
		}else{
			if(field.value.length){
				field.classList.remove('valIsEmpty');
			}else{
				field.classList.add('valIsEmpty');
			}
		}
	};
	if(document.querySelectorAll('.js_forLabel').length){
		showLabels();
		for(var i = 0; i < document.querySelectorAll('.js_forLabel:not(select)').length; i++){
			var input = document.querySelectorAll('.js_forLabel:not(select)')[i];
			input.setAttribute('data-default-placeholder',input.placeholder);
			input.addEventListener("focus", function(){
				this.setAttribute('placeholder','');
			});
			input.addEventListener("blur", function(){
				this.setAttribute('placeholder',this.getAttribute('data-default-placeholder'));
			});
		};
	}
	function yBoxIsOpen(){
		showLabels();
		runValidator();
	};
	//*************

	$('.showPasswordBtn').siblings('input[type="password"]').keyup(function(){
		if($(this).val().length){
			$(this).siblings('.showPasswordBtn').removeClass('hide');
		}else{
			$(this).siblings('.showPasswordBtn').addClass('hide');
		}
	});
	//Open login popup
	$('.js_openSubBox').click(function(){
		var wrap = $(this).closest('.js_subBoxWrap');
		$('.js_subBoxWrap.active').not(wrap).removeClass('active');
		wrap.toggleClass('active');
	});
	//Show Password.
	$('body').on('click','.showPasswordBtn',function(){
		var passField = $(this).siblings('.passwordField');
		var type = passField.attr('type') == 'password' ? 'text' : 'password';
		passField.attr('type',type);
		$(this).toggleClass('active');
	});
	//forgot Password window.
	$('.forgotPassBtn').click(function(){
		$('.topLoginPopupInner, .topLoginPopupPassword').toggleClass('active');
	});

	//************************
	//plus minus +/-
	$('.js_minusAmount').each(function(){
		var wrap = $(this).closest('.plusMinusWrap');
		if(wrap.find('.js_plusMinusField').val() == 1){
			wrap.find('.js_minusAmount').addClass('disabled');
		}
	});
	$('body').on('click','.js_plusAmount',function(){
		var self = $(this);
		var item_key = $(this).parents('.quantity-wrap').attr('data-key');

		var wrap = self.closest('.plusMinusWrap');
		var jumps = parseInt(wrap.data('jumps'),10);
		var amount = parseInt(wrap.find('.js_plusMinusField').val(),10);
		amount += jumps;
		wrap.find('.js_plusMinusField').val(amount);

		if ( item_key ) {
			ajax_mini_cart_edit_item_quantity( self, item_key, amount);
		}

		if(wrap.find('.js_minusAmount').hasClass('disabled')){
			wrap.find('.js_minusAmount').removeClass('disabled');
		}
	});
	$('body').on('click','.js_minusAmount',function(){
		var self = $(this);
		var item_key = $(this).parents('.quantity-wrap').attr('data-key');
		var wrap = self.closest('.plusMinusWrap');
		var jumps = parseInt(wrap.data('jumps'),10);
		var min = parseInt(wrap.data('min'),10);
		var amount = parseInt(wrap.find('.js_plusMinusField').val(),10);

		if(amount > min){
			if(self.hasClass('disabled')){
				self.removeClass('disabled');
			}
			amount -= jumps;

			wrap.find('.js_plusMinusField').val(amount);
			if(amount == 1){
				self.addClass('disabled');
			}
		}else{
			self.addClass('disabled');
		}

		if ( item_key ) {
			ajax_mini_cart_edit_item_quantity( self, item_key, amount);
		}

	});
	$('body').on('change','.js_plusMinusField',function(){
		var self = $(this);
		if(self.val() == '' || self.val() < 1 || /[^0-9]/.test(self.val())){
			self.val(1);
		}
	});
	//**********************
	function stickyHeader(){
		if(document.querySelector('.headerWaypoint').getBoundingClientRect().top <= 0){
			if(!document.querySelector('body').getAttribute('class') ||
			(document.querySelector('body').getAttribute('class') && document.querySelector('body').getAttribute('class').indexOf('headerActive') == -1)){
				// Down
				document.querySelector('body').classList.add('headerActive');
			}
		}else if(document.querySelector('body').getAttribute('class') && document.querySelector('body').getAttribute('class').indexOf('headerActive') > -1){
			// Up
			document.querySelector('body').classList.remove('headerActive');
		}
	};
	// sticky header
	window.onscroll = function(){
		stickyHeader();
	};
	document.addEventListener('DOMContentLoaded',function(){
		stickyHeader();
		if($('[href="#newsletterPopup"]').length){
			$('[href="#newsletterPopup"]').click();
		}
		if($('[href="#videoPopup"]').length){
			$('[href="#videoPopup"]').click();
		}
	},false);
	//Add To Wishlist
	$('body').on('click','.js_addToWishlist',function(){
		$(this).toggleClass('active');
	});
	//Share on Facebook
	$('body').on('click','.facebookShareBtn',function(){
		var href = $(this).data('href');
		window.open('https://www.facebook.com/sharer/sharer.php?u='+href, '', 'resizable=yes,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no,width=900,left=200,height=300,top=100');
	});
	//Copy Text
	$('body').on('click','.js_copyTextBtn',function(){
		var copyText = $(this).next('.js_copyThisText').text();
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(copyText).select();
		document.execCommand("copy");
		$temp.remove();
		alert('כתובת ה-URL הועתקה ללוח\n'+copyText);
	});
	//Open top search
	$('.openTopSearch').click(function(){
		$('body').toggleClass('topSearchIsOpen');
		if($('body').hasClass('topSearchIsOpen')){
			$('.headerSearchField').focus();
		}else{
			$('.openTopSearch').focus();
		}
	});
	$(document).keyup(function(e){
		if(e.keyCode === 27){ //Esc
			closeTopSearch();
			$('.js_subBoxWrap.active').removeClass('active');
		}
	});

	function closeTopSearch(){
		if($('body').hasClass('topSearchIsOpen')){
			$('body.topSearchIsOpen').removeClass('topSearchIsOpen');
			$('.openTopSearch').focus();
		}
	};
	$('.tabletPAreaBtn').click(function(){
		$('.personalAreaBtn .js_openSubBox').click();
	});
	$('.hpcglItem').click(function(){
		if(document.querySelector('html').clientWidth <= 767){
			window.location.href = $('.hpcglItemBtns_viewProducts',this).attr('href');
		}
	});
	$('.footerLinksTitle').click(function(){
		if(document.querySelector('html').clientWidth <= 767){
			$(this).closest('.footerLinksWrap').toggleClass('active');
		}
	});
	//Mobile - Close mini cart
	$('.closeMiniCartWindow').click(function(){
		$(this).closest('.js_subBoxWrap.active').removeClass('active');
	});
	//Sort Button
	$('.sortingBtn').click(function(){
		var wrap = $(this).closest('.sortingBtnWrap');
		$('.sortingBtnWrap.active').not(wrap).removeClass('active');
		wrap.toggleClass('active');
	});
	//Filters group Button
	$('.pFilterGroupBtn').click(function(){
		$(this).closest('.pFilterGroup').toggleClass('active');
	});
	//disabled link
	$('a.disable').click(function(e){
		e.preventDefault();
	});
	//order buttons
	$('.orderBtn').click(function(){
		var self = $(this);
		var view = self.attr('data-view');
		$('.orderBtn.active').not(self).removeClass('active');
		self.addClass('active');
		$('body').attr('data-view',view);
	});
	//toggle filter window
	$('.openFiltersBtn').click(function(){
		$('body').toggleClass('filtersOpen');
	});
	//close filter window
	$('.closeFiltersBtn, .productsLeftFiltersOverlayBG').click(function(){
		$('body').removeClass('filtersOpen');
	});
	//Product page Gallery
	if(document.querySelectorAll('.verticalSwiperWrap').length){
		var galleryThumbs = new Swiper('.gallery-thumbs', {
			spaceBetween			: 11,
			slidesPerView			: 5,
			loop					: false,
			freeMode				: true,
			loopedSlides			: 5,
			watchSlidesVisibility	: true,
			direction				: 'vertical',
			watchSlidesProgress		: true,
			breakpoints: {
				1200: {
					spaceBetween: 24
				},
				768: {
					spaceBetween: 14
				},
			}
		});
		var galleryTop = new Swiper('.gallery-top', {
			spaceBetween	: 0,
			loop			: false,
			loopedSlides	: 5,
			navigation		: false,
			thumbs			: {
				swiper	: galleryThumbs,
			},
		});
	}

	//Tabs
	$('.tabsBtn').length && (function(){
		$('.tabsBtn').click(function(){
			var self = $(this);
			$('.ul_class > li.active').removeClass('active');
			self.parent().addClass('active');
			var html = self.siblings('.tabsDiv').html();
			$('.tabsDivHere').html('<div class="tabsDiv">'+html+'</div>');
			self.parent().toggleClass('mActive');
		});
		if(document.querySelector('html').clientWidth > 767){
			$('.tabsBtn').first().click();
		}
	}());
	//select2
	$('.select2').length && (function(){
		$('.select2').select2({
			dir : "ltr",
			minimumResultsForSearch : -1 //search field
		}).on('change', function() {
			$(this).valid();
		});
	}());
	//Stores page - Tabs
	$('.storesBoxBtn').each(function(i){
		$(this).click(function(){
			$('.storesBoxBtn.active').not($(this)).removeClass('active');
			$(this).addClass('active');
			$('.storesBoxMarketsList.active').not($('.storesBoxMarketsList').eq(i)).removeClass('active');
			$('.storesBoxMarketsList').eq(i).addClass('active');
			$('.storesBoxRightInner.active').not($('.storesBoxRightInner').eq(i)).removeClass('active');
			$('.storesBoxRightInner').eq(i).addClass('active');
		});
	});
	//Stores map
	function mapFunction( langWidth,langHeight,myZoom,autoClick,rowNumber){
		if(!myZoom){
			myZoom = 15;
		};
		if(!langWidth){
			langWidth = locations[0][0];
			langHeight = locations[0][1];
		};
		var map = new google.maps.Map(document.getElementById('storesMap'), {
			zoom: myZoom,
			scrollwheel: true,
			center: new google.maps.LatLng(langWidth,langHeight),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		var marker, i;
		var markersArr = [];

		for (i = 0; i < locations.length; i++){

			// נקודת ציון - תמונה
			var mapIcon  = new google.maps.MarkerImage(siteObject.themepath + '/assets/images/map_icon.svg',
						   new google.maps.Size(48, 55),  // img size
						   new google.maps.Point(0, 0),
						   new google.maps.Point(24, 55)); // half image size

			// נקודת ציון - תמונה במצב פעיל
			var mapIconActive = new google.maps.MarkerImage(siteObject.themepath +  '/assets/images/map_icon_active.svg',
						   new google.maps.Size(56, 63),  // img size
						   new google.maps.Point(0, 0),
						   new google.maps.Point(28, 63)); // half image size

			marker = new google.maps.Marker({
				position	: new google.maps.LatLng(locations[i][0], locations[i][1]),
				map			: map,
				icon		: mapIcon
			});
			markersArr.push(marker);
			google.maps.event.addListener(marker, 'click', (function(marker, idx) {
				return function() {
					var self = $(this);
					var clicked = self.data('clicked');
					for(var j=0;j<markersArr.length;j++){
						markersArr[j].setIcon(mapIcon);
						if(markersArr[j]!=this)
							$(markersArr[j]).data('clicked',false);
					}
					var btn = $('.js_mapCoordinates').eq(idx).parent();
					if(!clicked){
						$('.js_mapCoordsBtnParent.active').not(btn).removeClass('active');
						btn.addClass('active');
						this.setIcon(mapIconActive);
					}else{
						btn.removeClass('active');
						this.setIcon(mapIcon);
					};
					self.data('clicked',!clicked);
				}
			})(marker, i));
		};
		$('.js_mapCoordinates').each(function(idx2){
			//btn on list
			$(this).click(function(){
				var lat = $(this).attr('data-latitude');
				var lon = $(this).attr('data-longitude');
				if(!$(this).parent().hasClass('active')){
					marker.map.setCenter(new google.maps.LatLng( lat, lon) );
				}
				new google.maps.event.trigger( markersArr[idx2], 'click' );
			});
		});
		if(autoClick){
			new google.maps.event.trigger( markersArr[rowNumber], 'click' );
		};
	};
	if($('#storesMap').length){
		var locations = [];
		$('.js_mapCoordinates').each(function(){
			var lat = $(this).attr('data-latitude');
			var lon = $(this).attr('data-longitude');
			locations.push([lat,lon]);
		});
		mapFunction();
	}
	//Ship to different address
	$('#differentAddressCheckbox').change(function(){
		if($(this).is(':checked')){
			$('.cdfChckbxShipFieldsWrap').addClass('active').find('[disabled]').removeAttr('disabled');
		}else{
			$('.cdfChckbxShipFieldsWrap').removeClass('active').find('.wpcf7-form-control').attr('disabled','disabled');
		}
	});
	//Share on Facebook
	$('.facebookShareBtn').click(function(){
		var href = $(this).data('href');
		window.open('https://www.facebook.com/sharer/sharer.php?u='+href, '', 'resizable=yes,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no,width=900,left=200,height=300,top=100');
	});
	//twitter
	$('.twitterShareBtn').click(function(){
		var href = $(this).data('href');
		window.open('https://twitter.com/share?url='+href, '', 'resizable=yes,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=no,dependent=no,width=700,left=200,height=400,top=100');
	});
	//Deals and Coupons - Mobile - Show all checkboxes
	$('.clearCheckboxes').click(function(){
		$(this).closest('.sortingList').find('input[type="checkbox"]').prop('checked',false);
	});
	//Personal area deshboard - Open order
	$('.myOrderItemDetails').click(function(){
		var parent = $(this).parent();
		$('.myOrdersList > li.active').not(parent).removeClass('active');
		parent.toggleClass('active');
	});
	//Personal area deshboard - Mobile - Open pArea menu
	$('.pAreaMenuMobileBtn').click(function(){
		$(this).parent().toggleClass('active');
	});
	//Personal area - edit field
	$('.iconEditField').click(function(){
		$(this).siblings('[readonly]').removeAttr('readonly').focus();
		$(this).hide().siblings('.iconSaveField').show();
	});
	//mobile - open logout button
	$('.js_openLogoutBtn').click(function(){
		$(this).parent().toggleClass('active');
	});
	//Calc text height and show "Read More" button
	function calcTextHeight(){
		$('.calcTextHeight').each(function(i){
			var $parent = $(this);
			if(!$parent.hasClass('workOnMobile') || ($parent.hasClass('workOnMobile') && document.querySelector('html').clientWidth <= 767)){
				var lineHeight = 22;
				if(/[0-9]/.test($parent.css('line-height'))){
					lineHeight = parseInt($parent.css('line-height'));
				}
				var fixHeight = (parseInt($parent.data('rows')) || 5) * lineHeight;
				$parent.css('max-height',fixHeight);
				var dynamicHeight = $parent.find('.calcTextHeightInner').height();
				if(!$parent.hasClass('active')){
					if(dynamicHeight > fixHeight){
						$('.showCalcText').eq(i).addClass('active');
					}else{
						$('.showCalcText').eq(i).removeClass('active');
					}
				}
				//click read more
				$('.showCalcText').eq(i).click(function(){
					$parent.addClass('active');
					$(this).removeClass('active').addClass('hide');
				});
			}
		});
	};
	calcTextHeight();
	window.addEventListener('resize',function(){
		calcTextHeight();
	});
}
