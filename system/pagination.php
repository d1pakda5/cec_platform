<?php
function pagination($query, $per_page = 30,$page = 1, $self = '?'){        
	$query = "SELECT COUNT(*) as `num` FROM {$query}";
	$row = mysql_fetch_array(mysql_query($query));
	$total = $row['num'];
	$adjacents = "2";	
	$page = ($page == 0 ? 1 : $page);  
	$start = ($page - 1) * $per_page;
	$prev = $page - 1;							
	$next = $page + 1;
	$lastpage = ceil($total/$per_page);
	$lpm1 = $lastpage - 1;	
	$pagination = "";
	$self = mysql_real_escape_string($self);
	if($lastpage > 1)
	{	
		$pagination .= "<ul class='pagination no-margin pull-left'>";
				$pagination .= "<li class='disabled'><a>Page $page of $lastpage</a></li>";
		if ($lastpage < 7 + ($adjacents * 2))
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<li class='active'><a>$counter</a></li>";
				else
					$pagination.= "<li><a href='$self&page=$counter'>$counter</a></li>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))
		{
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class='active'><a>$counter</a></li>";
					else
						$pagination.= "<li><a href='$self&page=$counter'>$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href='$self&page=$lpm1'>$lpm1</a></li>";
				$pagination.= "<li><a href='$self&page=$lastpage'>$lastpage</a></li>";		
			}
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<li><a href='$self&page=1'>1</a></li>";
				$pagination.= "<li><a href='$self&page=2'>2</a></li>";
				$pagination.= "<li class='dot'><a>...</a></li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class='active'>$counter</a></li>";
					else
						$pagination.= "<li><a href='$self&page=$counter'>$counter</a></li>";					
				}
				$pagination.= "<li class='dot'><a>...</a></li>";
				$pagination.= "<li><a href='$self&page=$lpm1'>$lpm1</a></li>";
				$pagination.= "<li><a href='$self&page=$lastpage'>$lastpage</a></li>";		
			}
			else
			{
				$pagination.= "<li><a href='$self&page=1'>1</a></li>";
				$pagination.= "<li><a href='$self&page=2'>2</a></li>";
				$pagination.= "<li class='dot'><a>...</a></li>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class='active'><a>$counter</a></li>";
					else
						$pagination.= "<li><a href='$self&page=$counter'>$counter</a></li>";					
				}
			}
		}		
		if ($page < $counter - 1){ 
			$pagination.= "<li><a href='$self&page=$next'>Next</a></li>";
			$pagination.= "<li><a href='$self&page=$lastpage'>Last</a></li>";
		}else{
			$pagination.= "<li class='disabled'><a>Next</a></li>";
			$pagination.= "<li class='disabled'><a>Last</a></li>";
		}
		$pagination.= "</ul>\n";		
	}
	return $pagination;
    }
?>