<?php

namespace Catalyst\Integrations;

use \Catalyst\Database\{Column, OrderByClause, SelectQuery, Tables};

/**
 * Represents social-media related things
 */
class SocialMedia {
	/**
	 * All the metadata for the social chips, filled by getMeta
	 * @var array|null
	 */
	protected static $meta = null;

	// DEPRECATED
	public static function getArtistDisplayFromDatabase(\Catalyst\Artist\Artist $artist) : array {
		$stmt = $GLOBALS["dbh"]->prepare("SELECT `ID`,`NETWORK`,`SERVICE_URL`,`DISP_NAME` FROM `".DB_TABLES["artist_social_media"]."` WHERE `ARTIST_ID` = :ARTIST_ID ORDER BY `SORT` ASC;");
		$id = $artist->getId();
		$stmt->bindParam(":ARTIST_ID", $id);
		$stmt->execute();

		$result = $stmt->fetchAll();

		$stmt->closeCursor();

		return $result;
	}

	/**
	 * Get meta information
	 * 
	 * @return array Associative array of response from database
	 */
	public static function getMeta() : array {
		if (!is_null(self::$meta)) {
			return self::$meta;
		}

		$stmt = new SelectQuery();

		$stmt->setTable(Tables::INTEGRATIONS_META);
		
		$stmt->addColumn(new Column("VISIBLE", Tables::INTEGRATIONS_META));
		$stmt->addColumn(new Column("INTEGRATION_NAME", Tables::INTEGRATIONS_META));
		$stmt->addColumn(new Column("IMAGE_PATH", Tables::INTEGRATIONS_META));
		$stmt->addColumn(new Column("DEFAULT_HUMAN_NAME", Tables::INTEGRATIONS_META));
		$stmt->addColumn(new Column("CHIP_CLASSES", Tables::INTEGRATIONS_META));

		$orderClause = new OrderByClause(new Column("SORT_ORDER", "ASC"));
		$stmt->addAdditionalCapability($orderClause);

		$stmt->execute();

		self::$meta = $stmt->getResult();

		return self::$meta;
	}

	/**
	 * Get a properly-structured array from a given set of chips
	 * 
	 * [
	 * id => int (0 by default),
	 * src => string|null
	 * label => string
	 * href => string
	 * classes => string (from Meta)
	 * tooltip => string (from Meta)
	 * ]
	 * 
	 * @return array
	 */
	public static function getChipArray(array $rows) : array {
		$result = [];

		$meta = self::getMeta();

		foreach ($rows as $row) {
			$result[] = [
				"id" => array_key_exists("ID", $row) ? $row["ID"] : 0,
				"src" => $meta[$row["NETWORK"]][0],
				"label" => $row["DISP_NAME"],
				"href" => $row["SERVICE_URL"],
				"classes" => $meta[$row["NETWORK"]][2],
				"tooltip" => $meta[$row["NETWORK"]][1]
			];
		}

		return $result;
	}

	/**
	 * Get the HTML for a given chip item (as generated by getChipArray)
	 * 
	 * @param array $chips
	 * @param bool $showClearButton If the "close" button should be added
	 */
	public static function getChipHtml(array $chips, bool $showClearButton=false) : string {
		$str = '';
		$str .= '<div';
		$str .= ' class="center-on-small-only"';
		$str .= '>';

		foreach ($chips as $chip) {
			// we wrap the chip with a link
			if (!is_null($chip["href"])) {
				$str .= '<a';
				$str .= ' target="_blank"';
				$str .= ' href="'.htmlspecialchars($chip["href"]).'"';
				$str .= '>';
			}
			$str .= '<div';
			$str .= ' class="chip hoverable tooltipped '.$chip["classes"].'"';
			$str .= ' data-id="'.$chip["id"].'"';
			$str .= ' data-tooltip="'.$chip["tooltip"].'"';
			$str .= ' data-position="bottom"';
			$str .= ' data-delay="50"';
			$str .= '>';

			$str .= '<img';
			$str .= ' src="'.htmlspecialchars($chip["src"]).'"';
			$str .= ' />';

			$str .= htmlspecialchars($chip["label"]);

			if ($showClearButton) {
				$str .= '<i';
				$str .= ' class="material-icons"';
				$str .= '>';
				$str .= 'clear';
				$str .= '</i>';
			}

			$str .= '</div>';
			
			if (!is_null($chip["href"])) {
				$str .= '</a>';
			}
		}

		$str.= '</div>';

		return $str;
	}

	public static function getAddChip() : string { return self::getAddChipHtml(); } // BC, DEPRECATED

	/**
	 * Get "Add Network" chip
	 * 
	 * @return string Chip HTML
	 */
	public static function getAddChipHtml() : string {
		$str = '';
		$str .= '<a';
		$str .= ' class="modal-trigger"';
		$str .= ' href="#add-social-link-modal"';
		$str .= '>';

		$str .= '<div';
		$str .= ' class="chip hoverable user-color white-text"';
		$str .= '>';

		$str .= 'Add link or e-mail';
		
		$str .= '<i';
		$str .= ' class="material-icons"';
		$str .= '>';
		$str .= 'add';
		$str .= '</i>';

		$str .= '</div>';
		
		$str .= '</a>';

		$str .= '<a';
		$str .= ' class="modal-trigger"';
		$str .= ' href="#add-social-other-modal"';
		$str .= '>';

		$str .= '<div';
		$str .= ' class="chip hoverable user-color white-text"';
		$str .= '>';

		$str .= 'Add other';
		
		$str .= '<i';
		$str .= ' class="material-icons"';
		$str .= '>';
		$str .= 'add';
		$str .= '</i>';

		$str .= '</div>';
		
		$str .= '</a>';

		return $str;
	}

	public static function getAddModal(string $destination="User") : string {
		$result  = '<input type="hidden" id="add-social-type" value="'.htmlspecialchars($destination).'">';
		$result .= '<div id="add-social-link-modal" class="modal modal-fixed-footer">';
		$result .= '<div class="modal-content">';
		$result .= \Catalyst\Form\FormHTML::generateForm(\Catalyst\Database\SocialMedia::getFormStructure());
		$result .= '</div>';
		$result .= '</div>';

		return $result;
	}

	// DEPRECATED
	public static function getArtistChipHTML(\Catalyst\Artist\Artist $artist) : string {
		return self::getChipHtml(self::getChipArray(self::getArtistDisplayFromDatabase($artist)));
	}
}
