<?php

	/**
	 * 	FUNCTIONS
	 *
	 * 	A class to provide global functions used throughout the application
	 */
	class Functions {

		/**
		 *	ESCAPE A STRING
		 */
		public static function escape($string) {
			return htmlentities($string, ENT_QUOTES, 'UTF-8');
		}

		/**
		 *	RENDER THE VIEW: Load a specific header, template and footer based on the Controller and Method name (View)
		 */
		public static function render($controller, $view, $resources = array()) {

			// extract resources into local context (must be an associative array)
			extract($resources);

			// use login header and footer if the view is 'login'
			if ($view === 'login') {

				require APP . 'view/_templates/header_login.php';
				require APP . 'view/' . strtolower($controller) . '/login.php';
				require APP . 'view/_templates/footer_login.php';

			} else {

				// use specific headers and footers for visualisation or data controllers
				switch ($controller) {

					// data controller: use JS for EditableGrid on specifc pages
					case 'Data':
						if ($view === 'index') {
							require APP . 'view/_templates/header_default.php';
							require APP . 'view/' . strtolower($controller) . '/' . strtolower($view) . '.php';
							require APP . 'view/_templates/footer_default.php';
							break;
						} else if ($view === 'createcombos') {
							require APP . 'view/_templates/header_data_combos.php';
							require APP . 'view/' . strtolower($controller) . '/' . strtolower($view) . '.php';
							require APP . 'view/_templates/footer_data_combos.php';
							break;
						} else {
							require APP . 'view/_templates/header_data_grid.php';
							require APP . 'view/' . strtolower($controller) . '/' . strtolower($view) . '.php';
							require APP . 'view/_templates/footer_data_grid.php';
							break;
						}

					// visualisation controller: use JS for D3 and WorkloadCalendarView
					case 'Visualisation':
						require APP . 'view/_templates/header_vis.php';
						require APP . 'view/' . strtolower($controller) . '/' . strtolower($view) . '.php';
						require APP . 'view/_templates/footer_vis.php';
					break;

					// otherwise use the default header and footer
					default:
						require APP . 'view/_templates/header_default.php';
						require APP . 'view/' . strtolower($controller) . '/' . strtolower($view) . '.php';
						require APP . 'view/_templates/footer_default.php';
				}
			}
		}

		/**
		 *	RENDER THE VIEW: Load a specific header, template and footer based on the Controller and Method name (View)
		 */
		public static function getDayRange($startDate, $endDate) {
			$diff = abs(strtotime($endDate) - strtotime($startDate));
			return floor($diff / (60*60*24));
		}

		/**
		 *	RETURN A FORMATTED DATE: Provide a string value of a date: 'YYYY-MM-DD'
		 */
		public static function dateFormat($date) {

			return date('D jS M Y', strtotime($date));

		}
	}
