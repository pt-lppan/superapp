<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<div class="element-box-content">
					<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
					
					<? if (strlen($strInfo)>0) { echo $umum->messageBox("info","<ul>".$strInfo."</ul>"); } ?>
					
					<div class="dd" id="tree">
						<ol class="dd-list" id="dd-placeholder">
						</ol>
					</div>
					
					<form class="mt-4" id="dform" method="post">
						<input type="hidden" name="data" id="data" value=""/>
						<input class="btn btn-primary" type="button" id="sf" name="sf" value="Simpan"/>
						<input class="btn btn-success" type="button" id="ea" name="ea" value="Expand Tree"/>
						<input class="btn btn-success" type="button" id="ca" name="ca" value="Collapse Tree"/>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// setup tree
	var json = '[<?=$data_tree?>]';
	var html = '';
	$.each(JSON.parse(json), function (index, item) {
        html += nestableTree(item,'','');
    });
	$('#dd-placeholder').html(html);
	$('#tree').nestable({ maxDepth:10 });
	$('#tree').nestable('collapseAll');
	
	$('#ea').click(function(){
		$('#tree').nestable('expandAll');
	});
	
	$('#ca').click(function(){
		$('#tree').nestable('collapseAll');
	});
	
	$('#sf').click(function(){
		$('#data').val( JSON.stringify($('.dd').nestable('serialize')) );
		$('#dform').submit();
	});
});
</script>