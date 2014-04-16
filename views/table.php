<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$style = '';

?>
<div id="table-<?php echo $tableName; ?>">
<?php 
if ($inPanel === true) { 
	$style = 'border-bottom: 1px solid #ddd';
?>
<div class="panel panel-default">
<?php if ($tableName !== null) { ?>
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $tableDisplayName; ?></h3>
    </div>
<?php } }
	if ($tableClass !== null)
	{
		echo '<table style="' . $style . '" class="' .  $tableClass . '" summary="' . $tableName . '">';
	}
	else
	{
		echo '<table style="' . $style . '" class="table table-condensed table-striped" summary="' . $tableName . '">';
	}
		if ($tableHeaders !== null)
		{
			echo '<thead>';
			echo '<tr>';
			foreach ($tableHeaders as $tableHeader)
			{
				echo '<th>';
				echo $tableHeader;
				echo '</th>';
			}
			echo '</tr>';
			echo '</thead>';
		}
		?>
        <tbody>
            <?php
			$counter = 0;
            foreach ($models as $model)
            {
				if ($determinePagination === true)
				{
					if ($counter < $this->pageStart or $counter >= $this->pageEnd)
					{
						$counter++;
						continue;
					}
				}
                echo '<tr>';
                foreach ($rowContent as $cellContents)
                {
                    echo '<td>';
                    if (is_array($cellContents) === true)
                    {
						//If its a link generate the first part of the link
						if (key_exists('link', $cellContents) === true)
						{
							echo '<a href="' . Uri::create($cellContents['link']);

							if (key_exists('linkArgument', $cellContents) === true)
							{
								echo '/'.$model->$cellContents['linkArgument'];
							}

							echo '"';

							if (key_exists('linkClass', $cellContents) === true)
							{
								echo ' class="'.$cellContents['linkClass'].'"';
							}

							echo '>';
						}

						//Echo the content of the cell.
						if (key_exists('property', $cellContents) === true)
						{
							if (isset($model->$cellContents['property']) === true)
							{
								if (key_exists('timestamp', $cellContents) === true and $cellContents['timestamp'] === true)
								{
									echo \Date::time_ago($model->$cellContents['property']);
								}
								else
								{
									echo $model->$cellContents['property'];
								}
							}
							
						}
						else if (key_exists('text', $cellContents) === true)
						{
							echo $cellContents['text'];
						}
						else
						{
							echo 'Text not specified';
						}

						//If its a link generate the closing part.
						if (key_exists('link', $cellContents) === true)
						{
							echo '</a>';
						}
                    }
                    else
                    {
						//Assumed to be a property
						$properties = explode("->", $cellContents);
						if (count($properties) !== 1)
						{
							$endProperty = $model;
							foreach ($properties as $property)
							{
								$endProperty = $endProperty->$property;
							}
							if (isset($endProperty))
							{
								echo $endProperty;
							}
							
						}
						else if (isset($model->$cellContents) === true)
						{
							echo $model->$cellContents;
						}
					}
                    echo '</td>';
                }
                echo '</tr>';
				$counter++;
            }
            ?>
		<tr>
			<td colspan="<?php echo count($rowContent); ?>">
				<?php if ($rowsLimit !== null){ ?>
				<ul style="margin: 0" class="pagination">
					<?php
					$getParameters = \Input::get();

					if ($paginationUrl === null)
					{
						$paginationUrl = Uri::current();
					}
					
					$var = '?';
					$counter = 1;
					foreach ($getParameters as $getParamaterIndex => $getParamater)
					{
						if ($getParamater === '')
						{
							continue;
						}
						if ($getParamaterIndex === $tableName . '-page')
						{
							continue;
						}
						
						if (substr($var, -1) === '?')
						{
							$var .= $getParamaterIndex . '=' . $getParamater . '&';
						}
						else
						{
							$var .= $getParamaterIndex . '=' . $getParamater . '&';
						}
					}
					
					//First Arrow
					if ($page == 1)
					{
						echo '<li class="disabled"><a class="disabled pagination-link">&laquo;</a></li>';
					}
					else
					{
						if ($paginationType === 'get')
						{
							echo '<li><a id="' . ($page - 1) . '" href="' . Uri::create($paginationUrl . $var . $tableName . '-page=' . ($page - 1)) . '" class="pagination-link">&laquo;</a></li>';
						}
						else 
						{
							echo '<li><a id="' . ($page - 1) . '" href="' . Uri::create($paginationUrl . DS . ($page - 1)) . '" class="pagination-link">&laquo;</a></li>';
						}
					}

					//Middle links
					for ($i = 1; $i <= $pageCount; $i++)
					{
						if ($i == $page)
						{
							echo '<li class="active"><a class="disabled pagination-link" id="' . $i . '">' . $i . '</a></li>';
						}
						else if ($paginationType === 'get')
						{
								echo '<li><a class="pagination-link" href="' . Uri::create($paginationUrl . $var . $tableName . '-page=' . $i) . '" id="' . $i . '">' . $i . '</a></li>';
						}
						else
						{
							
							echo '<li><a class="pagination-link" href="' . Uri::create($paginationUrl . DS . $i) . '" id="' . $i . '">' . $i . '</a></li>';
						}
					}

					//Last arrow
					if ($page == $pageCount)
					{
						echo '<li class="disabled"><a class="disabled pagination-link">&raquo;</a></li>';
					}
					else
					{
						if ($paginationType === 'get')
						{
							echo '<li><a id="' . ($page + 1) . '" href="' . Uri::create($paginationUrl . $var . $tableName . '-page=' . ($page + 1)) . '" class="pagination-link">&raquo;</a></li>';
						}
						else 
						{
							echo '<li><a id="' . ($page + 1) . '" href="' . Uri::create($paginationUrl . DS . ($page + 1)) . '" class="pagination-link">&raquo;</a></li>';
						}
					}
					?>
			   </ul>
			</td>
		</tr>
	</tbody>
</table>
    <?php } ?>
<?php if ($inPanel === true) { ?>
</div>
<?php } ?>
</div>