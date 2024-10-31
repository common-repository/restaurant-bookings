( function( tinymce, $ ) {
	tinymce.PluginManager.add( 'rbktmce', function( editor ) {
		var $business = $("#rbktmce-business"),
			$section = $("#rbktmce-section"),
			$content = $("#rbktmce-content"),
			$booking = $("#rbktmce-booking"),
			$takeaway = $("#rbktmce-takeaway"),
			$delivery = $("#rbktmce-delivery"),
			$allways_mobile = $("#rbktmce-allways-mobile"),
			$for_order = $("#rbktmce-for-order"),
			first_time = true;

		var enable_edit = $business.length > 0;

		$content.find("option").remove();
		$content.find("optGroup").remove();

		if (enable_edit) {
			editor.addCommand( 'rbktmce_edit_cmd', function() {
				rbktmce_edit( editor.id );
			});
		}

		if (enable_edit) {
			// Register rbktmce button
			editor.addButton("rbktmce_button", {
				title : editor.getLang("rbktmce.desc"),
				cmd   : "rbktmce_edit_cmd",
				image : RBKTMCE.URL + "img/button.png"
			});
		}

		function _rbk_get_sc(sc_name, business_id, content_id, booking, takeaway, delivery, allways_mobile, for_order) {
			var sc = "[" + sc_name + " " +
				'id="' + business_id + '" ';

			switch (sc_name) {
				case RBKTMCE.SC_MENU_GROUP:
				case RBKTMCE.SC_CARTE_GROUP:
					sc += 'groupid="' + content_id + '" ';
				break;
				case RBKTMCE.SC_MENU:
					sc += 'menuid="' + content_id + '" ';
				break;
				case RBKTMCE.SC_CARTE:
					sc += 'carteid="' + content_id + '" ';
				break;
				case RBKTMCE.SC_COUPON:
					sc += 'couponid="' + content_id + '" ';
				break;
			}

			if (booking) {
				sc += 'booking="true" ';
			}

			if (takeaway) {
				sc += 'takeaway="true" ';
			}

			if (delivery) {
				sc += 'delivery="true" ';
			}

			if (allways_mobile) {
				sc += 'allways_mobile="true" ';
			}

			if (for_order) {
				sc += 'for_order="true" ';
			}

			sc += '/]';

			return sc;
		}

		function rbktmce_edit(editorId) {
			var ed,
				backdrop = $("#rbktmce-backdrop"),
				wrap = $('#rbktmce-wrap'),
				$body = $( document.body );

			if ( editorId ) {
				window.wpActiveEditor = editorId;
			}

			if ( ! window.wpActiveEditor ) {
				return;
			}

			this.textarea = $( '#' + window.wpActiveEditor ).get( 0 );

			if ( typeof tinymce !== 'undefined' ) {
				$body.append( backdrop, wrap );

				$("#rbktmce-close").add( backdrop ).add( '#rbktmce-cancel a' ).click( function( event ) {
					event.preventDefault();

					$body.removeClass( 'modal-open' );
					backdrop.hide();
					wrap.hide();
				});

				ed = tinymce.get( wpActiveEditor );

				if ( ed && ! ed.isHidden() ) {
					editor = ed;
				} else {
					editor = null;
				}

				if ( editor && tinymce.isIE ) {
					editor.windowManager.bookmark = editor.selection.getBookmark();
				}
			}

			if (first_time) {
				$business.change(function (e) {
					var slug = $business.val();
					var hide_values = [];
					var $selected_option = $business.find("option[value=" + slug + "]");

					$("#rbktmce-section option").show();

					if ($selected_option.data("booking") != true) {
						hide_values.push(RBKTMCE.SC_BOOKING_FORM);
					}

					if ($selected_option.data("delivery") != true && $selected_option.data("takeaway") != true && $selected_option.data("booking") != true) {
						hide_values.push(RBKTMCE.SC_ORDER_FORM);
						hide_values.push(RBKTMCE.SC_ORDER_CATALOG_FORM);
						hide_values.push(RBKTMCE.SC_ORDER_ALL);
					}

					if ($selected_option.data("contact") != true) {
						hide_values.push(RBKTMCE.SC_BOOKING_FORM);
						hide_values.push(RBKTMCE.SC_CONTACT_FORM);
						hide_values.push(RBKTMCE.SC_GROUP_FORM);
						hide_values.push(RBKTMCE.SC_REVIEW_FORM);
					}

					if ($selected_option.data("opening") != true) {
						hide_values.push(RBKTMCE.SC_OPENING);
					}

					if ($selected_option.data("map") != true) {
						hide_values.push(RBKTMCE.SC_MAP);
					}

					if ($selected_option.data("cartes") == 0) {
						hide_values.push(RBKTMCE.SC_CARTE);
						hide_values.push(RBKTMCE.SC_CARTE_GROUP);
						hide_values.push(RBKTMCE.SC_CARTE_ALL);
					}

					if ($selected_option.data("menus") == 0) {
						hide_values.push(RBKTMCE.SC_MENU);
						hide_values.push(RBKTMCE.SC_MENU_GROUP);
						hide_values.push(RBKTMCE.SC_MENU_ALL);
						hide_values.push(RBKTMCE.SC_MENU_BOOKING);
					}

					if (hide_values.length > 0) {
						$("#rbktmce-section option[value=" + hide_values.join("], #rbktmce-section option[value=") + "]").hide();
					}

					if ($("#rbktmce-section option[value=" + $section.val() + "]").css("display") == "none") {
						$("#rbktmce-section option").each(function (el) {
							if ($(this).css("display") != "none") {
								$section.val($(this).val());
								return false;
							}
						});
					}

					// Por defecto deschequeamos el takeaway y el delivery
					$booking.prop( "checked", false );
					$takeaway.prop( "checked", false );
					$delivery.prop( "checked", false );
					$allways_mobile.prop( "checked", false );
					$for_order.prop( "checked", false );

					$section.trigger("change");
				});

				$section.change(function (e) {
					var current_business = $business.val();
					var $wrap_content = $("#wrap_rbktmce_content");
					var current_section = $section.val();
					$wrap_content.hide();

					switch (current_section) {
						case RBKTMCE.SC_CARTE:
						case RBKTMCE.SC_CARTE_GROUP:
						case RBKTMCE.SC_CARTE_ALL:
						case RBKTMCE.SC_MENU:
						case RBKTMCE.SC_MENU_GROUP:
						case RBKTMCE.SC_MENU_ALL:
							var $selected_option = $business.find("option[value=" + current_business + "]");

							if ($selected_option.data("booking") == true || $selected_option.data("takeaway") == true || $selected_option.data("delivery") == true) {
								$(".field-group-for-order").show();
							}
						break;
						case RBKTMCE.SC_ORDER_CART:
						case RBKTMCE.SC_ORDER_CATALOG_FORM:
							var $selected_option = $business.find("option[value=" + current_business + "]");

							if ($selected_option.data("booking") == true) {
								$booking.prop( "checked", true );
								$(".field-group-booking").show();
							} else {
								$booking.prop( "checked", false );
								$(".field-group-booking").hide();
							}

							if ($selected_option.data("takeaway") == true) {
								$takeaway.prop( "checked", true );
								$(".field-group-takeaway").show();
							} else {
								$takeaway.prop( "checked", false );
								$(".field-group-takeaway").hide();
							}

							if ($selected_option.data("delivery") == true) {
								$delivery.prop( "checked", true );
								$(".field-group-delivery").show();
							} else {
								$delivery.prop( "checked", false );
								$(".field-group-delivery").hide();
							}

							$allways_mobile.prop( "checked", true );
							$(".field-group-allways-mobile").show();
						break;
						default:
							// Por defecto en cualquier otro cambio lo ponemos como deschequeado y lo ocultamos
							$booking.prop( "checked", false );
							$takeaway.prop( "checked", false );
							$delivery.prop( "checked", false );
							$allways_mobile.prop( "checked", false );
							$for_order.prop( "checked", false );

							$(".field-group-booking").hide();
							$(".field-group-takeaway").hide();
							$(".field-group-delivery").hide();
							$(".field-group-allways-mobile").hide();
							$(".field-group-for-order").hide();
						break;
					}

					var ajax_action = "";

					$content.find("option").remove();
					$content.find("optGroup").remove();

					switch (current_section) {
						case RBKTMCE.SC_MENU_GROUP:
							ajax_action = "ae-get-menu-groups";
						break;
						case RBKTMCE.SC_CARTE_GROUP:
							ajax_action = "ae-get-carte-groups";
						break;
						case RBKTMCE.SC_MENU:
							ajax_action = "ae-get-menus";
						break;
						case RBKTMCE.SC_CARTE:
							ajax_action = "ae-get-cartes";
						break;
						case RBKTMCE.SC_COUPON:
							ajax_action = "ae-get-coupons";
						break;
					}

					if (ajax_action != "") {
						var jqxhr = $.getJSON( RBKTMCE.AJAX_URL + "?action=" + ajax_action + "&term=" + current_business, function(data) {
							switch (current_section) {
								case RBKTMCE.SC_CARTE_GROUP:
									$(data).each(function (i, carte) {
										var $carte = $('<optGroup/>').attr("label", carte.value);
										$(carte.groups).each(function (j, group) {
											$carte.append($('<option>', {
												value: group.id,
												text: group.value
											}));
										});

										$content.append($carte);
									});
								break;
								case RBKTMCE.SC_MENU:
									$(data).each(function (i, group) {
										var $group = $('<optGroup/>').attr("label", group.value);
										$(group.items).each(function (j, item) {
											$group.append($('<option>', {
												value: item.id,
												text: item.value
											}));
										});

										$content.append($group);
									});
								break;
								case RBKTMCE.SC_CARTE:
								case RBKTMCE.SC_MENU_GROUP:
								case RBKTMCE.SC_COUPON:
									$(data).each(function (i, item) {
										$content.append($('<option>', {
											value: item.id,
											text: item.value
										}));
									});
								break;
							}

							if ($content.find("option").length > 0) {
								$wrap_content.show();
							}
						}).fail(function() {
							alert(RBKTMCE.ERROR_GET_CONTENT);
						});
					}
				});

				$("#rbktmce-update .button").click(function (e) {
					e.preventDefault();
					var shortcode_name = $section.val();
					var business_id = $business.val();
					var content_id = $content.val();
					var booking = $booking.is(':checked');
					var takeaway = $takeaway.is(':checked');
					var delivery = $delivery.is(':checked');
					var allways_mobile = $allways_mobile.is(':checked');
					var for_order = $for_order.is(':checked');

					var sc = _rbk_get_sc(
						shortcode_name,
						business_id,
						content_id,
						booking,
						takeaway,
						delivery,
						allways_mobile,
						for_order
					);

					tinyMCE.execCommand('mceInsertContent',false, sc + "\n\n");

					if ( editor && !editor.isHidden() ) {
						editor.execCommand('mceRepaint');
					}

					$body.removeClass( 'modal-open' );
					backdrop.hide();
					wrap.hide();
				});

				$("#rbktmce-cancel a").click(function (e) {
					e.preventDefault();
				});

				first_time = false;
			}

			$body.addClass( 'modal-open' );

			wrap.show();

			backdrop.show();
		}
	} );
} )( window.tinymce, jQuery );
