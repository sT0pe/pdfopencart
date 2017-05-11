<?php

class MsHelper extends Model {
	public function getSortParams($sorts, $colMap, $defCol = false, $defWay = false) {
		if (isset($this->request->get['iSortCol_0'])) {
			if (isset($this->request->get['mDataProp_' . $this->request->get['iSortCol_0']])) {
				$sortCol = $this->request->get['mDataProp_' . $this->request->get['iSortCol_0']];
			} else {
				$sortCol = $defCol ? $defCol : $sorts[0];
			}
		} else {
			$sortCol = $defCol ? $defCol : $sorts[0];
		}
		
		if (!in_array($sortCol, $sorts)) {
			$sortCol = $defCol ? $defCol : $sorts[0];
		}
		
		$sortCol = isset($colMap[$sortCol]) ? $colMap[$sortCol] : $sortCol; 
		
		if (isset($this->request->get['sSortDir_0'])) {
			$sortDir = $this->request->get['sSortDir_0'] == 'desc' ? "DESC" : "ASC";
		} else {
			$sortDir = $defWay ? $defWay : "ASC";
		}
		
		return array($sortCol, $sortDir);
	}
	
	public function getFilterParams($filters, $colMap) {
		$filterParams = array();
		for ($col=0; $col < $this->request->get['iColumns']; $col++) {
			if (isset($this->request->get['sSearch_' .$col])) {
				$colName = $this->request->get['mDataProp_' . $col];
				$filterVal = $this->request->get['sSearch_' .$col];
				if (!empty($filterVal) && in_array($colName, $filters)) {
					$colName = isset($colMap[$colName]) ? $colMap[$colName] : $colName;
					$filterParams[$colName] = $this->request->get['sSearch_' .$col];
				}
			}
		}
		
		return $filterParams;
	}	
	
	public function setBreadcrumbs($data) {
		$breadcrumbs = array();
		
		$breadcrumbs[] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', '', 'SSL'),
        	'separator' => false
      	);
		
		foreach ($data as $breadcrumb) {
	      	$breadcrumbs[] = array(
	        	'text'      => $breadcrumb['text'],
				'href'      => $breadcrumb['href'],
	        	'separator' => $this->language->get('text_separator')
	      	);
		}
		
