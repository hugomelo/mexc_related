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
		if (!isset($type[1]))
			$type[1] = 3;

		$type[1] = min($type[1], count($data['MexcRelatedContent'])*3);
		
		echo $this->Bl->sbox(array(), array('size' => array('M' => $type[1], 'g' => -1), 'type' => 'cloud'));
			echo $this->Bl->boxContainer(array(), array('size' => array('M' => $type[1]), 'type' => 'column_container'));
			foreach ($data['MexcRelatedContent'] as $i => $related)
			{
				$className = $related['__className'];

				echo $this->Bl->sbox(array(), array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'))
				. $this->Jodel->insertModule($className, array('column', 'related_content'), $related)
				. $this->Bl->ebox();
				
				if (($i+1)%($type[1]/3) == 0)
					echo $this->Bl->floatBreak(), $this->Bl->br();
			}
			echo $this->Bl->eboxContainer();
		echo $this->Bl->ebox();
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

