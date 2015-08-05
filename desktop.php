<script data-jsfiddle="common" src="/libs/handsontable-master/dist/handsontable.full.js"></script>
<link data-jsfiddle="common" rel="stylesheet" media="screen" href="/libs/handsontable-master/dist/handsontable.full.css">

<?php 
	function get_file() {
		$csv = file_get_contents('my_data.csv');
		$lines = explode("\n", $csv);
		$head = str_getcsv(array_shift($lines));
		$array = array();
		foreach ($lines as $line) {
		  $array[] = array_combine($head, str_getcsv($line));
		}
		return $array;
	}

	function get_sliders($current_category,$categories) {
		$sliders = array();
		$i=0;
		foreach ($categories as $category) {
			if ($category['type']=='sliders' && $category['category']==$current_category && $category['status']=='enabled') {
				$sliders[] = $category;
			}
		}
		return $sliders;
	}

	function get_subheroes($current_category,$categories) {
		$subheroes = array();
		foreach ($categories as $category) {
			if ($category['type']=='subheroes' && $category['category']==$current_category && $category['status']=='enabled') {
				$subheroes[] = $category;
			}
		}
		return $subheroes;
	}

	function get_padding($cols) {
		$padding="";
		if ($cols==0) $padding="padding:0 5px 0 0";
		elseif ($cols==1) $padding="padding:0 5px 0 5px";
		elseif ($cols==2) $padding="padding:0 0 0 5px";

		return $padding;
	}

	function get_offset($subheroes,$row,$col) {
		$row = $row+1;
		$last_subhero_row = ceil(count($subheroes)/3);
		$spaces = count($subheroes)%3;

		$padding = '';
		if ($row == $last_subhero_row) {
			if($col==0) {
				if ($spaces==0) $padding = "";
				elseif ($spaces==1) $padding = "gccol-offset-4-12";
				elseif ($spaces==2) $padding = "gccol-offset-2-12";
				elseif ($spaces==3) $padding = "";
			}
		}
		return $padding=='' ? "" : $padding . " ";
	}

	function get_current_uri() {
		$current_url = explode("?", $_SERVER['REQUEST_URI']);
		return $current_url[0];
	}

	function create_file($filename,$html) {
		$filename = $filename . ".html";
		$myfile = fopen($filename,"w") or die("Unable to open file!");
		fwrite($myfile,$html);
		fclose($myfile);
	}

	function get_icid($tile) {
		return "";
	}

	function clean_code($code) {
		$find = array('http://www.guitarcenter.com','http://images.guitarcenter.com','http://');
		$replace = array('','//images.guitarcenter.com','');
		return str_replace($find,$replace,$code);
	}

	if ($_GET) {
		$categories = get_file();
		$sliders = get_sliders($_GET['category'],$categories);
		$subheroes = get_subheroes($_GET['category'],$categories);
		$subhero_rows = count($subheroes);
	}
?>

