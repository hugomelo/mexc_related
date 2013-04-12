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

class MexcHasRelatedContentBehavior extends ModelBehavior
{
/**
 * Bahavior property that holds all settings. 
 * This property is populated on setup()
 * 
 * @var array
 * @access public
 */
	public $settings;

/**
 * setup
 * 
 * This method just receive the configuration array and stores it.
 * The configuration must follow the format:
 * 
 * {{{
 * 	array(
 *		'SomeAlias' => 'Plugin.ModelName',
 *		...
 *	)
 * }}}
 * 
 * for each allowed related content.
 * 
 * 
 * @access public
 * @return void 
 */
	public function setup(&$Model, $config = array())
	{
		if (!is_array($config))
		{
			trigger_error('MexcHasRelatedContentBehavior::setup() - The configuration must be an array.');
			return;
		}
		
		$Model->bindModel(array('hasMany' => array(
			'MexcRelatedContent' => array(
				'className' => 'MexcRelated.MexcRelatedContent',
				'foreignKey' => 'foreign_key',
				'conditions' => array(
					array(
						'MexcRelatedContent.model LIKE' => '%'.$Model->alias.'%',
						'MexcRelatedContent.related_model' => $config,
					),
				)
			)
		)), false);
		
		$this->settings[$Model->alias] = $config;
	}

/**
 * 
 * 
 * @access public
 * @return array The model list of names
 */
	public function afterFind(&$Model, $results, $primary)
	{
		if (!$primary)
			return $results;
		
		foreach ($results as &$result)
		{
			if (!isset($result['MexcRelatedContent']))
				continue;

			foreach ($result['MexcRelatedContent'] as &$content)
				$content += $this->getRelatedContent($Model, $content);
		}
		
		return $results;
	}

/**
 * An getter to make the array of settings reachable.
 * 
 * @access public
 * @return array The list of related models
 */
	public function getRelatedModels(&$Model)
	{
		return $this->settings[$Model->alias];
	}

/**
 * Get the related content from database
 * 
 * @access protected
 * @return array The results from the related model
 */
	protected function getRelatedContent(&$Model, $data)
	{
		$className = $id = false;
		if (strpos($data['model'], $Model->alias) !== false)
		{
			$className = $data['related_model'];
			$id = $data['related_foreign_key'];
		}
		
		if (in_array($className, $this->settings[$Model->alias]))
		{
			$RelatedModel = ClassRegistry::init($className);
			
			if ($RelatedModel->Behaviors->attached('MexcHasRelatedContent') && $RelatedModel->Behaviors->enabled('MexcHasRelatedContent'))
				$RelatedModel->Behaviors->disable('MexcHasRelatedContent');

			$data = $RelatedModel->findById($id);

			if ($RelatedModel->Behaviors->attached('MexcHasRelatedContent') && !$RelatedModel->Behaviors->enabled('MexcHasRelatedContent'))
				$RelatedModel->Behaviors->enable('MexcHasRelatedContent');
			
			if (!empty($data))
				return $data + array('__className' => $className);
		}
		
		return array();
	}
}
