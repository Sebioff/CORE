<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

require_once CORE_PATH.'/gui/panel/3rdparty/jpgraph/jpgraph_theme.inc.php';

abstract class GUI_Panel_Plot extends GUI_Panel_Image {
	public static $defaultTheme = 'UniversalTheme';
	
	protected $graph = null;
	protected $height = 300;
	protected $width = 600;
	
	const LEGEND_POSITION_NORTH = 1;
	const LEGEND_POSITION_EAST = 2;
	const LEGEND_POSITION_SOUTH = 3;
	const LEGEND_POSITION_WEST = 4;
	
	public function __construct($name, $description = '', $title = '') {
		// wrap it... we don't need an url here
		parent::__construct($name, '', $description, $title);
		$this->graph->setTheme(new self::$defaultTheme());
	}
	
	protected function beforeDisplay() {
		$filename = System::getTemporaryDirectory().DS.microtime(true);
		try {
			$this->graph->stroke($filename);
			$this->setURL(App::get()->getModule(GUI_Panel_Plot_Image::SCRIPTLET_NAME)->getUrl(array('img' => basename($filename))));
		} catch (JpGraphExceptionL $e) {
			$this->setURL(Router::get()->getStaticRoute('core_img', '/error.png'));
		}
		
		parent::beforeDisplay();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	/**
	 * @return Graph
	 */
	public function getGraph() {
		return $this->graph;
	}
	
	/**
	 * @param position: self::LEGEND_POSITION_*
	 */
	public function setLegendPosition($position) {
		switch ($position) {
			case self::LEGEND_POSITION_NORTH:
				$this->graph->legend->Pos(0.5, 0.09, 'center', 'bottom');
				$this->graph->legend->SetLayout(LEGEND_HOR);
				$this->graph->img->SetMargin(60, 20, 35, 65);
			break;
			case self::LEGEND_POSITION_EAST:
				$this->graph->legend->Pos(0.01, 0.01, 'right', 'top');
				$this->graph->legend->SetLayout(LEGEND_VERT);
				$this->graph->img->SetMargin(30, 110, 10, 35);
			break;
			case self::LEGEND_POSITION_SOUTH:
				$this->graph->legend->Pos(0.5, 0.9, 'center', 'top');
				$this->graph->legend->SetLayout(LEGEND_HOR);
				$this->graph->img->SetMargin(60, 20, 5, 65);
			break;
			case self::LEGEND_POSITION_WEST:
				$this->graph->legend->Pos(0.01, 0.01, 'left', 'top');
				$this->graph->legend->SetLayout(LEGEND_VERT);
				$this->graph->img->SetMargin(140, 10, 10, 35);
			break;
			default:
				$this->addError('unknown legend position: '.$position);
			break;
		}
	}
}

?>