		return $breadcrumbs;
	}
	
	public function admSetBreadcrumbs($data) {
		$breadcrumbs = array();
		
		$breadcrumbs[] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        	'separator' => false
      	);
		
		foreach ($data as $breadcrumb) {
	      	$breadcrumbs[] = array(
	        	'text'      => $breadcrumb['text'],
				'href'      => $breadcrumb['href'] . '&token=' . $this->session->data['token'],
	        	'separator' => $this->language->get('text_separator')
	      	);
		}
		
		return $breadcrumbs;
	}	

	public function loadTemplate($templateName, $children = FALSE) {
		// ugly
		if(strpos($templateName, '/') == false) {
			$templateName = 'multiseller/' . $templateName;
		}
		
		if (file_exists(DIR_TEMPLATE . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . "/template/$templateName.tpl")) {
			$template = MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . "/template/$templateName.tpl";
		} else {
			$template = "default/template/$templateName.tpl";
		}

		if ($children == FALSE) {
			$children = array(
				'column_left' => $this->load->controller('common/column_left'),
				'column_right' => $this->load->controller('common/column_right'),
				'content_top' => $this->load->controller('common/content_top'),
				'content_bottom' => $this->load->controller('common/content_bottom'),
				'footer' => $this->load->controller('common/footer'),
				'header' => $this->load->controller('common/header')
			);
		}

		return array($template, $children);
	}

	public function admLoadTemplate($templateName, $children = FALSE) {
		// ugly
		if(strpos($templateName, '/') !== false)
			$template = "$templateName.tpl";
		else
			$template = "multiseller/$templateName.tpl";
		
		if ($children === FALSE) {
			$children = array(
				'common/footer',
				'common/header'
			);
		}
	
		return array($template, $children);
	}
	
	public function addStyle($style, $rel = 'stylesheet', $media = 'screen') {
		if (file_exists("catalog/view/theme/" . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . "/stylesheet/{$style}.css")) {
			$this->document->addStyle("catalog/view/theme/" . MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . "/stylesheet/{$style}.css", $rel, $media);
		} else {
			$this->document->addStyle("catalog/view/theme/default/stylesheet/{$style}.css", $rel, $media);
		}
	}
	
	public function getLanguageId($code) {
		$res = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '" . $code . "'");
		
		return $res->row['language_id'];
	}

	// @todo Remove from MsHelper
    public function getStockStatuses($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sql .= " ORDER BY name";

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $stock_status_data = $this->cache->get('stock_status.' . (int)$this->config->get('config_language_id'));

            if (!$stock_status_data) {
                $query = $this->db->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

                $stock_status_data = $query->rows;

                $this->cache->set('stock_status.' . (int)$this->config->get('config_language_id'), $stock_status_data);
            }

            return $stock_status_data;
        }
    }

    // @todo Remove from MsHelperg
    public function getTaxClasses($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "tax_class";

            $sql .= " ORDER BY title";

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $tax_class_data = $this->cache->get('tax_class');

            if (!$tax_class_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_class");

                $tax_class_data = $query->rows;

                $this->cache->set('tax_class', $tax_class_data);
            }

            return $tax_class_data;
        }
    }
	
	public function isUnsignedFloat($val) {
		$val=str_replace(" ","",trim($val));
		//return eregi("^([0-9])+([\.|,]([0-9])*)?$", $val);
		return preg_match("/^([0-9])+([\.|,]([0-9])*)?$/", $val);
	}
	
	public function uniformDecimalPoint($number) {
		return (float)(str_replace(array($this->language->get('thousand_point'), $this->language->get('decimal_point')), array('', '.'), $number));
	}

	public function trueCurrencyFormat($number) {
		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();
		$decimal_place = $currencies[$this->config->get('config_currency')]['decimal_place'];
		$decimal_point = $this->language->get('decimal_point');
		$thousand_point = $this->language->get('thousand_point');
		return number_format(round($number, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);
	}
	
	public function isInstalled() {
		$this->load->model('extension/extension');
		$installed_extensions = $this->model_extension_extension->getInstalled('module');
		return array_search('multimerch', $installed_extensions) !== FALSE;
	}

	public function renderPmDialog(&$data) {
		if (isset($this->request->get['product_id'])) {
			$seller_id = $this->MsLoader->MsProduct->getSellerId($this->request->get['product_id']);
			$data['product_id'] = (int)$this->request->get['product_id'];
		} else {
			$seller_id = $this->request->get['seller_id'];
			$data['product_id'] = 0;
		}


		$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
		if (empty($seller)) return false;

		$data['seller_id'] = $seller_id;
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('dialog-sellercontact', 1);
		return $this->load->view($template, $data);
	}

	public function getStatusName($data = array()) {
		$this->load->model('localisation/order_status');

		$order_statuses = $this->model_localisation_order_status->getOrderStatuses(array(
			'language_id' => (isset($data['language_id']) ? $data['language_id'] : $this->config->get('config_language_id'))
		));

		foreach ($order_statuses as $order_status) {
			if ($order_status['order_status_id'] == $data['order_status_id']) {
				return $order_status['name'];
			} else if ((int)$data['order_status_id'] == 0) {
				$this->load->language('multiseller/multiseller');
				return $this->language->get('ms_order_status_initial');
			}
		}

		return '';
	}

	public function isValidUrl($url) {
		return preg_match('|^[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
		//return (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED));
	}

	public function addScheme($url) {
		$scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
	}

	function addHttp($url) {
		if (!preg_match("@^http?://@i", $url) && !preg_match("@^https?://@i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}

	public function slugify($str, $options = array()) {
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

		$defaults = array(
			'delimiter' => '-',
			'limit' => null,
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => $this->config->get('msconf_enable_non_alphanumeric_seo') ? false : true,
		);

		// Merge options
		$options = array_merge($defaults, $options);

		$char_map = array(
			// Latin
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
			'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
			'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
			'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
			'ß' => 'ss',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
			'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
			'ÿ' => 'y',
			// Latin symbols
			'©' => '(c)',
			// Greek
			'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
			'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
			'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
			'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
			'Ϋ' => 'Y',
			'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
			'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
			'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
			'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
			'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
			// Turkish
			'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
			'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
			// Russian
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
			'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
			'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
			'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
			'Я' => 'Ya',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
			'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
			'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
			'я' => 'ya',
			// Ukrainian
			'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
			'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
			// Czech
			'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
			'Ž' => 'Z',
			'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
			'ž' => 'z',
			// Polish
			'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
			'Ż' => 'Z',
			'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
			'ż' => 'z',
			// Latvian
			'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
			'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
			'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
			'š' => 's', 'ū' => 'u', 'ž' => 'z',
			// German
			'Ä' => 'AE', 'Ö' => 'OE', 'Ü' => 'UE',
			'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue'
		);

		// Make custom replacements
		$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

		// Transliterate characters to ASCII
		if ($options['transliterate']) {
			$str = str_replace(array_keys($char_map), $char_map, $str);
		}

		// Replace non-alphanumeric characters with our delimiter
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

		// Remove duplicate delimiters
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

		// Truncate slug to max. characters
		$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

		// Remove delimiter from ends
		$str = trim($str, $options['delimiter']);

		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	}

	public function createOCSetting($data = array(), $store_id = 0) {
		$res = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($data['code']) . "' AND `key` = '" . $this->db->escape($data['key']) . "' AND `store_id` = '" . (int)$store_id . "'");

		if($res->num_rows && isset($res->row['setting_id'])) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "setting
				SET `value` = '" . (!is_array($data['value']) ? $this->db->escape($data['value']) : $this->db->escape(json_encode($data['value']))) . "',
					`serialized` = '" . (!is_array($data['value']) ? 0 : 1) . "'
				WHERE `setting_id` = '" . (int)$res->row['setting_id'] . "'
			");
		} else {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "setting
				SET `store_id` = '" . (int)$store_id . "',
					`code` = '" . $this->db->escape($data['code']) . "',
					`key` = '" . $this->db->escape($data['key']) . "',
					`value` = '" . (!is_array($data['value']) ? $this->db->escape($data['value']) : $this->db->escape(json_encode($data['value']))) . "',
					`serialized` = '" . (!is_array($data['value']) ? 0 : 1) . "'
			");
		}
	}

	public function getIntegrationPackVersion() {
		$version = false;
		$f_name = realpath(__DIR__ . '/../../vqmod/xml/multimerch_c_hooks.xml');

		if(is_file($f_name)) {
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->load($f_name);
			$version_tag = $dom->getElementsByTagName('version')->item(0);
			if($version_tag && $version_tag->textContent) {
				$version = $version_tag->textContent;
			}
		}

		return $version;
	}

	public function adminUrlLink($route, $args = '', $secure = false) {
		$url = '';
		$admin_config_path = realpath(__DIR__ . '/../../admin/config.php');

		if($admin_config_path) {
			$admin_config_file = fopen($admin_config_path, "r") or die("Unable to open " . $admin_config_path);
			$raw_file_content = fread($admin_config_file, filesize($admin_config_path));

			if($raw_file_content) {
				$pattern = '~\'(HTTP_SERVER|HTTPS_SERVER)\', \'(.*)\'~i';
				preg_match_all($pattern, $raw_file_content, $config_urls);

				$url .= (isset($config_urls[2][1]) && $secure ? $config_urls[2][1] : $config_urls[2][0]) . 'index.php?route=' . $route;

				if ($args) {
					$url .= is_array($args) ? '&amp;' . http_build_query($args) : str_replace('&', '&amp;', '&' . ltrim($args, '&'));
				}
			}

			fclose($admin_config_file);
		}

		return $url;
	}

	public function addPpreapprovalkey($preapprovalKey, $customer_id) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_customer_ppakey SET
					preapprovalkey = '" . $this->db->escape($preapprovalKey) . "',
					customer_id = '" . (int)$customer_id . "'
					ON DUPLICATE KEY UPDATE preapprovalkey = '" . $this->db->escape($preapprovalKey) . "'
					");
		return true;
	}

	public function getPpreapprovalkey($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_customer_ppakey WHERE customer_id=".(int)$customer_id);
		return $query->row;
	}

	public function activePpreapprovalkey($customer_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "ms_customer_ppakey SET active = '1' WHERE `customer_id` = '" . (int)$customer_id . "'");
	}

	/**
	 * Turn on / off vqmod modification file
	 *
	 * @param string $xml
	 * @param int $action. 1 - activate, 0 - disable
	 * @return bool
	 */
	public function setVqmod($xml, $action = 1) {
		$dir_vqmod = str_replace('system', 'vqmod/xml', DIR_SYSTEM);
		$on  = $dir_vqmod . $xml;
		$off = $dir_vqmod . $xml . '_';

		if($action) {
			if(file_exists($off) && is_writable($off)) {
				return rename($off, $on);
			} else {
				return 'File `' . $off . '` does not exist or is not writable!';
			}
		} else {
			if(file_exists($on) && is_writable($on)) {
				return rename($on, $off);
			} else {
				return 'File `' . $on . '` does not exist or is not writable!';
			}
		}
	}
}
