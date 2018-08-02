<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Forum plugin notify configuration
 *
*/

// TODO - create notify messages + LAN

if (!defined('e107_INIT')) { exit; }

e107::lan('forum','notify',true); 

// v2.x Standard 
class forum_notify extends notify
{		
	function config()
	{
			
		$config = array();
	
		$config[] = array(
			'name'			=> LAN_FORUM_NT_NEWTOPIC,
			// 'function'		=> "forum_nt",
			'function'		=> "user_forum_topic_created",
			'category'		=> ''
		);	

		$config[] = array(
			'name'			=> LAN_FORUM_NT_NEWTOPIC_PROB,
			//'function'		=> "forum_ntp",
			'function'		=> "user_forum_topic_created_probationary",
			'category'		=> ''
		);

		$config[] = array(
			'name'			=> LAN_FORUM_NT_TOPIC_DELETED,
			//'function'		=> "forum_topic_del",
			'function'		=> "user_forum_topic_deleted",
			'category'		=> ''
		);	
/*
	    // todo: implement thread split
		$config[] = array(
			'name'			=> LAN_FORUM_NT_TOPIC_SPLIT,
			//'function'		=> "forum_topic_split",
			'function'		=> "user_forum_topic_split",
			'category'		=> ''
		);	
*/
		$config[] = array(
			'name'			=> LAN_FORUM_NT_POST_DELETED,
			//'function'		=> "forum_post_del",
			'function'		=> "user_forum_post_deleted",
			'category'		=> ''
		);	

		$config[] = array(
			'name'			=> LAN_FORUM_NT_POST_REPORTED,
			//'function'		=> "forum_post_rep",
			'function'		=> "user_forum_post_report",
			'category'		=> ''
		);		

		return $config;
	}
	
