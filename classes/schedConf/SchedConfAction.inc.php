<?php

/**
 * SchedConfAction.inc.php
 *
 * Copyright (c) 2003-2006 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package schedConf
 *
 * SchedConfAction class.
 *
 * $Id$
 */

class SchedConfAction {

	/**
	 * Constructor.
	 */
	function SchedConfAction() {
	}
	
	/**
	 * Actions.
	 */
	 
	/**
	 * Get whether or not we permit users to register as readers
	 */
	function allowRegReader($schedConf) {
		$allowRegReader = false;
		if($schedConf->getSetting('openRegReader')) {
			$allowRegReader = true;
		}
		return $allowRegReader;
	}

	/**
	 * Get whether or not we permit users to register as reviewers
	 */
	function allowRegReviewer($schedConf) {
		$allowRegReviewer = false;
		if($schedConf->getSetting('regReviewerOpenDate') && time() > $schedConf->getSetting('regReviewerOpenDate')) {
			$allowRegReviewer = true;
		}
		if($schedConf->getSetting('regReviewerCloseDate') && time() > $schedConf->getSetting('regReviewerCloseDate')) {
			$allowRegReviewer = false;
		}
		return $allowRegReviewer;
	}

	/**
	 * Get whether or not we permit users to register as presenters
	 */
	function allowRegPresenter($schedConf) {
		$allowRegPresenter = false;
		if($schedConf->getSetting('regPresenterOpenDate') && time() > $schedConf->getSetting('regPresenterOpenDate')) {
			$allowRegPresenter = true;
		}
		if($schedConf->getSetting('regPresenterCloseDate') && time() > $schedConf->getSetting('regPresenterCloseDate')) {
			$allowRegPresenter = false;
		}
		return $allowRegPresenter;
	}
	
	/**
	 * Checks if a user has access to the scheduled conference
	 * @param $schedConf
	 * @return bool
	 */
	function mayViewSchedConf(&$schedConf) {
		$conference =& $schedConf->getConference();
		return $schedConf->getEnabled() && $conference->getEnabled();
	}

	/**
	 * Checks if a user has access to the proceedings index (titles and abstracts)
	 * @param $schedConf
	 * @return bool
	 */
	function mayViewProceedings(&$schedConf) {
		if(Validation::isSiteAdmin() || Validation::isConferenceManager() || Validation::isEditor() || Validation::isTrackEditor()) {
			return true;
		}

		if(!SchedConfAction::mayViewSchedConf($schedConf)) {
			return false;
		}
		
		if($schedConf->getSetting('delayOpenAccess') && time() > $schedConf->getSetting('delayOpenAccessUntil')) {
			if($schedConf->getSetting('openAccessVisitor')) {
				return true;
			}
			if(Validation::isReader() && $schedConf->getSetting('openAccessReader')) {
				return true;
			}
		}

		if(($schedConf->getSetting('postAbstracts') && time() > $schedConf->getSetting('postAbstractsDate')) ||
				($schedConf->getSetting('postPapers')) && time() > $schedConf->getSetting('postPapersDate')) {

			// Abstracts become publicly available as soon as anything is released.
			// Is this too strong an assumption? Presumably, posting abstracts is an
			// unabashedly good thing (since it drums up interest in the conference.)
			return true;
		}

		return false;
	}

	/**
	 * Checks if a user has access to the proceedings index (titles and abstracts)
	 * @param $schedConf
	 * @return bool
	 */
	function mayViewPapers(&$schedConf) {
		if(Validation::isSiteAdmin() || Validation::isConferenceManager() || Validation::isEditor() || Validation::isTrackEditor()) {
			return true;
		}

		if(!SchedConfAction::mayViewSchedConf($schedConf)) {
			return false;
		}
		
		// Allow open access once the "open access" date has passed.
		
		if($schedConf->getSetting('delayOpenAccess') && time() > $schedConf->getSetting('delayOpenAccessUntil')) {
			if($schedConf->getSetting('openAccessVisitor')) {
				return true;
			}
			if(Validation::isReader() && $schedConf->getSetting('openAccessReader')) {
				return true;
			}
		}

		if($schedConf->getSetting('postPapers') && time() > $schedConf->getSetting('postPapersDate')) {

			if($schedConf->getSetting('registrationEnabled') && SchedConfAction::registeredUser($schedConf)) {
				return true;
			} else {
				if($schedConf->getSetting('openAccessVisitor')) {
					return true;
				}
				if(Validation::isReader() && $schedConf->getSetting('openAccessReader')) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Checks if user is entitled by dint of granted roles
	 * @return bool
	 */
	function entitledUser(&$schedConf) {
		$user = &Request::getUser();

		if (isset($user) && isset($schedConf)) {
			// If the user is a conference manager, editor, track editor, or layout editor,
			// it is assumed that they are allowed to view the scheduled conference as a registrant.
			$roleDao = &DAORegistry::getDAO('RoleDAO');
			$registrationAssumedRoles = array(
				ROLE_ID_CONFERENCE_MANAGER,
				ROLE_ID_EDITOR,
				ROLE_ID_TRACK_EDITOR,
				ROLE_ID_REGISTRATION_MANAGER
			);

			// First check for scheduled conference roles
			$roles = &$roleDao->getRolesByUserId($user->getUserId(), $schedConf->getConferenceId(), $schedConf->getSchedConfId());
			foreach ($roles as $role) {
				if (in_array($role->getRoleId(), $registrationAssumedRoles)) return true;
			}

			// Second, conference-level roles
			$roles = &$roleDao->getRolesByUserId($user->getUserId(), $schedConf->getConferenceId(), 0);
			foreach ($roles as $role) {
				if (in_array($role->getRoleId(), $registrationAssumedRoles)) return true;
			}
		}

		$result = false;
		HookRegistry::call('SchedConfAction::entitledUser', array(&$schedConf, &$result));
		return $result;
	}

	/**
	 * Checks if user has registration
	 * @return bool
	 */
	function registeredUser(&$schedConf) {
		$user = &Request::getUser();
		$registrationDao = &DAORegistry::getDAO('RegistrationDAO');

		if (isset($user) && isset($schedConf)) {

			if(SchedConfAction::entitledUser($schedConf)) return true;
			
			$result = $registrationDao->isValidRegistration(null, null, $user->getUserId(), $schedConf->getSchedConfId());
		}
		HookRegistry::call('SchedConfAction::registeredUser', array(&$schedConf, &$result));
		return $result;
	}
	
	/**
	 * Checks if remote client domain or ip is allowed
	 * @return bool
	 */
	function registeredDomain(&$schedConf) {
		$registrationDao = &DAORegistry::getDAO('RegistrationDAO');
		$result = $registrationDao->isValidRegistration(Request::getRemoteDomain(), Request::getRemoteAddr(), null, $schedConf->getSchedConfId());
		HookRegistry::call('SchedConfAction::registeredDomain', array(&$schedConf, &$result));
		return $result;
	}
}

?>
