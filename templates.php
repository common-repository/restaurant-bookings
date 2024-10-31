<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

require_once "inc/ae-api/utils.php";

class RBKTemplateTags {
	private static $instance = null;
	
	public static function get_instance() {
		if (self::$instance == null) {
			self::$instance = new RBKTemplateTags();
		}
	
		return self::$instance;
	}
	
	/**
	 * Pinta bonito un array de EasyRange
	 *
	 * @param \Listae\Client\Model\EasyRange[] $easy_ranges
	 * @param array $args {
	 * 		@type string $wrap_start, apertura del contenedor de
	 * 			lo que se imprima
	 * 		@type string $wrap_end, cierre del contenedor anterior
	 * 		@type string $range_start, apertura del contenedor de
	 * 			cada uno de los rangos que se imprimen
	 * 		@type $string $range_end, cierre del contenedor anterior
	 * }
	 */
	public function print_easy_ranges($easy_ranges, $args=array()) {
		echo $this->get_html_easy_ranges($easy_ranges, $args);
	}
	
	/**
	 * devuelve un html bonito a partir de un array de EasyRange
	 *
	 * @param \Listae\Client\Model\EasyRange[] $easy_ranges
	 * @param array $args {
	 * 		@type string $wrap_start, apertura del contenedor de
	 * 			lo que se imprima
	 * 		@type string $wrap_end, cierre del contenedor anterior
	 * 		@type string $range_start, apertura del contenedor de
	 * 			cada uno de los rangos que se imprimen
	 * 		@type $string $range_end, cierre del contenedor anterior
	 * }
	 */
	public function get_html_easy_ranges($easy_ranges, $args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_tt_print_easy_ranges_defaults", array(
				"wrap_start" => '<ul class="calendar-list">',
				"wrap_end" => '</ul>',
				"range_start" => '<li>',
				"range_end" => '</li>'
		)) );
		
		$s = $args["wrap_start"];
		foreach ( $easy_ranges as $i => $er) {
			if (empty($er)) {continue;}
			$s .= $args["range_start"];
			if ($er->getWeekdays()) {
				$weekdays = "";
				foreach ($er->getWeekdays()->getWeekday() as $wd_index => $wd) {
					if ($wd_index > 0 && $wd_index != (count($er->getWeekdays()->getWeekday()) - 1)) {
						$weekdays .= ", ";
					} elseif ($wd_index > 0 && $wd_index == (count($er->getWeekdays()->getWeekday()) - 1)) {
						$weekdays .=" &amp; ";
					}
						
					$weekdays .= date_i18n("l", strtotime($wd->getName()));
				}
				$s .= sprintf(_x("The %s.", "Dias de la semana", 'restaurant-bookings'), $weekdays);
			}
				
			if ($er->getMonths()) {
				$months = "";
				foreach ($er->getMonths()->getMonth() as $m_index => $m) {
					if ($m_index > 0 && $m_index != (count($er->getMonths()->getMonth()) - 1)) {
						$months .= ", ";
					} elseif ($m_index > 0 && $m_index == (count($er->getMonths()->getMonth()) - 1)) {
						$months .=" &amp; ";
					}
						
					$months .= date_i18n("F", strtotime($m->getName()));
				}
				$s .= sprintf(_x("On %s. ", "Meses", 'restaurant-bookings'), $months);
			}
				
			if ($er->getFrom() && $er->getFrom() == $er->getTo()) {
				$date = date_i18n(_x("F j\\t\\h \\o\\f Y", "Formato de fecha largo", 'restaurant-bookings'), $er->getFrom()->getTimestamp());
	
				$s .= sprintf(_x("The %s. ", "Fecha larga exacta", 'restaurant-bookings'), $date);
			} elseif ($er->getFrom() && $er->getTo()) {
				$date_from = date_i18n(_x("F j\\t\\h \\o\\f Y", "Formato de fecha largo", 'restaurant-bookings'), $er->getFrom()->getTimestamp());
				$date_to = date_i18n(_x("F j\\t\\h \\o\\f Y", "Formato de fecha largo", 'restaurant-bookings'), $er->getTo()->getTimestamp());
	
				$s .= sprintf(_x('From %1$s until %2$s. ', "Rango entre fechas largas", 'restaurant-bookings'), $date_from, $date_to);
			} elseif ($er->getFrom()) {
				$date = date_i18n(_x("F j\\t\\h \\o\\f Y", "Formato de fecha largo", 'restaurant-bookings'), $er->getFrom()->getTimestamp());
	
				$s .= sprintf(_x('From %s. ', "Rango entre fechas largas", 'restaurant-bookings'), $date);
			} elseif ($er->getTo()) {
				$date = date_i18n(_x("F j\\t\\h \\o\\f Y", "Formato de fecha largo", 'restaurant-bookings'), $er->getTo()->getTimestamp());
	
				$s .= sprintf(_x('Until %s. ', "Rango entre fechas largas", 'restaurant-bookings'), $date);
			}
				
			if ($er->getTimeRanges()) {
				foreach ($er->getTimeRanges()->getTimeRange() as $tr) {
					if ($tr->getFrom() && $tr->getTo()) {
						if ($tr->getFrom() == $tr->getTo()) {
							$s .= sprintf(_x('At %1$s. ', "Rango horas", 'restaurant-bookings'), $tr->getFrom(), $tr->getTo());
						} else {
							$s .= sprintf(_x('From %1$s to %2$s. ', "Rango horas", 'restaurant-bookings'), $tr->getFrom(), $tr->getTo());
						}
					} elseif ($tr->getFrom()) {
						$s .= sprintf(_x('From %s. ', "Rango horas", 'restaurant-bookings'), $tr->getFrom());
					} elseif ($tr->getTo()) {
						$s .= sprintf(_x('To %s. ', "Rango horas", 'restaurant-bookings'), $tr->getTo());
					}
				}
			}
				
			if (!empty($er->getDescription())) {
				$s .= "<span>" . esc_html( AEI18n::__($er->getDescription()) ) . "</span>";
			}
			$s .= $args["range_end"];
		}
		$s .= $args["wrap_end"];
		
		return $s;
	}

	private function get_item_reviewed_meta($restaurant) {
		$s = '<div itemprop="itemReviewed" itemscope itemtype="https://schema.org/Restaurant" style="display: none;">';
		
		if (!empty($restaurant->getLogo())) {
			$s .= '<img itemprop="image" src="' . $restaurant->getLogo()->getThumbnail() . '"/>';
		}
		$s .= '<span itemprop="name">' . esc_html($restaurant->getName()) . '</span>';
		$ncat_food = 1;
		foreach ($restaurant->getCategories()->getCategory() as $cat) {
			if ( preg_match('/^food-type./', $cat->getIdentifier()) && $ncat_food <= 5 ) {
				$s .= '<span itemprop="servesCuisine">';
				$s .= esc_html(AEI18n::__($cat->getTitle()));
				$s .= '</span>';
				$ncat_food++;
			}
		}
		
		$phone = $restaurant->getPhone();
		
		if ($phone) {
			$s .= '<span itemprop="telephone">' . esc_html($phone->getInternationalFormat()) . '</span>';
		}
		
		$address = $restaurant->getMainContact()->getAddress();
		$s .= '<span itemprop="address">';
		$s .= esc_html($address->getAddress1());
		if (!empty($address->getAddress2())) {
			$s .= ', ' . esc_html($address->getAddress2());
		}
		$s .= '. ';
		if (!empty($address->getPostalCode())) {
			$s .= esc_html($address->getPostalCode()) . ' ';
		}
		$s .= $address->getTown();
		$s .= '</span>';
		$s .= '</div>';
		
		return $s;
	}
	
	/**
	 * @param \Listae\Client\Model\RatingStat $rating
	 * @param string $info_label
	 * @param string $link
	 * @return mixed
	 */
	public function get_html_rating_aggregate($rating, $restaurant, $info_label, $link="" ){
		$total_count = $rating->getCount();
		$average = $rating->getAverage();
		$average_f = number_format_i18n( $average, 1);
	
		$s = '<div class="biz-rating" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">';
		
		if (!empty($restaurant)) {
			$s .= $this->get_item_reviewed_meta($restaurant);
		}
		
		$s .= '<span class="info-label">';
		$s .= esc_html( $info_label );
		$s .= '</span>';
		$s .= '<span class="star-holder">';
		$s .= '<span class="star-rating" itemprop="ratingValue" ';
		$s .= 'content="' . esc_attr($average_f) . '" ';
		$s .= 'title="' . esc_attr(sprintf(__("%1s stars of 5", 'restaurant-bookings'), $average_f)) . '" ';
		$s .= 'style="width: ' . ((100 * $average) / 5) . '%;"></span>';
		$s .= '</span>';
		$s .= '<span class="value-label">';
		$s .= $average_f . '/5';
		$s .= '</span>';
	
		if (!empty($link)) {
			$s .= '<a href="' . esc_attr($link) . '">';
		}
	
		$s .= '<span class="star-label">';
		$s .= '<span>'. __('Base on ','restaurant-bookings') .'</span> <span itemprop="ratingCount">'. $total_count .'</span> ';
		$s .= _n('review', 'reviews', $total_count, 'restaurant-bookings');
		$s .= '</span>';
	
		if (!empty($link)) {
			$s .= '</a>';
		}
		$s .= '</div>';
	
		return apply_filters("ae_get_html_rating_aggregate", $s, $average, $total_count, $info_label);
	}
	
	/**
	 * @param \Listae\Client\Model\RatingStat $rating
	 * @param string $info_label
	 * @return mixed
	 */
	public function get_html_rating_aggregate_no_meta($rating, $info_label) {
		$average = $rating->getAverage();
		$average_f = number_format_i18n( $average, 1);
	
		$s = '<div class="biz-rating">';
		$s .= '<span class="info-label">';
		$s .= esc_html( $info_label );
		$s .= '</span>';
		$s .= '<span class="star-holder">';
		$s .= '<span class="star-rating" ';
		$s .= 'title="' . esc_attr(sprintf(__("%1s stars of 5", 'restaurant-bookings'), $average_f)) . '" ';
		$s .= 'style="width: ' . ((100 * $average) / 5) . '%;"></span>';
		$s .= '</span>';
		$s .= '<span class="value-label">';
		$s .= $average_f . '/5';
		$s .= '</span>';
		$s .= '</div>';
	
		return apply_filters("ae_get_html_rating_aggregate_no_meta", $s, $average, $info_label);
	}
	
	/**
	 * @param int $rating_value
	 * @param string $info_label
	 * @return mixed
	 */
	public function get_html_rating($rating_value, $info_label, $css_class="general") {
		$rating_value = intval($rating_value);
	
		$s = '<span class="star-wrap rating-' . esc_attr($css_class) . '" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">';
		$s .= '<span class="info-label">';
		$s .= esc_html( $info_label );
		$s .= '</span>';
		$s .= '<span class="star-holder">';
		$s .= '<span class="star-rating" itemprop="ratingValue" content="' . $rating_value . '"';
		$s .= 'title="' . esc_attr(sprintf(__("%1s stars of 5", 'restaurant-bookings'), $rating_value)) . '" ';
		$s .= 'style="width: ' . ((100 * $rating_value) / 5) . '%;"><span class="label-value"> ' . esc_html($rating_value) . ' / 5</span></span>';
		$s .= '</span>';
		$s .= '</span>';
	
		return apply_filters("ae_get_html_rating", $s, $rating_value, $info_label, $css_class);
	}
	
	/**
	 * @param int $ratingValue
	 * @param string $info_label
	 * @return mixed
	 */
	public function get_html_rating_no_meta($rating_value, $info_label, $css_class="general") {
		$rating_value = intval($rating_value);
	
		$s = '<span class="star-wrap rating-' . esc_attr($css_class) . '">';
		$s .= '<span class="info-label">';
		$s .= esc_html( $info_label );
		$s .= '</span>';
		$s .= '<span class="star-holder">';
		$s .= '<span class="star-rating" ';
		$s .= 'title="' . esc_attr(sprintf(__("%1s stars of 5", 'restaurant-bookings'), $rating_value)) . '" ';
		$s .= 'style="width: ' . ((100 * $rating_value) / 5) . '%;"><span class="label-value"> ' . esc_html($rating_value) . ' / 5</span></span>';
		$s .= '</span>';
		$s .= '</span>';
	
		return apply_filters("ae_get_html_rating_no_meta", $s, $rating_value, $info_label);
	}
	
	/**
	 * @param \Listae\Client\Model\Review $review
	 * @param \Listae\Client\Model\Restaurant $restaurant
	 * @param string $info_label
	 * @return mixed
	 */
	public function get_html_review($review, $restaurant) {
		$business_name = empty($restaurant) || empty($restaurant->getName()) ? get_bloginfo("name") : $restaurant->getName();
		
		
		$s = '<article class="ae-review ae-review-' . $review->getOrderType() . '" itemprop="review" itemscope itemtype="http://schema.org/Review" class="review review-from' . ($review->getFromBooking() ? 'booking' : 'web') . '">';
		
		if (!empty($restaurant) && is_object($restaurant)) {
			$s .= $this->get_item_reviewed_meta($restaurant);
		} else {
			$s .= '<meta itemprop="itemReviewed" content="' . esc_attr($business_name) . '"/>';
		}
		
		$display_name = $review->getDisplayName();

		$s .= '<span itemprop="author" itemscope itemtype="https://schema.org/Person">';
		$s .= '<meta itemprop="name" content="' . esc_attr(empty($display_name) ? "John Doe" : $display_name) . '"/>';
		$s .= '</span>';
		$s .= '<header>';
		
		$subject = $review->getSubject();
		
		if ( !empty($subject) ) {
			$s .= '<h2 itemprop="name">' . esc_html($subject) . '</h2>';
		}
	
		$s .= '<span class="review-meta">';
	
		$review_date_text = esc_attr(date_i18n(get_option('date_format'), ($review->getCreated()->getTimestamp())));
		$s .= '<meta itemprop="datePublished" content="' . esc_attr(date("c", $review->getCreated()->getTimestamp())) . '">';
		$s .= '<span class="posted-on"><span class="on-text">' . __( 'Review of ', 'restaurant-bookings' ) . '</span>' . $review_date_text . '</span>';
		
		
		if ( !empty($display_name) ) {
			$s .= '<span class="byline"><span class="bylineby"> '. __('by', 'restaurant-bookings'). ' </span>' . esc_html($display_name) . '</span>';
		}
		$s .= '</span>';
		
		$s .= '</header>';
	
		$s .= '<div class="review-content clear">';
		$s .= '<ul class="review-rating">';
		$s .= '<li>';
		$s .= self::get_instance()->get_html_rating($review->getStars(), __("General", 'restaurant-bookings'));
		$s .= '</li>';
	
		if ($review->getStarsService() != null) {
			$s .= '<li>';
			$s .= self::get_instance()->get_html_rating_no_meta($review->getStarsService(), __("Service", 'restaurant-bookings'), "service");
			$s .= '</li>';
			$s .= '<li>';
			$s .= self::get_instance()->get_html_rating_no_meta($review->getStarsFood(), __("Kitchen", 'restaurant-bookings'), "food");
			$s .= '</li>';
			$s .= '<li>';
			
			$local_label = __("Decoration", 'restaurant-bookings');
			
			if ($review->getOrderType() == "delivery") {
			$local_label = _x("Delivery", 'Tipo de pedido en valoracion de opiniones', 'restaurant-bookings');
			} else if ($review->getOrderType() == "takeaway") {
			$local_label = _x("Takeaway", 'Tipo de pedido en valoracion de opiniones', 'restaurant-bookings');
			}
			$s .= self::get_instance()->get_html_rating_no_meta($review->getStarsLocal(), $local_label, "local");
			$s .= '</li>';
		}
	
		$s .= '</ul>';
		
		$body = $review->getBody();
		if (!empty($body)) {
			$s .= '<div class="review-body" itemprop="reviewBody">';
			$s .= esc_html($body);
			$s .= '</div>';
		}
	
		$s .= '</div>';
	
		$s .= '<footer>';
		$s .= '<span class="review-origin">';
		if ($review->getFromBooking()) {
			$s .= '<span class="ae-verified tip-link" title="' . esc_attr(__("Review from an effective reservation whose editing or moderation is not allowed",'restaurant-bookings')) . '">';
			$s .= esc_html(__('Review verify', 'restaurant-bookings'));
			$s .= '</span>';
		} else if (substr($review->getOrigin(), 0, 7) == "http://" || substr($review->getOrigin(), 0, 8) == "https://") {
			$s .= sprintf(
				esc_html(__('Review from %s.', 'restaurant-bookings')) ,
				'<a target="_blank" href="' . esc_attr(AEUrl::get_host_url($review->getOrigin())) . '">' .
				esc_html(AEUrl::get_domain_name($review->getOrigin())) .
				'</a>'
			);
		} else if (substr($review->getOrigin(), 0, 5) == "ae://") {
			$s .= '<a target="_blank" href="https://apps.apple.com/us/app/academia-vasca-gastronomia/id1065481324?l=es&ls=1">' .
			esc_html__("From AVDG/Gastrovasca App", 'restaurant-bookings') .
			'</a>';
		}
		$s .= '</span>';
		
		$reason = $review->getReason();
		if (!empty($reason)) {
			$s .= '<span class="review-ocasion">';
			$s .= esc_html(AEI18n::get_review_reason_desc($reason));
			$s .= '</span>';
		}
			
		$s .= '</footer>';
		$s .= '</article>';
		
		$reply = $review->getReply();
		if ( !empty($reply) ) {
			$s .= '<article class="ae-review-reply">';
			$s .= '<footer>';
			$s .= '<h3>' . esc_html(sprintf(__("Answer by %s", 'restaurant-bookings'), $business_name ) ) . '</h3>';
			$s .= '</footer>';
			$s .= '<div class="review-reply-body">';
			$s .= esc_html($reply);
			$s .= '</div>';
			$s .= '</article>';
		}
	
		return $s;
	}
	
	/**
	 * Rellena un array con indices de los dias de la semana
	 * y los turnos de los mismos para la AgendaBase que
	 * le pasamos como parametro
	 *
	 * @param \Listae\Client\Model\AgendaBase $agenda
	 * @return \Listae\Client\Model\TurnDay[][]
	 */
	private static function turns_to_array_by_weekday($agenda) {
		$weekdays = array();
		
		foreach ($agenda->getTurns()->getTurn() as $i => $turn) {
			foreach ($turn->getDay() as $turnDay) {
				if (!isset($weekdays[$turnDay->getName()])) {
					$weekdays[$turnDay->getName()] = array();
				}
					
				$turn_idx = $turn->getType();
				if (empty($turn_idx)) {
					$turn_idx = $i;
				}
					
				$weekdays[$turnDay->getName()][$turn_idx] = $turnDay;
			}
		}
	
		return $weekdays;
	}
	
	/**
	 * 
	 * @param string[]object[] $agenda_cfg {
	 *	 	"prefix"	   => prefijo que identifica esta cfg del resto
			"title"		   => titulo
			"agenda" 	   => Listae\Client\Model\AgendaBase, con los datos de la agenda
			// TODO: show_closed creo que no se usa y se podria eliminar
			"show_closed"  => boolean que indica si se muestran los cierres o no
			"min_time_in_advance" => int, tiempo minimo para la gestion del servicio expresado en minutos
	 * }
	 * @param boolean $show_title
	 * @return string
	 */
	public function get_agenda_html($agenda_cfg, $show_title=true) {
		$s = "";
		
		if (isset($agenda_cfg["min_time_in_advance"])) {
			$min_time_in_advance = intval($agenda_cfg["min_time_in_advance"]);
			
			if ($min_time_in_advance > 0) {
				$msg_min_time_in_advance = "";
				
				if ($min_time_in_advance < 60) {
					$msg_min_time_in_advance = sprintf(__("%s minutes", 'restaurant-bookings'), $min_time_in_advance);
				} elseif ($min_time_in_advance > 119) {
					$msg_min_time_in_advance = sprintf(__("%s hours", 'restaurant-bookings'), intval($min_time_in_advance / 60));
				} else {
					$msg_min_time_in_advance = __("One hour", 'restaurant-bookings');
				}
				
				$s .= '<div class="alert alert-info">' . sprintf(__('Minimum advance: %s', 'restaurant-bookings'), '<strong>' . esc_html($msg_min_time_in_advance) . '</strong>') . '</div>';
			}
			
		}
		
		$s .= '<div class="biz-agenda agenda-' . esc_attr($agenda_cfg["prefix"]) . '">';
		
		if ($show_title) {
			$s .= '<h3>' . esc_html( $agenda_cfg["title"] ) . '</h3>';
		}
		
		if (!empty($agenda_cfg["agenda"]->getDescription())) {
			$s .= '<div class="agenda-description">' . AEI18n::__( $agenda_cfg["agenda"]->getDescription() ) . '</div>';
		}

		if ($agenda_cfg["agenda"]->getTurns() != null) {
			$s .= '<div class="biz-opening">';
			$s .= RBKTemplateTags::get_instance()->get_turns_html($agenda_cfg["agenda"], _x("l:", "titulo para  los dias de la semana en el shortcode de horario", 'restaurant-bookings'));
			$s .= '</div>';
		}

		if ( $agenda_cfg["agenda"]->getClosures() != null ) {
			$s .= '<div class="agenda-closures">';
			$s .= '<h4 class="info-label">' . __('Special closures', 'restaurant-bookings') . '</h4>';
			$s .= RBKTemplateTags::get_instance()->get_html_easy_ranges($agenda_cfg["agenda"]->getClosures()->getClosure());
			$s .= '</div>';
		}

		if ( $agenda_cfg["agenda"]->getOpenings() != null ) {
			$s .= '<div class="agenda-opening">';
			$s .= '<h4 class="info-label">' . __('Special openings', 'restaurant-bookings') . '</h4>';
			$s .= RBKTemplateTags::get_instance()->get_html_easy_ranges($agenda_cfg["agenda"]->getOpenings()->getOpening());
			$s .= '</div>';
		}
		$s .= '</div><!-- agenda-' . esc_html($agenda_cfg["prefix"]) . ' -->';
		
		return $s;
	}
	
	/**
	 * Pinta la lista de los turnos de una AgendaBase
	 *
	 * @param \Listae\Client\Model\AgendaBase $agenda, nombre en ingles del dia de la semana
	 * @param string $title, titulo a mostrar en los turnos, se le aplica date_i18n( $title, strtotime( $week_day_name ) )
	**/
	public function get_turns_html($agenda, $title) {
		$turns_by_weekday = self::turns_to_array_by_weekday($agenda);
			
		$html_turns = '<ul>';
		$now = time();
		$today_day = AEI18n::get_week_day_name($now);
		
		foreach (AEI18n::get_week_days_name() as $week_day_name) {
			$html_turns .= $this->get_turn_html($week_day_name, 
				date_i18n( $title, strtotime( $week_day_name ) ), 
				$turns_by_weekday[$week_day_name], 
				($today_day == $week_day_name)
			);
		}
			
		$html_turns .= '</ul>';
	
		return $html_turns;
	}
	
	/**
	 *
	 * @param string $week_day_name, nombre en ingles del dia de la semana
	 * @param string $title, titulo a mostrar antes del turno
	 * @param \Listae\Client\Model\TurnDay[] $turns
	 * @param boolean $today
	 */
	private function get_turn_html($week_day_name, $title, $turns, $today=false) {
		$closed_count = 0;
		foreach ($turns as $turn) {
			if ($turn->getClosed()) {
				$closed_count++;
			}
		}
		
		$css_class = "weekday";
		
		if($today) {
			$css_class .= ' today';
			$title = '<span>' . __('Today' , 'restaurant-bookings') . ' </span><span>' . esc_html($title) . '</span>' ;
		} else {
			$title = esc_html($title);
		}
		
		$all_day_closed = $closed_count == count($turns);
			
		$s = '<li class="' . esc_attr($css_class) . ' ' . esc_attr($week_day_name) . ($all_day_closed ? " closed" : "") . '">';
		
		$s .= '<span class="label">' . $title . "</span> ";
	
			
		if ($all_day_closed) {
			$s .= '<span class="info closed">' . __('Closed' , 'restaurant-bookings') . '</span>';
		} else {
			$turns_values = array();
			foreach ($turns as $turn) {
				if (!$turn->getClosed()) {
					$s .= '<meta itemprop="openingHours" content="' . date("D", strtotime($week_day_name)) . ' ' . $turn->getFrom() . '-' . $turn->getTo() . '">';
					
					$turns_values[] = '<span class="turn">' .
						sprintf(
							_x('%1$s - %2$s h.', 'Rango entre horas desde : %1$s hasta : %2$s', 'restaurant-bookings'),
							$turn->getFrom(), $turn->getTo()
						) .
						'</span>';
				}
			}
			$s .= '<span class="info">' . implode('<span class="sep"></span>', $turns_values) . '</span>';
		}
		$s .= '</li>';
	
		return $s;
	}
}
RBKTemplateTags::get_instance();