	//function forum_nt($data) 
	function user_forum_topic_created($data) 
	{
		/*
			[u] = username / realname?
			[f] = forumname
			[l] = link / url
			[s] = subject
			[m] = message
			[d] = deleted by
			[p] = post id
		*/

		if (isset($data['id']) && isset($data['data']))
		{
			$message = 'Notify test: New thread created';
		}
		else
		{
			if(!isset($data['post_id']) || intval($data['post_id']) < 1)
			{
				return;
			}

			$sef = $data['thread_sef'];

			$sql = e107::getDb();
			if($sql->gen('SELECT f.forum_name, f.forum_sef, t.thread_id, t.thread_name, p.post_entry 
				FROM `#forum_post` AS p
				LEFT JOIN `#forum_thread` AS t ON (t.thread_id = p.post_thread)
				LEFT JOIN `#forum` AS f ON (f.forum_id = t.thread_forum_id) 
				WHERE p.post_id = ' . intval($data['post_id'])))
			{
				$data = $sql->fetch();
			}
			else
			{
				return;
			}


			$message = e107::getParser()->lanVars(LAN_FORUM_NT_NEWTOPIC_MSG, array(
				'u' => USERNAME,
				'f' => $data['forum_name'],
				's' => $data['thread_name'],
				'm' => $data['post_entry'],
				'l' => e107::url('forum', 'topic', array('thread_id' => $data['thread_id'], 'thread_sef' => $sef, 'forum_sef' => $data['forum_sef']), array('mode' => 'full'))
			));
		}
		$this->send('user_forum_topic_created', LAN_PLUGIN_FORUM_NAME, $message);
		return true;
	}

	//function forum_ntp($data)
	function user_forum_topic_created_probationary($data)
	{
		if (isset($data['id']) && isset($data['data']))
		{
			$message = 'Notify test: New thread (probationary) created';
		}
		else
		{
			if(!isset($data['post_id']) || intval($data['post_id']) < 1)
			{
				return;
			}

			$sef = $data['thread_sef'];

			$sql = e107::getDb();
			if($sql->gen('SELECT f.forum_name, f.forum_sef, t.thread_id, t.thread_name, p.post_entry 
				FROM `#forum_post` AS p
				LEFT JOIN `#forum_thread` AS t ON (t.thread_id = p.post_thread)
				LEFT JOIN `#forum` AS f ON (f.forum_id = t.thread_forum_id) 
				WHERE p.post_id = ' . intval($data['post_id'])))
			{
				$data = $sql->fetch();
			}
			else
			{
				return;
			}


			$message = e107::getParser()->lanVars(LAN_FORUM_NT_NEWTOPIC_PROB_MSG, array(
				'u' => USERNAME,
				'f' => $data['forum_name'],
				's' => $data['thread_name'],
				'm' => $data['post_entry'],
				'l' => e107::url('forum', 'topic', array('thread_id' => $data['thread_id'], 'thread_sef' => $sef, 'forum_sef' => $data['forum_sef']), array('mode' => 'full'))
			));
		}

		$this->send('user_forum_topic_created_probationary', LAN_FORUM_NT_7, $message);
	}

	//function forum_topic_del($data)
	function user_forum_topic_deleted($data)
	{
		if (isset($data['id']) && isset($data['data']))
		{
			$message = 'Notify test: Thread deleted';
		}
		else
		{
			if(isset($data['thread_id']) && intval($data['thread_id']) < 1)
			{
				return;
			}

			$message = e107::getParser()->lanVars(LAN_FORUM_NT_TOPIC_DELETED_MSG, array(
				'd' => USERNAME,
				'f' => $data['forum_name'],
				's' => $data['thread_name'],
				'l' => e107::url('forum', 'forum', array('forum_id' => $data['forum_id'], 'forum_sef' => $data['forum_sef']), array('mode' => 'full'))
			));
		}

		$this->send('user_forum_topic_deleted', LAN_FORUM_NT_8, $message);
	}

	//function forum_topic_split($data)
	function user_forum_topic_split($data)
	{
		if (isset($data['id']) && isset($data['data']))
		{
			$message = 'Notify test: Topic splitted';
		}
		else
		{
			$message = $data;
		}

		$this->send('forum_topic_split', LAN_FORUM_NT_9, $message);
	}

	//function forum_post_del($data)
	function user_forum_post_deleted($data)
	{
		if (isset($data['id']) && isset($data['data']))
		{
			$message = 'Notify test: Post deleted';
		}
		else
		{
			if(isset($data['post_id']) && intval($data['post_id']) < 1)
			{
				return;
			}

			$entry = $data['post_entry'];
			$postid = $data['post_id'];

			$sql = e107::getDb();
			if($sql->gen('SELECT f.forum_name, f.forum_sef, t.thread_id, t.thread_name 
				FROM `#forum_thread` AS t
				LEFT JOIN `#forum` AS f ON (f.forum_id = t.thread_forum_id) 
				WHERE t.thread_id = ' . intval($data['post_thread'])))
			{
				$data = $sql->fetch();
			}
			else
			{
				return;
			}

			$sef = eHelper::title2sef($data['thread_name'],'dashl');

			$message = e107::getParser()->lanVars(LAN_FORUM_NT_POST_DELETED_MSG, array(
				'd' => USERNAME,
				'f' => $data['forum_name'],
				's' => $data['thread_name'],
				'p' => $postid,
				'm' => $entry,
				'l' => e107::url('forum', 'topic', array('thread_id' => $data['thread_id'], 'thread_sef' => $sef, 'forum_sef' => $data['forum_sef']), array('mode' => 'full'))
			));
		}
		$this->send('user_forum_post_deleted', LAN_FORUM_NT_10, $message);
	}

	//function forum_post_rep($data)
	function user_forum_post_report($data)
	{
		if (isset($data['id']) && isset($data['data']))
		{
			$message = 'Notify test: Post reported';
		}
		else
		{
			$message = $data;
		}

		$this->send('forum_post_rep', LAN_FORUM_NT_11, $message);
	}
	
}


?>