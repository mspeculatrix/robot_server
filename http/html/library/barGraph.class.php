<?php
/*
	Bar graph class
*/

class BarGraph
{
	var $title;
	
    var $graphHeight = 400;
    var $graphWidth = 600;
	var $marginWidth = 40;
	
    var $data;
	var $dataItems = 0;
	var $labels;
	var $labelFrequency = 1;

	var $useScalines = TRUE;
	var $scalelineColour = array('r' => 204, 'b' => 204, 'g' => 204);
	var $scalelineInterval = 5;
	var $scalelinePos = 'under';
	var $cursorVal = FALSE;
	
	var $maxValue = 1;
	var $baseline = 0;
	var $headroom = 1.01;
	
    var $bgColour = array('r' => 178, 'b' => 178, 'g' => 178);
    var $barColour = array('r' => 204, 'b' => 50, 'g' => 50);
    var $strokeColour = array('r' => 180, 'b' => 20, 'g' => 20);
    var $textColour = array('r' => 0, 'b' => 0, 'g' => 0);
    var $marginColour = array('r' => 220, 'b' => 220, 'g' => 220);
 	var $cursorColour = array('r' => 0, 'b' => 0, 'g' => 180);

    var $im;

    function init($dataArray, $w=600, $h=400, $labelArray=FALSE, $baseline=0) /* initializes the image */
    {
		$this->graphWidth = $w;
		$this->graphHeight = $h;
		$this->data = $dataArray;
		$this->labels = $labelArray;
		$this->dataItems = count($this->data);
		$this->baseline = $baseline;
    }

	function setBaseline($base)
	{
		$this->baseline = $base;
	}

	function setStrokeColour($r, $g, $b)
    {
        $this->strokeColour['r'] = $r;
        $this->strokeColour['g'] = $g;
        $this->strokeColour['b'] = $b;
    }

    function setBarColour($r, $g, $b) /* sets the bar color of the image */
    {
        $this->barColour['r'] = $r;
        $this->barColour['g'] = $g;
        $this->barColour['b'] = $b;
    }

    function setBgColour($r, $g, $b) /* sets the background color of the image */
    {
        $this->bgColour['r'] = $r;
        $this->bgColour['g'] = $g;
        $this->bgColour['b'] = $b;
    }
    
    function setCursor($cVal)
    {
    	$this->cursorVal = $cVal;
    }
    
    function setCursorColour($r, $g, $b) /* sets the background color of the image */
    {
        $this->cursorColour['r'] = $r;
        $this->cursorColour['g'] = $g;
        $this->cursorColour['b'] = $b;
    }
    
    function setHeadroom($hr)
    {
    	$this->headroom = $hr;
    }

	function setLabelFrequency($freq)
	{
		$this->labelFrequency = $freq;
	}

	function setMaxValue($maxval)
	{
		$this->maxValue = $maxval;
	}
	
	function setScalelineInterval($interval)
	{
		$this->scalelineInterval = $interval;
	}

	function setTitle($title)
	{
		$this->title = $title;
	}

	function useScalelines($trueFalse)
	{
		$this->useScalelines = $trueFalse;
	}
	
    function setMarginColour($r, $g, $b) /* sets the background color of the image */
    {
        $this->marginColour['r'] = $r;
        $this->marginColour['g'] = $g;
        $this->marginColour['b'] = $b;
    }

    function setTextColour($r, $g, $b) /* sets the background color of the image */
    {
        $this->textColour['r'] = $r;
        $this->textColour['g'] = $g;
        $this->textColour['b'] = $b;
    }

    function setScalelineColour($r, $g, $b) /* sets the background color of the image */
    {
        $this->scalelineColour['r'] = $r;
        $this->scalelineColour['g'] = $g;
        $this->scalelineColour['b'] = $b;
    }

	function drawCursor($maxval, $minval, &$cursorcol)
	{
		$x1 = $this->marginWidth - 10;
		$x2 = $x1 + $this->graphWidth + 20;
		$y = $this->marginWidth + 
			($this->graphHeight - (($this->cursorVal - $minval) * ($this->graphHeight/($maxval - $minval))));
		imageline($this->im, $x1, $y, $x2, $y, $cursorcol);
	}
	
	function drawScalelines($maxval, $minval, &$scalecol, &$txtcol)
	{
		$x1 = $this->marginWidth - 10;
		$x2 = $x1 + $this->graphWidth + 20;
		for ($i = $minval; $i <= $maxval; $i = $i + $this->scalelineInterval) {
			$y = $this->marginWidth + 
				($this->graphHeight - (($i - $minval) * ($this->graphHeight/($maxval - $minval))));
			imageline($this->im, $x1, $y, $x2, $y, $scalecol);
			imagestring($this->im, 2, 10, $y, $i, $txtcol);
			imagestring($this->im, 2, $this->graphWidth + $this->marginWidth + 10, $y, $i, $txtcol);
		}
	}
	
