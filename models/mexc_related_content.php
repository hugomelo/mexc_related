<?php

/**
 *
 * Copyright 2011-2013, Museu Exploratório de Ciências da Unicamp (http://www.museudeciencias.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011-2013, Museu Exploratório de Ciências da Unicamp (http://www.museudeciencias.com.br)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link          https://github.com/museudecienciasunicamp/mexc_related.git Mexc Related public repository
 */

class MexcRelatedContent extends MexcRelatedAppModel
{
	var $name = 'MexcRelatedContent';
	
	function findBurocrataAutocomplete($query)
	{
		$results = array();
		$modelName = $query['autocomplete']['MexcRelatedContent']['model'];
		$query = $query['autocomplete']['MexcRelatedContent']['id'];
		
		$Model =& ClassRegistry::init($modelName);
		$relateds = $Model->getRelatedModels();
		
		$results = array();
		foreach ($relateds as $related)
		{
			$RModel =& ClassRegistry::init($related);
			
			$currentModule = '';
			foreach (Configure::read('jj.modules') as $module => $config)
				if ($config['model'] == $related)
					$currentModule = $module;
			
			$type = __d('dashboard', "Dashboard types: $currentModule", true);
			
			$Rresults = $RModel->find('all', array(
				'fields' => array('CONCAT(id) as `content_id`', "'{$related}' as `model`", "CONCAT('({$type}) ', {$RModel->displayField}) as `displayField`"),
				'recursive' => -1,
				'conditions' => array("{$RModel->displayField} LIKE '%$query%'"),
				'limit' => 10
			));
			
			$results = am ($results, Set::extract("/0/.[1]", $Rresults));
		}
		return (object) $results;
	}

/**
 * Custom find for burocrata inputs
 * 
 * @access public
 * @return array Result from findById + related data.
 */	
	public function findBurocrata($id)
	{
		$this->recursive = -1;
		$data = $this->findById($id);
		
		if (!empty($data))
		{
			$Model =& ClassRegistry::init($data['MexcRelatedContent']['related_model']);
			$Model->recursive = -1;
			$data['MexcRelatedContent'] += $Model->findById($data['MexcRelatedContent']['related_foreign_key']);
			$data['MexcRelatedContent']['__className'] = $data['MexcRelatedContent']['related_model'];
		}

		return $data;
	}

/**
 * Deal with "reciprocal" relationships.
 * 
 * @access public
 * @return mixed Same return of Model::save()
 */
	public function saveBurocrata($data)
	{
		if (isset($data['MexcRelatedContent']['reciprocal']) && $data['MexcRelatedContent']['reciprocal'])
		{
			$this->save(array('MexcRelatedContent' => array(
				'model' => $data['MexcRelatedContent']['related_model'],
				'related_model' => $data['MexcRelatedContent']['model'],
				'foreign_key' => $data['MexcRelatedContent']['related_foreign_key'],
				'related_foreign_key' => $data['MexcRelatedContent']['foreign_key']
			)));
		}
		
		$this->create();
		return $this->save($data);
	}
}

