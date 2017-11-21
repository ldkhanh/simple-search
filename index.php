<?php 

header('Content-Type:text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q'])? $_REQUEST['q'] : false;
$results = false;
if(isset($query))
{
	require_once('Apache/Solr/Service.php');
	$solr = new Apache_Solr_Service('localhost',8983,'/solr/myexample/');

	if(get_magic_quotes_gpc() == 1)
	{
		$query = stripslashes($query);
	}

	try
	{
		if(!isset($_GET['algorithm']))
			$_GET['algorithm']="lucene";

		if($_GET['algorithm'] == "lucene"){

			$additionalParameters = array('fl'=>"id, description, title, og_url");
			$results = $solr->search($query, 0, $limit, $additionalParameters);
		}else{
			$pageRankParameters = array(
				'fl'=>"id, description, title, og_url", 
				'sort'=>'pageRankFile desc'
			);
			$results = $solr->search($query, 0, $limit, $pageRankParameters);
		}

		
	}
	catch(Exception $e)
	{
		die("<html><head><title>SEARCH EXCEPTION</title></head><body><pre>{$e->__toString()}</pre></body></html>");
	}
}
?>
<html>
	<head>
		<title>Khanh HW4</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">

		<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,‌​100italic,300,300ita‌​lic,400italic,500,50‌​0italic,700,700itali‌​c,900italic,900' rel='stylesheet' type='text/css'>
		<style>
			.header-form { margin: 20px 0 0 150px; }
			.resultStats {font-size: 14px; line-height: 43px; color: #808080; display: block; font-family: 'Roboto',arial,sans-serif; padding: 0 0 0 105px;}
			.table_re { text-align: left; padding: 5px 0 5px 0px;}
			.table_re th {font-size: 12px;}
			.table_re td {padding-left: 20px;}
			.title 	{text-decoration: none; }
			a.title:link {cursor: pointer; color: #1a0dab; font-size: 18px; font-family: 'Roboto',arial,sans-serif; line-height: 1.2; text-align: left; font-weight: normal;}
			.og_url {text-decoration: none; font-size: 14px; color: #006621;font-style: normal; height: 18px; line-height: 16px; font-family: 'Roboto',arial,sans-serif; text-align: left; font-weight: normal;}
			.description , .id_re{line-height: 1.4; word-wrap: break-word; line-height: 18px; color: #545454; font-family: 'Roboto',arial,sans-serif; font-size: 14px; text-align: left; font-weight: normal;}

		</style>
	</head>
	<body>
		<form class="header-form form-inline" accept-charset="utf-8" method="get">
			<div class="form-group mx-sm-3">
				<label for="q" class="sr-only">Search:</label>
		      	<div class="input-group mb-2 mb-sm-0">
		        	<input type="text" class="form-control" id="q" name="q" size="50" placeholder="Search:"
		        	value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8');?>">
		        	<button type="submit" class="input-group-addon">Search</button>
		      	</div>
			</div>
			
			<div class="radio-algo">
				<input type="radio" name="algorithm" value="lucene"/> Solr Lucene
				<input type="radio" name="algorithm" value="pagerank"/> Google PageRank 
			</div>
		</form>
		<?php
			if($results) 
			{
				$total= (int)$results->response->numFound;
				$start = min(1, $total);
				$end = min($limit, $total);
		?>
			<div class="resultStats">Results <?php echo $start;?> - <?php echo $end;?> of <?php echo $total;?>:</div>
				<?php
				foreach ($results->response->docs as $doc) {
				?>
					<table class="table_re">
						<tr>
							<th>title</th>
							<td>
								<?php
									if(isset($doc->og_url)) {
										$og_Url_T = htmlspecialchars($doc->og_url, ENT_NOQUOTES, 'utf-8');
									} else {
										$og_Url_T = "#";
									}
									$titleL = htmlspecialchars($doc->title, ENT_NOQUOTES, 'utf-8');
								?>
								<a class="title" href="<?php echo $og_Url_T;?>" target="_blank">
										<?php echo $titleL;?>
								</a>

							</td>
						</tr>
						<tr>
							<th>og_url</th>
							<td>
								<?php
									if(isset($doc->og_url)) {
										$linkUrl = htmlspecialchars($doc->og_url, ENT_NOQUOTES, 'utf-8');
								?>
									<a class="og_url" href="<?php echo $linkUrl;?>" target="_blank">
										<?php echo $linkUrl;?>
									</a>
								<?php } else {
									echo "<span class='id_re'>NA</span>";
								} 
								?>
							</td>
						</tr>
						<tr>
							<th>description</th>
							<td>
								<span class="description">
								<?php 
									if (isset($doc->description)) {
										echo htmlspecialchars($doc->description, ENT_NOQUOTES, 'utf-8');
									} else {
										echo "NA";
									}
								?>
								</span>
							</td>
						</tr>
						<tr>
							<th>id</th>
							<td><span class="id_re"><?php echo htmlspecialchars($doc->id, ENT_NOQUOTES, 'utf-8');?></span></td>
						</tr>
					</table>
				<?php
				}
				?>
		<?php		
			}
		?>
	</body>
</html>