    function renderGraph() /* to draw bars on the image */
    {
    	$rangeMax = $this->maxValue;
    	$rangeMin = $this->baseline;
    	
    	foreach ($this->data as $data) {
			if($data > $rangeMax) { $rangeMax = $data; }
			if($data < $rangeMin) { $rangeMin = $data; }
		}
		$rangeMax = ceil($rangeMax * $this->headroom); // add a bit of headroom
		$rangeMin = floor($rangeMin);
		$range = $rangeMax - $rangeMin;
		
		$barWidth = $this->graphWidth / $this->dataItems;
		
		$imgWidth = $this->graphWidth + (2 * $this->marginWidth);
		$imgHeight = $this->graphHeight + (2 * $this->marginWidth);
		
        $this->im = imagecreate($imgWidth, $imgHeight);
    
		$bgCol = imagecolorallocate($this->im, $this->bgColour['r'], $this->bgColour['g'], $this->bgColour['b']);
		$barCol = imagecolorallocate($this->im, $this->barColour['r'], $this->barColour['g'], $this->barColour['b']);
		$strokeCol = imagecolorallocate($this->im, $this->strokeColour['r'], $this->strokeColour['g'], $this->strokeColour['b']);
		$textCol = imagecolorallocate($this->im, $this->textColour['r'], $this->textColour['g'], $this->textColour['b']);
		$scalelineCol = imagecolorallocate($this->im, $this->scalelineColour['r'], $this->scalelineColour['g'], $this->scalelineColour['b']);
		$marginCol = imagecolorallocate($this->im, $this->marginColour['r'], $this->marginColour['g'], $this->marginColour['b']);
		$cursorCol = imagecolorallocate($this->im, $this->cursorColour['r'], $this->cursorColour['g'], $this->cursorColour['b']);
		
		# draw image background
        imagefilledrectangle($this->im, 0, 0, $imgWidth, $imgHeight, $marginCol);
		
		# draw graph background
        imagefilledrectangle($this->im, $this->marginWidth, $this->marginWidth, 
        	$this->graphWidth + $this->marginWidth, $this->graphHeight + $this->marginWidth, $bgCol);

		# draw scale lines
		if ($this->scalelinePos == 'under') {
			$this->drawScalelines($rangeMax, $rangeMin, $scalelineCol, $textCol);
		}
		
		$labelCount = 0;
		$scalefactor = $this->graphHeight / $range;

        for ( $bar = 0; $bar < $this->dataItems; $bar++ )
        {
			$val = $this->data[$bar];
            $x1 = ($barWidth * $bar) + $this->marginWidth;
            $y1 = (($rangeMax - $val) * $scalefactor) + $this->marginWidth;
            $x2 = $x1 + $barWidth;
            $y2 = $this->graphHeight + $this->marginWidth;
            imagefilledrectangle($this->im, $x1, $y1, $x2, $y2, $barCol);
			imagerectangle($this->im, $x1, $y1, $x2, $y2, $strokeCol);
        }

		# draw scale lines
		if ($this->scalelinePos == 'over') {
			$this->drawScalelines($rangeMax, $rangeMin, $scalelineCol, $textCol);
		}

		if(count($this->labels) > 0) {
	        for ( $bar = 0; $bar < $this->dataItems; $bar++ ) {
				$labelCount++;
				if ($labelCount == $this->labelFrequency) {
		            $x1 = ($barWidth * $bar) + $this->marginWidth;
		            imageline($this->im, $x1 + ($barWidth/2), $this->graphHeight + $this->marginWidth,
		            	$x1 + ($barWidth/2), $this->graphHeight + $this->marginWidth + 10, $textCol);
 					imagestring($this->im, 2, 
 						$x1, $this->graphHeight + $this->marginWidth + ($this->marginWidth/3), 
 						$this->labels[$bar], $textCol);
					$labelCount = 0;
				}
			}
		}
		
		if ($this->cursorVal) {
			$this->drawCursor($rangeMax, $rangeMin, $cursorCol);
		}
		
		if ($this->title) {
			imagestring($this->im, 2, $this->marginWidth, 10, $this->title, $textCol);
		}

        header("Content-Type: image/png");
        imagepng($this->im);
    }

}
?>
