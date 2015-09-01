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


switch ($type[0])
{
	case 'full':
		echo $this->Bl->srow(array('class' => 'pages news'));
			echo $this->Bl->sdiv(array('class' => "posts-list"), array());
				foreach ($data['MexcRelatedContent'] as $i => $related)
				{
					echo $this->Bl->sdiv(array('class' => "col-xs-12 col-sm-6 col-md-4"), array());
						echo $this->Bl->sdiv(array('class' => "post new"), array());
							echo $this->Jodel->insertModule($related['related_model'], array('preview', 'box'), $related);
						echo $this->Bl->ediv();
					echo $this->Bl->ediv();
				}
			echo $this->Bl->ediv();
		echo $this->Bl->erow();
	break;
	
	
	
	case 'buro':
		switch ($type[1])
		{
			case 'view':
				$currentModule = '';
				foreach (Configure::read('jj.modules') as $module => $config)
					if ($config['model'] == $data['MexcRelatedContent']['__className'])
						$currentModule = $module;
				
				$type = __d('dashboard', "Dashboard types: $currentModule", true);

				$Model = ClassRegistry::getObject($data['MexcRelatedContent']['__className']);
				$displayField = $data['MexcRelatedContent'][$Model->alias][$Model->displayField];
				
				printf(__d('mexc', 'Relacionado com (%s) "%s"', true), $type, $displayField);
			break;
			
			case 'form':
				echo $this->Buro->sform(array(), array('model' => 'MexcRelated.MexcRelatedContent'));
					echo $this->Buro->input(array(), array('type' => 'hidden', 'fieldName' => 'id'));
					echo $this->Buro->input(array(), array('type' => 'hidden', 'fieldName' => 'model'));
					echo $this->Buro->input(array(), array('type' => 'hidden', 'fieldName' => 'foreign_key'));
					echo $this->Buro->input(array(), array('type' => 'hidden', 'fieldName' => 'related_model'));
					echo $this->Buro->input(array(), array('type' => 'hidden', 'fieldName' => 'related_foreign_key'));

					echo $this->Buro->input(
						array(), 
						array(
							'type' => 'autocomplete',
							'label' => __d('mexc_related', 'Input label', true),
							'instructions' => __d('mexc_related', 'Input instructions', true),
							'options' => array(
								'model' => 'MexcRelated.MexcRelatedContent',
							)
						)
					);
					
					echo $this->Buro->input(
						array(), 
						array(
							'type' => 'checkbox',
							'fieldName' => 'reciprocal',
							'label' => __d('mexc_related', 'form - reciprocal label', true),
						)
					);
					
					echo $this->Buro->submit(
						array(),
						array(
							'label' => __d('mexc', 'save form', true),
							'cancel' => array(
								'label' => __d('mexc', 'cancel form', true)
							)
						)
					);
				echo $this->Buro->eform();
				echo $this->Bl->floatBreak();
			break;
		}
	break;
}