<!-- Links -->
<style type="text/css">
	* {font-family:Arial;}
	.categories {list-style-type:none;display:block;width:100%;padding:5px 0 5px 0;margin:0;clear:both;height:30px;}
	.categories li {list-style-type:none;float:left;margin:0 10px 10px 0;}
	.box {border:1px solid #afafaf;padding:10px;margin:5px 5px 10px 5px;}
	.important {padding:0;margin:0;color:#c00;}
	.note {padding:0;margin:0;}
</style>
<div class="box">
	<h3 style="padding:0;margin:0;">Select a category</h3>
	<p class="important">Make sure that the csv file, "my_data.csv," exists in the <strong>same folder</strong> and should have the column names <strong>category, type, title, imageUrl, url, and status</strong>.</p>
	<p class="note"><strong>Note: </strong>Status column should always be enabled, unless advised.</p>
	<hr/>
	<ul class="categories">
		<li><a href="<?php echo get_current_uri(); ?>">Editor</a></li>
		<li><a href="?category=Accessories">Accessories</a></li>
		<li><a href="?category=Amps">Amps</a></li>
		<li><a href="?category=Bass">Bass</a></li>
		<li><a href="?category=Books">Books</a></li>
		<li><a href="?category=DJ">DJ</a></li>
		<li><a href="?category=Drums">Drums</a></li>
		<li><a href="?category=Guitars">Guitars</a></li>
		<li><a href="?category=Keyboards">Keyboards</a></li>
		<li><a href="?category=Livesound">Livesound</a></li>
		<li><a href="?category=Mics">Mics</a></li>
		<li><a href="?category=Recording">Recording</a></li>
		<li><a href="?category=Lighting">Lighting</a></li>
		<li><a href="?category=Software">Software</a></li>
		<li><a href="?category=iOS">iOS</a></li>
	</ul>
</div>

<!-- Display Slider and Subhero -->
<?php if($_GET): ?>
<?php ob_start(); ?>
<!-- Html -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<style type="text/css">
	#content-wrapper {width:775px;margin:0 auto;display:block;}
</style>

<link rel="stylesheet" type="text/css" href="http://images.guitarcenter.com/Content/GC/libs/css/gcgrid-desktop_v6.css" />
<link rel="stylesheet" type="text/css" href="http://images.guitarcenter.com/Content/GC/libs/flexslider/css/flexslider.css" />
<style type="text/css">
	#images.flexslider {border:0;border-radius:0;}
	.flex-control-paging li a.fl2ex-active {background-color:#c00;}
	.flexslider {box-shadow:none;}
</style>
<div id="content-wrapper">
	<section class="slider">
		<!-- Slides -->
	    <div id="images" class="flexslider">
	      <ul class="slides">
			<?php foreach($sliders as $slider): ?>
		    	<li>
		    		<a href="<?php echo $slider['url']; ?>">
		    			<img src="<?php echo $slider['imageUrl'] ?>" alt="<?php echo $slider['title'] ?>" title="<?php echo $slider['title'] ?>" />
		    		</a>
				</li>
			<?php endforeach; ?>
		  </ul>
		</div>
		<!-- Slides -->
		<!-- Subheroes -->
		<?php $counter=0;$last_subhero_row=ceil(count($subheroes)/2);$subheroes_count=count($subheroes); ?>
		<?php for($rows=0;$rows<$last_subhero_row;$rows++): ?>
			<div class="gcrow">
				<?php for ($cols=0;$cols<3;$cols++): ?>
					<?php if ($counter<$subheroes_count): ?>
						<div class="<?php echo get_offset($subheroes,$rows,$cols); ?>gccol-4-12" style="<?php echo get_padding($cols); ?>">
							<a href="<?php echo $subheroes[$counter]['url']; ?>">
								<img src="<?php echo $subheroes[$counter]['imageUrl'] ?>" alt="<?php echo $subheroes[$counter]['title'] ?>" title="<?php echo $subheroes[$counter]['title']; ?>" />
							</a>
						</div>
					<?php endif; ?>
					<?php $counter++; ?>
				<?php endfor; ?>
			</div>
		<?php endfor; ?>
		<!-- Subheroes -->
	</section>
</div>
<script src="http://images.guitarcenter.com/Content/GC/libs/js/jquery.flexslider-min.js"></script>
<script type="text/javascript">
	$(window).load(function() {
	  $('#images').flexslider({
	    animation: "slide",
	    slideshow: true,
	    slideshowSpeed: 4000,
	    itemMargin: 0,
	    directionNav: false,
	  });
	});
</script>
<?php $html_content = ob_get_clean(); ?>
<?php echo $html_content; ?>
<!-- Html -->

<!-- Code -->
<?php ob_start(); ?>
<link rel="stylesheet" type="text/css" href="http://images.guitarcenter.com/Content/GC/libs/css/gcgrid-desktop_v6.css" />
<link rel="stylesheet" type="text/css" href="http://images.guitarcenter.com/Content/GC/libs/flexslider/css/flexslider.css" />
<style type="text/css">
	#images.flexslider {border:0;border-radius:0;}
	.flex-control-paging li a.fl2ex-active {background-color:#c00;}
	.flexslider {box-shadow:none;}
</style>
<div id="content-wrapper">
	<section class="slider">
		<!-- Slides -->
	    <div id="images" class="flexslider">
	      <ul class="slides">
			<?php foreach($sliders as $slider): ?>
		    	<li>
		    		<a href="<?php echo $slider['url']; ?>">
		    			<img src="<?php echo $slider['imageUrl'] ?>" alt="<?php echo $slider['title'] ?>" />
		    		</a>
				</li>
			<?php endforeach; ?>
		  </ul>
		</div>
		<!-- Slides -->

		<!-- Subheroes -->
		<?php $counter=0; $last_subhero_row=ceil(count($subheroes)/2);$subheroes_count=count($subheroes); ?>
		<?php for($rows=0;$rows<$last_subhero_row;$rows++): ?>
			<div class="gcrow">
				<?php for ($cols=0;$cols<3;$cols++): ?>
					<?php if ($counter<$subheroes_count): ?>
						<div class="<?php echo get_offset($subheroes,$rows,$cols); ?>gccol-4-12" style="style="<?php echo get_padding($cols); ?>"">
							<a href="<?php echo $subheroes[$counter]['url']; ?>">
								<img src="<?php echo $subheroes[$counter]['imageUrl'] ?>" alt="<?php echo $subheroes[$counter]['title'] ?>" />
							</a>
						</div>
					<?php endif; ?>
					<?php $counter++; ?>
				<?php endfor; ?>
			</div>
		<?php endfor; ?>
		<!-- Subheroes -->
	</section>
</div>
<script src="http://images.guitarcenter.com/Content/GC/libs/js/jquery.flexslider-min.js"></script>
<script type="text/javascript">
	$(window).load(function() {
	  $('#images').flexslider({
	    animation: "slide",
	    slideshow: true,
	    slideshowSpeed: 4000,
	    itemMargin: 0,
	    directionNav: false,
	  });
	});
</script>

<?php $code = clean_code(ob_get_clean()); ?>

<div style="text-align:center">
	<h3>Get Code</h3>
	<p>Click the textarea and press Ctrl+C on your keyboard to copy.</p>
	<textarea cols="150" rows="20" onclick="this.focus();this.select()">
		<?php echo $code; ?>
	</textarea>
</div>

<?php create_file($_GET['category'],$html_content); ?>


<?php else: ?>

	<div id="example1">
	</div>
	<script type="text/javascript">
		var $ = function(id) {
		      return document.getElementById(id);
		  },
		  container = $('example1'),
		  exampleConsole = $('example1console'),
		  autosave = $('autosave'),
		  load = $('load'),
		  save = $('save'),
		  autosaveNotification,
		  hot;

		hot = new Handsontable(container, {
		  startRows: 8,
		  startCols: 5,
		  colHeaders: ["category","type","imageUrl","url","status"],
		  colWidths: [200,200,200,200,200],
		  minSpareRows: 1,
		  contextMenu: true,
		  afterChange: function (change, source) {
		    if (source === 'loadData') {
		      return; //don't save this change
		    }
		    if (!autosave.checked) {
		      return;
		    }
		    clearTimeout(autosaveNotification);
		    ajax('json/save.json', 'GET', JSON.stringify({data: change}), function (data) {
		      exampleConsole.innerText  = 'Autosaved (' + change.length + ' ' + 'cell' + (change.length > 1 ? 's' : '') + ')';
		      autosaveNotification = setTimeout(function() {
		        exampleConsole.innerText ='Changes will be autosaved';
		      }, 1000);
		    });
		  }
		});

		Handsontable.Dom.addEvent(load, 'click', function() {
		  ajax('json/load.json', 'GET', '', function(res) {
		    var data = JSON.parse(res.response);
		  	console.log(res.response);
		    hot.loadData(data.data);
		    exampleConsole.innerText = 'Data loaded';
		  });
		});

		Handsontable.Dom.addEvent(save, 'click', function() {
		  // save all cell's data
		  ajax('json/save.json', 'GET', JSON.stringify({data: hot.getData()}), function (res) {
		    var response = JSON.parse(res.response);

		    if (response.result === 'ok') {
		      exampleConsole.innerText = 'Data saved';
		    }
		    else {
		      exampleConsole.innerText = 'Save error';
		    }
		  });
		});

		Handsontable.Dom.addEvent(autosave, 'click', function() {
		  if (autosave.checked) {
		    exampleConsole.innerText = 'Changes will be autosaved';
		  }
		  else {
		    exampleConsole.innerText ='Changes will not be autosaved';
		  }
		});
	</script>
<?php endif; ?>

<?php if($_POST) {

	$data = $_POST['data'];
	echo $data;

} ?>






