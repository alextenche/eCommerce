<?php
$url = '/ecommerce/admin'.Url::getCurrentUrl(array('action', 'id'));
require_once('template/_header.php');
?>
<h1>Categories :: Edit</h1>
<p>The record has been updatted successfully.<br />
<a href="<?php echo $url; ?>">Go back to the list of categories.</a></p>
<?php require_once('template/_footer.php'); ?>