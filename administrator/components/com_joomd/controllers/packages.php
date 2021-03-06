<?php
/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Mohammad arshi - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class joomdControllerPackages extends joomdController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		JRequest::setVar( 'view', 'packages' );

		// Register Extra tasks
		$this->registerTask( 'add',	'edit' );
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unpublish', 'publish' );

	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		
		JRequest::setVar( 'layout', 'form'  );
		
		$html = parent::edit();
		
		jexit($html);
		
	}
	
	function save()
	{
		$model = $this->getModel('packages');

		$obj = $model->store();
		
		jexit(json_encode($obj));

	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function delete()
	{
		$model = $this->getModel('packages');
		
		$json = new stdClass();
		
		if($model->delete())	{
						
			$msg = JText::_('DELETESUCCESS');
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
			
		}
		else	{
			$json->result = "error";
			$json->msg = '';
			$json->error = $model->getError();
			
		}
		
		jexit(json_encode($json));

	}
	
	function publish()
	{
		$model = $this->getModel('packages');
		$task = $this->getTask();
		
		$json = new stdClass();
		
		if($model->publish())	{
			
			$msg = ($task=='publish')?JText::_('PUBLISHSUCCESS'):JText::_('UNPUBLISHSUCCESS');
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = $msg;
			$json->error = $model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
					
			
			
		}
		else	{
		
			$json->result = "error";
			$json->error = $model->getError();
		
		}
		
		jexit(json_encode($json));
		
	}
	
	function reorder()
	{
	
		$model = $this->getModel('packages');
		
		if($model->reorder())	{
			jexit('{"result":"success", "msg": "'.JText::_('NEWORDERSAVE').'"}');
		}
		else	{
			
			jexit('{"result":"error", "error": "'.$model->getError().'"}');
			
		}
	
	}
	
	function saveorder()
	{
		
		$model = $this->getModel('packages');
		
		$json = new stdClass();
		
		if($model->saveorder())	{
			
			$data = $this->getItems();
			
			$json->result = "success";
			$json->msg = JText::_('NEWORDERSAVE');
			$json->error = $model->getError();
			$json->html = $data->html;
			$json->count = $data->count;
			$json->total = $data->total;
						
		}
		else	{
			
			$json->result = "error";
			$json->error = $model->getError();
			
		}
		
		jexit(json_encode($json));
	
	}
	
	function getItems()
	{
		
		$model = $this->getModel('packages');
		
		$items = $model->getItems();
		$params = $model->getParams();
		
		ob_start();
		
		$ordering = ($params->filter_order == 'i.ordering');
		
		$disabled = $ordering?'':'disabled="disabled"';
		
		$k = 0;
		$i = $params->limitstart;
		
		if(count($items))	{
		
			for ($n=0; $n < count( $items ); $n++)
			{
				$row =  $items[$n];
				
				require(JPATH_ADMINISTRATOR.'/components/com_joomd/views/packages/tmpl/default_item.php');
				
				$k = 1 - $k;
				$i++;
				
			}
			
		}
		
		else
			echo '<tr class="no_item_block"><td colspan="7" align="center">'.JText::_('NO_ITEM_CREATED').'</td></tr>';
		
		$html = mb_convert_encoding(ob_get_contents(), 'UTF-8');
		
		ob_end_clean();
		
		$data->html = $html;
		$data->count = count($items);
		$data->total = $params->total;
		
		return $data;
		
	}
	
	function loaditems()
	{
		
		$data = $this->getItems();
		
		$json = new stdClass();
		
		$json->result = "success";
		$json->html = $data->html;
		$json->count = $data->count;
		$json->total = $data->total;
		
		jexit(json_encode($json));
	
	}
	
	function loadcats()
	{
		
		$model = $this->getModel('packages');
		
		$item = $model->getItem();
		
		$catids = (array)$item->params->get('cats');
		
		$cats = $model->getCats();
		
		$obj = new stdClass();
		
		$obj->list = '';
				
		for($i=0;$i<count($cats);$i++)	{
		
			$obj->list .= '<option value="'.$cats[$i]->id.'"';
			if(in_array($cats[$i]->id, $catids))
				$obj->list .= ' selected="selected"';
			$obj->list .= '>'.$cats[$i]->treename.'</option>';
			
		}
		
		$obj->result = 'success';
		
		echo json_encode($obj);
		
	}
		
}