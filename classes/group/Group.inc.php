<?php

/**
 * Group.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package group
 *
 * Group class.
 * Describes user groups in conferences.
 *
 * $Id$
 */

class Group extends DataObject {

	/**
	 * Constructor.
	 */
	function Group() {
		parent::DataObject();
	}

	/**
	 * Get localized title of conference group.
	 */
	function getGroupTitle() {
		$alternateLocaleNum = Locale::isAlternateConferenceLocale($this->getConferenceId());
		$title = null;
		switch ($alternateLocaleNum) {
			case 1: $title = $this->getTitleAlt1(); break;
			case 2: $title = $this->getTitleAlt2(); break;
		}
		// Fall back on the primary locale title.
		if (empty($title)) $title = $this->getTitle();

		return $title;
	}

	//
	// Get/set methods
	//
	
	/**
	 * Get title of group (primary locale)
	 * @return string
	 */
	 function getTitle() {
	 	return $this->getData('title');
	}
	
	/**
	* Set title of group
	* @param $title string
	*/
	function setTitle($title) {
		return $this->setData('title',$title);
	}
	
	/**
	 * Get flag indicating whether or not the group is displayed in "About"
	 * @return boolean
	 */
	 function getAboutDisplayed() {
	 	return $this->getData('aboutDisplayed');
	}
	
	/**
	* Set flag indicating whether or not the group is displayed in "About"
	* @param $aboutDisplayed boolean
	*/
	function setAboutDisplayed($aboutDisplayed) {
		return $this->setData('aboutDisplayed',$aboutDisplayed);
	}
	
	/**
	 * Get title of group (alternate locale 1)
	 * @return string
	 */
	 function getTitleAlt1() {
	 	return $this->getData('titleAlt1');
	}
	
	/**
	* Set title of group (alternate locale 1)
	* @param $title string
	*/
	function setTitleAlt1($title) {
		return $this->setData('titleAlt1',$title);
	}
	
	/**
	 * Get title of group (alternate locale 2)
	 * @return string
	 */
	 function getTitleAlt2() {
	 	return $this->getData('titleAlt2');
	}
	
	/**
	* Set title of group (alternate locale 2)
	* @param $title string
	*/
	function setTitleAlt2($title) {
		return $this->setData('titleAlt2',$title);
	}
	
	/**
	 * Get ID of group.
	 * @return int
	 */
	function getGroupId() {
		return $this->getData('groupId');
	}
	
	/**
	 * Set ID of group.
	 * @param $groupId int
	 */
	function setGroupId($groupId) {
		return $this->setData('groupId', $groupId);
	}
	
	/**
	 * Get ID of scheduled conference this group belongs to.
	 * @return int
	 */
	function getSchedConfId() {
		return $this->getData('schedConfId');
	}
	
	/**
	 * Set ID of scheduled conference this group belongs to.
	 * @param $schedConfId int
	 */
	function setSchedConfId($schedConfId) {
		return $this->setData('schedConfId', $schedConfId);
	}
	
	/**
	 * Get ID of conference this group belongs to.
	 * @return int
	 */
	function getConferenceId() {
		return $this->getData('conferenceId');
	}
	
	/**
	 * Set ID of conference this group belongs to.
	 * @param $conferenceId int
	 */
	function setConferenceId($conferenceId) {
		return $this->setData('conferenceId', $conferenceId);
	}
	
	/**
	 * Get sequence of group.
	 * @return float
	 */
	function getSequence() {
		return $this->getData('sequence');
	}
	
	/**
	 * Set sequence of group.
	 * @param $sequence float
	 */
	function setSequence($sequence) {
		return $this->setData('sequence', $sequence);
	}
}

?>
