<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($tableStyle) === false)
{
	$tableStyle = '';
}

?>
<div id="table-<?php echo $tableName; ?>">
<?php 
if ($inPanel === true) {
	if (isset($tableStyle) == false)
	{
		$tableStyle .= 'border-bottom: 1px solid #ddd';
	}
	else
	{
		$tableStyle .= ' border-bottom: 1px solid #ddd';
	}
	
?>
<div class="panel panel-default">
<?php if ($tableName !== null) { ?>
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $tableDisplayName; ?></h3>
    </div>
<?php } } else if ($inBox === true) { ?>
	<div class="box">
	<?php if ($tableDisplayName !== null) { ?>
	<div class="box-header">
		<h3 class="box-title"><?php echo $tableDisplayName; ?></h3>
	</div>
	<?php } ?>
	<div class="box-body no-padding">
	<?php } else if ($tableDisplayName !== null) { ?>
	<h3><?php echo $tableDisplayName; ?></h3>
<?php }
	if ($tableClass !== null)
	{
		echo '<table style="' . $tableStyle . '" class="' .  $tableClass . '" summary="' . $tableName . '">';
	}
	else
	{
		echo '<table style="' . $tableStyle . '" class="table table-condensed table-striped" summary="' . $tableName . '">';
	}
	if ($tableHeaders !== null)
	{
		echo '<thead>';
		echo '<tr>';
		$counter = 0;
		foreach ($tableHeaders as $tableHeader)
		{
			if (isset($sortable) === true and $sortable === true and ((is_array($rowContent[$counter]) === true and key_exists('sortProperty', $rowContent[$counter])) or is_array($rowContent[$counter]) === false or key_exists('timestamp', $rowContent[$counter]) and $rowContent[$counter]['timestamp'] === true))
			{
				if (is_array($rowContent[$counter]) === true)
				{
					$attribute = $rowContent[$counter]['property'];
				}
				else
				{
					$attribute = $rowContent[$counter];
				}
				
				if (isset($sortBy) === false or isset($sortDirection) === false)
				{
					echo '<th class="sorting pagination-link" data-tablename="' . $tableName . '"  data-attribute="' . $attribute . '" data-direction="asc">';
				}
				else if ($attribute === $sortBy and $sortDirection === 'desc')
				{

					echo '<th class="sorting_desc" data-tablename="' . $tableName . '"  data-attribute="' . $attribute . '" data-direction="asc">';
				}
				else if ($attribute === $sortBy)
				{
					echo '<th class="sorting_asc" data-tablename="' . $tableName . '" data-attribute="' . $attribute . '" data-direction="desc">';
				}
				else
				{
					echo '<th class="sorting pagination-link" data-tablename="' . $tableName . '"  data-attribute="' . $attribute . '" data-direction="desc">';
				}

			}
			else
			{
				echo '<th>';
			}

			echo $tableHeader;
			echo '</th>';
			$counter++;
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
			if (key_exists('0', $cellContents) and is_array($cellContents[0]))
			{
				foreach ($cellContents as $cellContent)
				{
							//If its a link generate the first part of the link
							if (key_exists('link', $cellContent) === true)
							{
								if (is_array($cellContent['link']) === true or is_array($cellContent['linkArgument']))
								{
									$keyMissing = false;
									foreach ($cellContent['linkArgument'] as $linkArgument)
									{
										if($model->$linkArgument === null)
										{
											$keyMissing = true;
										}
									}
									
									if ($keyMissing === true)
									{
										continue;
									}
									
									$counter = 0;
									echo '<a href="';
									while (key_exists($counter, $cellContent['link']) === true)
									{
										echo $cellContent['link'][$counter];
										echo $model->$cellContent['linkArgument'][$counter];
										$counter++;
									}
								}
								else
								{
									if ($cellContent['link'] !== '')
									{
										$cellContent['link'] .= '/';
									}
									
									if (key_exists('linkArgument', $cellContent) === true)
									{
										echo '<a href="' . Uri::create($urlSuffix . $cellContent['link'] . $model->$cellContent['linkArgument']);
									}
									else
									{
										echo '<a href="' . Uri::create($urlSuffix . $cellContent['link']);
									}
									
									if (key_exists('linkEnd', $cellContent) === true)
									{
										echo '/' . $cellContent['linkEnd'];
									}
								}
	
								echo '"';
	
								if (key_exists('linkClass', $cellContent) === true)
								{
									echo ' class="'.$cellContent['linkClass'].'"';
								}
								
								echo ' style="margin-left: 5px; margin-top: 5px"';
	
								echo '>';
							}
	
							//Echo the content of the cell.
							if (key_exists('property', $cellContent) === true)
							{
								if (isset($model->$cellContent['property']) === true)
								{
									if (key_exists('timestamp', $cellContent) === true and $cellContent['timestamp'] === true)
									{
										if ($model->$cellContent['property'] != null)
										{
											echo \Date::time_ago($model->$cellContent['property']);	
										}
									}
									else
									{
										echo $model->$cellContent['property'];
									}
								}
								
							}
							else if (key_exists('text', $cellContent) === true)
							{
								echo htmlspecialchars_decode($cellContent['text']);
							}
							else if (key_exists('list', $cellContent) === true)
							{
								$list = '';
								foreach ($model->$cellContent['list'] as $element)
								{
									$list .= $element->$cellContent['listProperty'] . ', ';
								}
								$list = rtrim($list);
								$list = rtrim($list, ',');
								
								echo $list;
							}
							else
							{
								echo 'Text not specified';
							}
	
							//If its a link generate the closing part.
							if (key_exists('link', $cellContent) === true)
							{
								echo '</a>';
							}
				}
			}
			else
			{
				//If its a link generate the first part of the link
							if (key_exists('link', $cellContents) === true)
							{
								if (is_array($cellContents['link']) === true or is_array($cellContents['linkArgument']))
								{
									$keyMissing = false;
									foreach ($cellContents['linkArgument'] as $linkArgument)
									{
										//if($model->$linkArgument === null)
										//{
										//	$keyMissing = true;
										//}
									}
									
									if ($keyMissing === true)
									{
										continue;
									}
									
									$counter = 0;
									echo '<a href="';
									while (key_exists($counter, $cellContents['link']) === true)
									{
										echo $cellContents['link'][$counter];
										if ($cellContents['linkArgument'][$counter] == null or $cellContents['linkArgument'][$counter] == '')
										{
											$counter++;
											continue;
										}
										echo $model->$cellContents['linkArgument'][$counter];
										$counter++;
									}
								}
								else
								{
									if ($cellContents['link'] !== '')
									{
										$cellContents['link'] .= '/';
									}
									
									if (key_exists('linkArgument', $cellContents) === true)
									{
										echo '<a href="' . Uri::create($urlSuffix . $cellContents['link'] . $model->$cellContents['linkArgument']);
									}
									else
									{
										echo '<a href="' . Uri::create($urlSuffix . $cellContents['link']);
									}
									
									if (key_exists('linkEnd', $cellContents) === true)
									{
										echo '/' . $cellContents['linkEnd'];
									}
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
										if ($model->$cellContents['property'] != '')
										{
											echo '<p style="display: inline" data-toggle="tooltip" data-placement="top" title="' . Date::forge($model->$cellContents['property'])->format() . '">' . \Date::time_ago($model->$cellContents['property']) . '</p>';	
										}
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
							else if (key_exists('list', $cellContents) === true)
							{
								$list = '';
								foreach ($model->$cellContents['list'] as $element)
								{
									$list .= $element->$cellContents['listProperty'] . ', ';
								}
								$list = rtrim($list);
								$list = rtrim($list, ',');
								
								echo $list;
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
                    }
                    else
                    {
						$cellContents = html_entity_decode($cellContents);
						$properties = explode("->", $cellContents);
						//Assumed to be a property
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
            
			if ($rowsLimit !== null and $pageCount > 1) { ?>
		<tr>
			<td colspan="<?php echo count($rowContent); ?>">
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

    <?php } ?>
    	</tbody>
</table>
<?php if ($inPanel === true) { ?>
</div>
<?php } ?>
<?php if ($inBox === true) { ?>
</div>
	</div>
<?php } ?>
</div>
<script>
	$('th.sorting, th.sorting_desc, th.sorting_asc').click(function(e){
		navigateTable('<?php echo \Uri::current(); ?>' + '?' + e.target.dataset.tablename + '-sort-by=' + e.target.dataset.attribute + '&' + e.target.dataset.tablename + '-sort-direction=' + e.target.dataset.direction, e);
	});
	
	if (typeof navigateTable === 'undefined')
	{
			navigateTable = function (url, event){
				window.location = url;
			}
	}
	

</script>
