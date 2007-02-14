<?php

/**
 * TrackEditorsDAO.inc.php
 *
 * Copyright (c) 2003-2007 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package conference
 *
 * Class for DAO relating tracks to editors.
 *
 * $Id$
 */

class TrackEditorsDAO extends DAO {

	/**
	 * Constructor.
	 */
	function TrackEditorsDAO() {
		parent::DAO();
	}
	
	/**
	 * Insert a new track editor.
	 * @param $schedConfId int
	 * @param $trackId int
	 * @param $userId int
	 */
	function insertEditor($schedConfId, $trackId, $userId) {
		return $this->update(
			'INSERT INTO track_editors
				(sched_conf_id, track_id, user_id)
				VALUES
				(?, ?, ?)',
			array(
				$schedConfId,
				$trackId,
				$userId
			)
		);
	}
	
	/**
	 * Delete a track editor.
	 * @param $schedConfId int
	 * @param $trackId int
	 * @param $userId int
	 */
	function deleteEditor($schedConfId, $trackId, $userId) {
		return $this->update(
			'DELETE FROM track_editors WHERE sched_conf_id = ? AND track_id = ? AND user_id = ?',
			array(
				$schedConfId,
				$trackId,
				$userId
			)
		);
	}
	
	/**
	 * Retrieve a list of tracks assigned to the specified user.
	 * @param $schedConfId int
	 * @param $userId int
	 * @return array matching Tracks
	 */
	function &getTracksByUserId($schedConfId, $userId) {
		$tracks = array();
		
		$trackDao = &DAORegistry::getDAO('TrackDAO');
				
		$result = &$this->retrieve(
			'SELECT s.* FROM tracks AS s, track_editors AS e WHERE s.track_id = e.track_id AND s.sched_conf_id = ? AND e.user_id = ?',
			array($schedConfId, $userId)
		);
		
		while (!$result->EOF) {
			$tracks[] = &$trackDao->_returnTrackFromRow($result->GetRowAssoc(false));
			$result->moveNext();
		}

		$result->Close();
		unset($result);
	
		return $tracks;
	}
	
	/**
	 * Retrieve a list of all track editors assigned to the specified track.
	 * @param $schedConfId int
	 * @param $trackId int
	 * @return array matching Users
	 */
	function &getEditorsByTrackId($schedConfId, $trackId) {
		$users = array();
		
		$userDao = &DAORegistry::getDAO('UserDAO');
				
		$result = &$this->retrieve(
			'SELECT u.* FROM users AS u, track_editors AS e WHERE u.user_id = e.user_id AND e.sched_conf_id = ? AND e.track_id = ? ORDER BY last_name, first_name',
			array($schedConfId, $trackId)
		);
		
		while (!$result->EOF) {
			$users[] = &$userDao->_returnUserFromRow($result->GetRowAssoc(false));
			$result->moveNext();
		}

		$result->Close();
		unset($result);
	
		return $users;
	}
	
	/**
	 * Retrieve a list of all track editors not assigned to the specified track.
	 * @param $schedConfId int
	 * @param $trackId int
	 * @return array matching Users
	 */
	function &getEditorsNotInTrack($schedConfId, $trackId) {
		$users = array();
		
		$userDao = &DAORegistry::getDAO('UserDAO');
				
		$result = &$this->retrieve(
			'SELECT u.* FROM users AS u NATURAL JOIN roles r LEFT JOIN track_editors AS e ON e.user_id = u.user_id AND e.sched_conf_id = r.sched_conf_id AND e.track_id = ? WHERE r.sched_conf_id = ? AND r.role_id = ? AND e.track_id IS NULL ORDER BY last_name, first_name',
			array($trackId, $schedConfId, ROLE_ID_TRACK_EDITOR)
		);
		
		while (!$result->EOF) {
			$users[] = &$userDao->_returnUserFromRow($result->GetRowAssoc(false));
			$result->moveNext();
		}

		$result->Close();
		unset($result);
	
		return $users;
	}
	
	/**
	 * Delete all track editors for a specified track in a scheduled conference.
	 * @param $trackId int
	 * @param $schedConfId int
	 */
	function deleteEditorsByTrackId($trackId, $schedConfId = null) {
		if (isset($schedConfId)) return $this->update(
			'DELETE FROM track_editors WHERE sched_conf_id = ? AND track_id = ?',
			array($schedConfId, $trackId)
		);
		else return $this->update(
			'DELETE FROM track_editors WHERE track_id = ?',
			$trackId
		);
	}
	
	/**
	 * Delete all track editors for a specified scheduled conference.
	 * @param $schedConfId int
	 */
	function deleteEditorsBySchedConfId($schedConfId) {
		return $this->update(
			'DELETE FROM track_editors WHERE sched_conf_id = ?', $schedConfId
		);
	}
	
	/**
	 * Delete all track assignments for the specified user.
	 * @param $userId int
	 * @param $schedConfId int optional, include assignments only in this scheduled conference
	 * @param $trackId int optional, include only this track
	 */
	function deleteEditorsByUserId($userId, $schedConfId  = null, $trackId = null) {
		return $this->update(
			'DELETE FROM track_editors WHERE user_id = ?' . (isset($schedConfId) ? ' AND sched_conf_id = ?' : '') . (isset($trackId) ? ' AND track_id = ?' : ''),
			isset($schedConfId) && isset($trackId) ? array($userId, $schedConfId, $trackId)
			: (isset($schedConfId) ? array($userId, $schedConfId)
			: (isset($trackId) ? array($userId, $trackId) : $userId))
		);
	}
	
	/**
	 * Check if a user is assigned to a specified track.
	 * @param $schedConfId int
	 * @param $trackId int
	 * @param $userId int
	 * @return boolean
	 */
	function editorExists($schedConfId, $trackId, $userId) {
		$result = &$this->retrieve(
			'SELECT COUNT(*) FROM track_editors WHERE sched_conf_id = ? AND track_id = ? AND user_id = ?', array($schedConfId, $trackId, $userId)
		);
		$returner = isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;

		$result->Close();
		unset($result);

		return $returner;
	}
	
}

?>
