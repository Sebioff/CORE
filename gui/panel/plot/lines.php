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

//require jpgraph
require_once dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph.php';
//require the plot files
require_once dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_line.php';
require_once dirname(__FILE__).'/../3rdparty/jpgraph/jpgraph_plotline.php';

class GUI_Panel_Plot_Lines extends GUI_Panel_Plot {
	private $enableMarkers = true;
	private $markerCount = 0;
	
	public function __construct($name, $width = 600, $height = 300, $description = '', $title = '') {
		$this->graph = new Graph($width, $height, 'auto');
		$this->graph->SetScale('textlin');
		// format the image
		$this->setLegendPosition(parent::LEGEND_POSITION_NORTH);
		if ($title)
			$this->graph->title->Set($title);
		
		parent::__construct($name, $description, $title);
	}

	/**
	 * @return LinePlot
	 */
	public function addLine(array $line, $name = '', $color = '') {
		$plot = new LinePlot($line);
		if (Text::length($name) > 10)
			$name = substr($name, 0, 10);
		$plot->SetLegend($name);
		$plot->SetWeight(2);
		if (Text::length($color) > 0)
			$plot->SetColor($color);
		$this->graph->add($plot);
		if ($this->enableMarkers) {
			$this->markerCount++;
			$plot->mark->SetColor($plot->color);
			$plot->mark->SetFillColor($plot->color);
			$plot->mark->setType($this->markerCount);
			if ($this->markerCount >= 11)
				$this->markerCount = 0;
		}
		return $plot;
	}
	
	public function setTitle($title) {
		$this->graph->title->set($title);
	}
	
	public function setXNames(array $names) {
		$this->graph->xaxis->setTickLabels(array_values($names));
		if (Text::length(reset($names)) > 2)
			$this->graph->xaxis->SetLabelAngle(90);
	}
	
	public function setXTickInterval($interval) {
		$this->graph->xaxis->SetTextTickInterval($interval);
	}
	
	public function enableMarkers($enableMarkers) {
		$this->enableMarkers = $enableMarkers;
	}
}

?>